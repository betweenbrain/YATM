<?php defined('_JEXEC') or die;

/**
 * File       helper.php
 * Created    11/29/12 11:13 PM
 * Author     Matt Thomas matt@betweenbrain.com
 * Copyright  Copyright (C) 2012 betweenbrain llc.
 * License    GNU GPL v3 or later
 */

class modYatmHelper {

    /**
     * Module parameters
     * @var    boolean
     * @since  0.2
     */
    protected $params;

    /**
     * Flag to determine whether data is cached or to load fresh
     * @var    boolean
     * @since  0.2
     */
    public $isRawCache = FALSE;

    /**
     * Flag to determine whether filtered Tweet cache exists
     * @var    boolean
     * @since  0.2
     */
    public $isCleanCache = FALSE;

    /**
     * Container for the formatted module data
     *
     * @var    array
     * @since  0.2
     */
    public $tweets = array();

    /**
     * Constructor
     *
     * @param   JRegistry  $params  The module parameters
     *
     * @since  0.2
     */
    public function __construct($params) {
        // Store the module params
        $this->params = $params;
    }

    /**
     * Function to search Twitter
     *
     * @return json
     * @since  0.2
     */
    function searchTwitter() {

        // Get parameters from the module's configuration
        $term = htmlspecialchars($this->params->get('term'));
        $type = $this->params->get('type');
        $rpp  = htmlspecialchars($this->params->get('rpp'));

        // Build the search URL
        $url = 'http://search.twitter.com/search.json?q=';
        $url .= '%23' . $term;
        $url .= '&result_type=' . $type;
        $url .= '&include_entities=1';
        $url .= '&rpp=' . $rpp;

        $curl = curl_init();

        curl_setopt_array($curl, Array(
            CURLOPT_USERAGENT      => "YetAnotherTwitterModule",
            CURLOPT_URL            => $url,
            CURLOPT_TIMEOUT        => 300,
            CURLOPT_CONNECTTIMEOUT => 60,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_ENCODING       => 'UTF-8'
        ));

        $json = curl_exec($curl);

        return $json;

    }

    /**
     * Function to compile Tweets for rendering
     *
     * @param $json
     * @return array
     * @since  0.2
     */
    function compileTweets($json) {
        // Decode json input
        $tweets = json_decode($json);
        // Process Tweets
        foreach ($tweets->results as $result) {
            // Flag out bad Tweets
            $banned = $this->filterTweet($result);
            // Load only good tweets
            if (!$banned) {
                // Link Tweet entities
                $result = $this->linkEntities($result);
                // Build final Tweet array
                $tweet[] = array('from_user' => $result->from_user, 'profile_image_url' => $result->profile_image_url, 'from_user_name' => $result->from_user_name, 'text' => $result->text, 'id' => $result->id, 'created_at' => $result->created_at);
            }
        }

        // Return array of compiled Tweets
        return $tweet;

    }

    /**
     * Function to link Tweet entities in Tweet text (hashtags, mentions, and URLs)
     *
     * @param $result
     * @return array
     * @since  0.2
     */
    protected function linkEntities($result) {

        // Link hashtags
        foreach ($result->entities->hashtags as $hashtag) {
            $url          = 'http://search.twitter.com/search?q=';
            $obj          = $hashtag->text;
            $replacement  = '<a href="' . $url . $obj . '" >#' . $obj . '</a>';
            $result->text = preg_replace("|#$obj|i", $replacement, $result->text);
        }

        // Link mentions
        foreach ($result->entities->user_mentions as $mention) {
            $url          = 'http://twitter.com/';
            $obj          = $mention->screen_name;
            $replacement  = '<a href="' . $url . $obj . '" >@' . $obj . '</a>';
            $result->text = preg_replace("|@$obj|i", $replacement, $result->text);
        }

        // Link URLs
        foreach ($result->entities->urls as $urls) {
            $url          = NULL;
            $obj          = $urls->url;
            $replacement  = '<a href="' . $url . $obj . '" >' . $urls->url . '</a>';
            $result->text = preg_replace("|$obj|i", $replacement, $result->text);
        }

        return $result;
    }

