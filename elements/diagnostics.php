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
		$sql = 'SELECT params
        FROM #__modules
        WHERE module = "mod_yatm"';
		$db->setQuery($sql);
		$params = $db->loadResult();

		// Params are stored as a string, so we need to match condition with strpos.
		$displayDiagnostic = strpos($params, 'showdiagnostics=1');
		$cache             = strpos($params, 'cache=1');

		// JPATH_CACHE is relative to where it is being called from, as we want the site cache, /administrator is removed.
		$cacheDir = JPATH_CACHE . '/mod_yatm/';
		$cacheDir = preg_replace("/administrator\//", '', $cacheDir);
		preg_match("/cachemaxage=([0-9]*)/", $params, $cacheMaxAge);
		$bakcache   = $cacheDir . 'clean_bak_tweets.json';
		$cleancache = $cacheDir . 'clean_tweets.json';
		$rawcache   = $cacheDir . 'raw_tweets.json';

		// Initialize variables
		$result   = NULL;
		$messages = NULL;
		$errors   = NULL;

		if ($displayDiagnostic) {

			// Check cache stuff
			if (!$cache) {
				$messages[] = "Caching is disabled.";
			}

			if ($cache) {

				$messages[] = "Caching is enabled.";

				$messages[] = "Cache lifetime is $cacheMaxAge[1] minute(s).<br/>";

				if (is_dir($cacheDir)) {
					$messages[] = "Cache directory at $cacheDir exists.";
					if (is_writable($cacheDir)) {
						$messages[] = "Cache directory at $cacheDir is writable.";
					}
				} else {
					$errors[] = "Cache directory at $cacheDir does not exist!";
					if (!is_writable($cacheDir)) {
						$errors[] = "Cache directory at $cacheDir is not writable!";
					}
				}

				if (file_exists($bakcache)) {
					$messages[] = "Backup cache file exists at $bakcache";
					$cacheAge   = date("F d Y H:i:s", filemtime($bakcache));
					$messages[] = "Cache file was created $cacheAge.";
				} else {
					$errors[] = "The backup cache file at $bakcache does not exist!";
				}

				if (file_exists($cleancache)) {
					$messages[] = "Clean cache file exists at $cleancache";
					$cacheAge   = date("F d Y H:i:s", filemtime($cleancache));
					$messages[] = "Cache file was created $cacheAge.";
				} else {
					$errors[] = "The clean cache file at $cleancache does not exist!";
				}

				if (file_exists($rawcache)) {
					$messages[] = "Raw cache file exists at $rawcache";
					$cacheAge   = date("F d Y H:i:s", filemtime($rawcache));
					$messages[] = "Cache file was created $cacheAge.";
				} else {
					$errors[] = "The raw cache file at $rawcache does not exist!";
				}
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

			if ($result) {
				return print_r($result, FALSE);
			}

			return FALSE;
		}
	}
}