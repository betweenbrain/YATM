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
    public $isCached = FALSE;

    /**
     * Flag to determine whether filtered Tweet cache exists
     * @var    boolean
     * @since  0.2
     */
    public $filteredCache = FALSE;

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

    function searchTwitter() {

        // get parameters from the module's configuration
        $term = htmlspecialchars($this->params->get('term'));
        $type = $this->params->get('type');
        $rpp  = htmlspecialchars($this->params->get('rpp'));

        // build the search URL
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

        $this->compileRawCache($json);

    }

    function linkEntities($result) {

        // Link hashtags
        foreach ($result->entities->hashtags as $hashtag) {
            $url          = 'http://search.twitter.com/search?q=';
            $obj          = $hashtag->text;
            $replacement  = '<a href="' . $url . $obj . '" >#' . $obj . '</a>';
            $result->text = preg_replace("/#$obj/i", $replacement, $result->text);
        }

        // Link mentions
        foreach ($result->entities->user_mentions as $mention) {
            $url          = 'http://twitter.com/';
            $obj          = $mention->screen_name;
            $replacement  = '<a href="' . $url . $obj . '" >@' . $obj . '</a>';
            $result->text = preg_replace("/@$obj/i", $replacement, $result->text);
        }

        // Link URLs
        foreach ($result->entities->urls as $urls) {
            $url          = NULL;
            $obj          = str_replace(array('/'), array('\/'), $urls->url);
            $replacement  = '<a href="' . $url . $obj . '" >' . $urls->url . '</a>';
            $result->text = preg_replace("/$obj/i", $replacement, $result->text);
        }

        return $result;
    }

    function filterTweet($result) {
        // Clear the bad flag
        $bannedflag = NULL;

        // retrieve the parameter
        $bannedwords = htmlspecialchars($this->params->get('bannedwords'));
        // check for any input
        if ($bannedwords) {
            // remove spaces around commas, make it an array
            $bannedwords = explode(',', (str_replace(array(', ', ' , ', ' ,'), ',', $bannedwords)));
            // Check Tweet text for bad words
            foreach ($bannedwords as $bannedword) {
                if (preg_match("/\b$bannedword\b/i", $result->text)) {
                    $bannedflag = TRUE;
                }
            }
        }

        $bannedusers = htmlspecialchars($this->params->get('bannedusers'));
        if ($bannedusers) {
            $bannedusers = explode(',', (str_replace(array(', ', ' , ', ' ,'), ',', $bannedusers)));
            foreach ($bannedusers as $banneduser) {
                if (preg_match("/\b$banneduser\b/i", $result->from_user)) {
                    $bannedflag = TRUE;
                }
            }
        }

        return $bannedflag;
    }

    function fetchTweets() {
        if ($this->params->get('cache') == 1) {
            return $this->checkCache();
        } else {
            return $this->searchTwitter();
        }
    }

    function filterRawCache() {
        // if ($this->isCached === 1) {
        $json    = file_get_contents(JPATH_CACHE . '/mod_yatm/raw_tweets.json');
        $results = json_decode($json);
        /*
         } else {
              $this->searchTwitter();
          }
        */
        // Retrieve number of required good Tweets for filtered cache
        $cachemin = $this->params->get('cachemin', 4);
        // Define filtered cache file
        $filteredCache = JPATH_CACHE . '/mod_yatm/filtered_tweets.json';
        foreach ($results->results as $index => $result) {
            // Flag out bad Tweets
            $banned = $this->filterTweet($result);
            // Load only good tweets
            if (!$banned) {
                // Link Tweet entities
                $result = $this->linkEntities($result);
                // Build filtered Tweet array
                $tweet[] = array('from_user' => $result->from_user, 'profile_image_url' => $result->profile_image_url, 'from_user_name' => $result->from_user_name, 'text' => $result->text, 'id' => $result->id, 'created_at' => $result->created_at);
            }
        }
        // Check for suffecient good Tweets to caching
        if ($tweet[$cachemin]) {
            // Toggle filtered cache flag
            $this->filteredCache = TRUE;
            // Write filtered Tweet cache
            file_put_contents($filteredCache, json_encode($tweet));
        }
    }

    function checkCache() {
        // Cache file
        $cache = JPATH_CACHE . '/mod_yatm/filtered_tweets.json';

        $this->checkCacheAge($cache);

        // Check if the cache file exist
        if (file_exists($cache)) {
            $this->isCached = TRUE;
            $tweets         = file_get_contents($cache);

            return json_decode($tweets);

        } else {
            $this->filterRawCache();
        }
    }

    function checkCacheAge($cache) {
        if (file_exists($cache)) {
            // Convert user input max cache age to minutes
            $cacheTime = ($this->params->get('cachetime', 15)) * 60;
            // Get age of cache file
            $cacheAge = filemtime($cache);
            // Check if cache has expired
            if ((time() - $cacheAge) >= $cacheTime) {
                // If it's stale, delete it an set flag
                unlink($cache);
                $this->isCached = FALSE;
            }
        }
    }

    function compileRawCache($json) {
        if (!$this->isCached) {
            file_put_contents(JPATH_CACHE . '/mod_yatm/raw_tweets.json', $json);
            $this->isCached = TRUE;
        }
    }

}

