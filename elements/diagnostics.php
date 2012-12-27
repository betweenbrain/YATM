<?php defined('_JEXEC') or die;

/**
 * File       diagnostics.php
 * Created    12/21/12 3:21 PM
 * Author     Matt Thomas
 * Website    http://betweenbrain.com
 * Email      matt@betweenbrain.com
 * Support    https://github.com/betweenbrain/
 * Copyright  Copyright (C) 2012 betweenbrain llc. All Rights Reserved.
 * License    GNU GPL v3 or later
 */

class JElementDiagnostics extends JElement {

    var $_name = 'Googlewebfont';

    function fetchElement() {

        // Fetch parameters via database query
        $db  = JFactory::getDBO();
        $sql = "SELECT params
        FROM #__modules
        WHERE module = \"mod_yatm\"";
        $db->setQuery($sql);
        $params = $db->loadResult();

        // Get
        $showdiagnostics = strpos($params, 'showdiagnostics=1');

        $cacheon   = strpos($params, 'cache=1');
        $cachetime = preg_match("/cachetime=[(0-9)*]/", $params);

        $cachedir   = JPATH_CACHE . '/mod_yatm/';
        $cachedir   = preg_replace("/administrator\//", '', $cachedir);
        $bakcache   = $cachedir . 'clean_bak_tweets.json';
        $cleancache = $cachedir . 'clean_tweets.json';
        $rawcache   = $cachedir . 'raw_tweets.json';

        $result   = NULL;
        $messages = NULL;
        $errors   = NULL;

        if ($showdiagnostics) {

            //return var_dump($params);

            // Check cache stuff
            if ($cacheon) {

                $messages[] = "Caching is enabled.";

                $messages[] = "Cache lifetime is $cachetime minute(s).<br/>";

                if (is_dir($cachedir)) {
                    $messages[] = "Cache dirtectory exists at $cachedir";
                } else {
                    $errors[] = "The cache diretory at $cachedir does not exist!";
                }

                if (file_exists($bakcache)) {
                    $messages[] = "Backup cache file exists at $bakcache";
                } else {
                    $errors[] = "The backup cache file at $bakcache does not exist!";
                }

                if (file_exists($cleancache)) {
                    $messages[] = "Clean cache file exists at $cleancache";
                } else {
                    $errors[] = "The clean cache file at $cleancache does not exist!";
                }

                if (file_exists($rawcache)) {
                    $messages[] = "Raw cache file exists at $rawcache";
                } else {
                    $errors[] = "The raw cache file at $rawcache does not exist!";
                }

                if ($messages[0]) {
                    $result .= '<dl id="system-message"><dt>Information</dt><dd class="message fade"><ul>';
                    foreach ($messages as $message) {
                        $result .= '<li>' . $message . '</li>';
                    }
                    $result .= '</ul></dd></dl>';
                }

                if ($errors[0]) {
                    $result .= '<dl id="system-message"><dt>Errors</dt><dd class="error message fade"><ul>';
                    foreach ($errors as $error) {
                        $result .= '<li>' . $error . '</li>';
                    }
                    $result .= '</ul></dd></dl>';
                }

                return print_r($result, FALSE);

            }
        }
    }
}