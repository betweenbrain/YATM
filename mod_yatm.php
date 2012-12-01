<?php defined('_JEXEC') or die;

/**
 * File       mod_yatm.php
 * Created    11/29/12 11:12 PM
 * Author     Matt Thomas matt@betweenbrain.com
 * Copyright  Copyright (C) 2012 betweenbrain llc.
 * License    GNU GPL v3 or later
 */

// Get the brains of this opration
require_once(dirname(__FILE__) . DS . 'helper.php');
// Reference global document object
$doc = JFactory::getDocument();
// Load JavaScript
modYatmHelper::loadJs($params, $doc);
// Load CSS
modYatmHelper::loadCss($params, $doc);
// Retrieve the search results
$results = modYatmHelper::searchTwitter($params);
// Filter unwanted Tweets
$badflag = modYatmHelper::filterTweet($result, $params);
// We need a body for the brains
require(JModuleHelper::getLayoutPath('mod_yatm'));