    /**
     * Function to check Tweet for text containing banned words or by a banned Tweeter
     *
     * @param $result
     * @return var
     * @since  0.2
     */
    protected function filterTweet($result) {
        // Clear the bad flag
        $bannedflag = NULL;
        // Retrieve the banned word list
        $bannedwords = htmlspecialchars($this->params->get('bannedwords'));
        // Check the banned word list for the presence of any banned words
        if ($bannedwords) {
            // Remove spaces around commas, make it an array
            $bannedwords = explode(',', (str_replace(array(', ', ' , ', ' ,'), ',', $bannedwords)));
            // Check Tweet text for bad words
            foreach ($bannedwords as $bannedword) {
                if (preg_match("/\b$bannedword\b/i", $result->text)) {
                    $bannedflag = TRUE;
                }
            }
        }

        // Retrieve the banned Tweeter list
        $bannedusers = htmlspecialchars($this->params->get('bannedusers'));
        // Check the banned Tweeter list for the presence of any banned Tweeters
        if ($bannedusers) {
            // Remove spaces around commas, make it an array
            $bannedusers = explode(',', (str_replace(array(', ', ' , ', ' ,'), ',', $bannedusers)));
            // Check Tweet to see if it is from a banned Tweeter
            foreach ($bannedusers as $banneduser) {
                if (preg_match("/\b$banneduser\b/i", $result->from_user)) {
                    $bannedflag = TRUE;
                }
            }
        }

        return $bannedflag;
    }

    /**
     * Function to fetch valid Tweets
     * @since  0.2
     */
    function fetchTweets() {
        if ($this->params->get('cache') && $this->validateCache('clean')) {
            $json   = file_get_contents(JPATH_CACHE . '/mod_yatm/clean_tweets.json');
            $tweets = json_decode($json, TRUE);
        } elseif ($this->params->get('cache') && $this->validateCache('raw')) {
            $json   = file_get_contents(JPATH_CACHE . '/mod_yatm/raw_tweets.json');
            $tweets = $this->compileTweets($json);
            $this->compileCache(json_encode($tweets), 'clean');
        } elseif ($this->params->get('cache') && !$this->validateCache('raw')) {
            $json   = $this->searchTwitter();
            $tweets = $this->compileTweets($json);
            $this->compileCache($json);
        } else {
            $json   = $this->searchTwitter();
            $tweets = $this->compileTweets($json);
            $this->validateCache('clean');
            $this->validateCache('raw');
        }

        return $tweets;
    }

    /**
     * Function to compile cache file
     *
     * @since  0.2
     */
    protected function compileCache($json, $type = "raw") {
        file_put_contents(JPATH_CACHE . '/mod_yatm/' . $type . '_tweets.json', $json);
    }

    /**
     * Function to validate cache file
     * @params $type
     * @since  0.2
     */
    function validateCache($type) {
        $cache = JPATH_CACHE . '/mod_yatm/' . $type . '_tweets.json';
        if ($this->params->get('cache')) {
            // Define which cache we are validating
            if (file_exists($cache)) {
                // Convert user input max cache age to minutes
                $cacheTime = ($this->params->get('cachetime', 15)) * 60;
                // Get age of cache file
                $cacheAge = filemtime($cache);
                // Check if cache has expired
                if ((time() - $cacheAge) >= $cacheTime) {
                    // If it's stale, delete it an set flag
                    unlink($cache);

                    return FALSE;
                }

                return TRUE;
            }
            // Remove any old cache if caching is truned off
        } elseif (!$this->params->get('cache') && file_exists($cache)) {
            unlink($cache);
        }

    }

    /*
        function checkCacheQuantity() {
            // Retrieve number of required good Tweets for filtered cache
            $cachemin = $this->params->get('cachemin', 4);
            // Check for suffecient good Tweets to caching
            if ($tweet[$cachemin]) {
                // Toggle filtered cache flag
                $this->filteredCache = TRUE;
                // Write filtered Tweet cache
                file_put_contents($filteredCache, json_encode($tweet));
            }
        }

    */

}

