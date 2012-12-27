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

        $showdiagnostics = strpos($params, 'showdiagnostics=1');

        $cacheon   = strpos($params, 'cache=1');
        $cachetime = preg_match("/cachetime=[(0-9)*]/", $params);

        $cachedir   = JPATH_CACHE . '/mod_yatm/';
        $cachedir   = preg_replace("/administrator\//", '', $cachedir);
        $bakcache   = $cachedir . 'clean_bak_tweets.json';
        $cleancache = $cachedir . 'clean_tweets.json';
        $rawcache   = $cachedir . 'raw_tweets.json';

        if ($showdiagnostics) {

            //return var_dump($params);

            // Check cache stuff
            if ($cacheon) {

                $results[] = "Cache lifetime is $cachetime minute(s).<br/>";

                if (is_dir($cachedir)) {
                    $results[] = "Cache dirtectory exists at $cachedir";
                }

                if (file_exists($bakcache)) {
                    $results[] = "Backup cache file exists at $bakcache";
                }

                if (file_exists($cleancache)) {
                    $results[] = "Clean cache file exists at $cleancache";
                }

                if (file_exists($rawcache)) {
                    $results[] = "Raw cache file exists at $rawcache";
                }

                $message = '<dl id="system-message"><dt>Diagnostic Information</dt><dd class="message fade"><ul>';
                foreach ($results as $result) {
                    $message .= '<li>' . $result . '</li>';
                }
                $message .= '</ul></dd></dl>';

                return print_r($message, FALSE);

            }
        }
    }
}