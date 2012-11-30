<?php defined('_JEXEC') or die;

/**
 * File       helper.php
 * Created    11/29/12 11:13 PM
 * Author     Matt Thomas matt@betweenbrain.com
 * Copyright  Copyright (C) 2012 betweenbrain llc.
 * License    GNU GPL v3 or later
 */

class modYatmHelper {

    function searchTwitter($url = NULL, $array = TRUE) {
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
        // true for associative array
        $results = json_decode($json, $array);

        return $results;
    }

    function linkEntities($result) {

        // Link hashtags
        foreach ($result['entities']['hashtags'] as $hashtag) {
            $url            = 'http://search.twitter.com/search?q=';
            $obj            = $hashtag['text'];
            $replacement    = '<a href="' . $url . $obj . '" >#' . $obj . '</a>';
            $result['text'] = preg_replace("/#$obj/i", $replacement, $result['text']);
        }

        // Link mentions
        foreach ($result['entities']['user_mentions'] as $mention) {
            $url            = 'http://twitter.com/';
            $obj            = $mention['screen_name'];
            $replacement    = '<a href="' . $url . $obj . '" >@' . $obj . '</a>';
            $result['text'] = preg_replace("/@$obj/i", $replacement, $result['text']);
        }

        // Link URLs
        foreach ($result['entities']['urls'] as $urls) {
            $url            = NULL;
            $obj            = str_replace(array('/'), array('\/'), $urls['url']);
            $replacement    = '<a href="' . $url . $obj . '" >' . $urls['url'] . '</a>';
            $result['text'] = preg_replace("/$obj/i", $replacement, $result['text']);
        }

        return $result;
    }

    function filterTweet($result) {
        // Clear the bad flag
        $badflag = NULL;

        // Check Tweet text for bad words
        $badwords = array('TopTag', 'Fundraising');
        foreach ($badwords as $badword) {
            if (preg_match("/\b$badword\b/i", $result['text'])) {
                $badflag = TRUE;
            }
        }

        // Check Tweetee for being banned or not
        $bannedusers = array('betweenbrain', 'suportejoomlabr');
        foreach ($bannedusers as $banneduser) {
            if (preg_match("/\b$banneduser\b/i", $result['from_user'])) {
                $badflag = TRUE;
            }
        }

        return $badflag;
    }

}

