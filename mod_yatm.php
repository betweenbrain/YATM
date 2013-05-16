<?php defined('_JEXEC') or die;

/**
 * File       mod_yatm.php
 * Created    11/29/12 11:12 PM
 * Author     Matt Thomas matt@betweenbrain.com
 * Copyright  Copyright (C) 2012 The Solomon R. Guggenheim Foundation. All Rights Reserved.
 */

// Get the brains of this opration
require_once(dirname(__FILE__) . DS . 'helper.php');
// Global anchor class
$anchorclass = htmlspecialchars($params->get('anchorclass'));
// Application object
$app = JFactory::getApplication();
// Global document object
$doc = JFactory::getDocument();
// Instantiate our class
$yatm = new modYatmHelper($params);
// Yolanda, please fetch the Tweets
$tweets = $yatm->fetchTweets();
// The fallback message
$fallback = $params->get('fallback');
// Minimum Tweets
$mintweets = isset($tweets[$params->get('mintweets', 4)]);
// Last item flag
$last = count($tweets) - 1;
// Show the search term?
$showterm = $params->get('showterm');
// Twitter search term
$term = htmlspecialchars($params->get('term'));
// @via parameter
$via = htmlspecialchars($params->get('via'));
// Render output
require JModuleHelper::getLayoutPath('mod_yatm');
// Load JS
if ($mintweets) {
	$doc->addScript('http://platform.twitter.com/widgets.js');
}

// Load CSS
if ($params->get('loadcss')) {
	// Check for template override of CSS
	if (file_exists(JPATH_SITE . '/templates/' . $app->getTemplate() . '/css/yatm.css')) {
		$doc->addStyleSheet(JURI::base(TRUE) . '/templates/' . $app->getTemplate() . '/css/yatm.css');
	} else {
		$doc->addStyleSheet(JURI::base(TRUE) . '/modules/mod_yatm/tmpl/css/yatm.css');
		$css = '.yatm {
			width   : ' . htmlspecialchars($params->get('containerwidth')) . ';
			padding : 0 ' . htmlspecialchars($params->get('buttondistance')) . ';
		}
		.yatm li {
			width		   : ' . htmlspecialchars($params->get('tweetwidth')) . ';
		}';
		// Nicefy CSS
		$css = preg_replace(array('/\s{2,}+/', '/\t/', '/\n/'), '', $css);
		$doc->addStyleDeclaration($css);
	}
}

if (file_exists(JPATH_SITE . '/templates/' . $app->getTemplate() . '/css/looper.css')) {
	$doc->addStyleSheet(JURI::base(TRUE) . '/templates/' . $app->getTemplate() . '/css/looper.css');
} elseif (file_exists(JPATH_SITE . '/media/css/looper.css')) {
	$doc->addStyleSheet(JURI::base(TRUE) . '/media/css/looper.css');
}

if (file_exists(JPATH_SITE . '/templates/' . $app->getTemplate() . '/js/looper.js')) {
	$doc->addScript(JURI::base(TRUE) . '/templates/' . $app->getTemplate() . '/js/looper.js');
} elseif (file_exists(JPATH_SITE . '//media/js/looper.js')) {
	$doc->addScript(JURI::base(TRUE) . '//media/js/looper.js');
}
