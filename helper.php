<?php defined('_JEXEC') or die;

/**
 * File       helper.php
 * Created    11/29/12 11:13 PM
 * Author     Matt Thomas matt@betweenbrain.com
 * Copyright  Copyright (C) 2012 betweenbrain llc.
 * License    GNU GPL v3 or later
 */

class modYatmHelper {

    function searchTwitter($params) {

        // get parameters from the module's configuration
        $term = htmlspecialchars($params->get('term'));
        $type = $params->get('type');
        $rpp  = htmlspecialchars($params->get('rpp'));

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

        $results = json_decode($json, TRUE);

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

    function filterTweet($result, $params) {
        // Clear the bad flag
        $bannedflag = NULL;

        // retrieve the parameter
        $bannedwords = htmlspecialchars($params->get('bannedwords'));
        // check for any input
        if ($bannedwords) {
            // remove spaces around commas, make it an array
            $bannedwords = explode(',', (str_replace(array(', ', ' , ', ' ,'), ',', $bannedwords)));
            // Check Tweet text for bad words
            foreach ($bannedwords as $bannedword) {
                if (preg_match("/\b$bannedword\b/i", $result['text'])) {
                    $bannedflag = TRUE;
                }
            }
        }

        $bannedusers = htmlspecialchars($params->get('bannedusers'));
        if ($bannedusers) {
            $bannedusers = explode(',', (str_replace(array(', ', ' , ', ' ,'), ',', $bannedusers)));
            foreach ($bannedusers as $banneduser) {
                if (preg_match("/\b$banneduser\b/i", $result['from_user'])) {
                    $bannedflag = TRUE;
                }
            }
        }

        return $bannedflag;
    }

    function loadJs($params, $doc) {
        if ($params->get('loadjs')) {
            $doc->addScript('http://platform.twitter.com/widgets.js');
            if ($params->get('loadjquery')) {
                $doc->addScript('http://code.jquery.com/jquery.min.js');
            }
            $doc->addScript($this->baseurl . 'modules/mod_yatm/tmpl/js/jquery.carousel.min.js');
            $js = '(function ($) {
                $().ready(function () {
                    $("div.yatm").carousel({
                        dispItems        :' . htmlspecialchars($params->get('dispItems')) . ',
                        loop             :' . ($params->get('loop') ? 'true' : 'false') . ',
                        autoSlide        :' . ($params->get('autoSlide') ? 'true' : 'false') . ',
                        autoSlideInterval:' . htmlspecialchars($params->get('autoSlideInterval')) . '
                    });
                });
            })(jQuery)';
            // Nicefy JS
            $js = preg_replace(array('/\s{2,}+/', '/\t/', '/\n/'), '', $js);
            $doc->addScriptDeclaration($js);
        }
    }

    function loadCss($params, $doc) {
        if ($params->get('loadcss')) {
            $doc->addStyleSheet($this->baseurl . 'modules/mod_yatm/tmpl/css/yatm.css');
            $css = '.yatm {
        	    width   : ' . htmlspecialchars($params->get('containerwidth')) . ';
        	    padding : 0 ' . htmlspecialchars($params->get('buttondistance')) . ';
            }
            .yatm li {
        	    width           : ' . htmlspecialchars($params->get('tweetwidth')) . ';
            }';
            // Nicefy CSS
            $css = preg_replace(array('/\s{2,}+/', '/\t/', '/\n/'), '', $css);
            $doc->addStyleDeclaration($css);
        }
    }

}

