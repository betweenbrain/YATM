<?php defined('_JEXEC') or die;

/**
 * File       mod_yatm.php
 * Created    11/29/12 11:12 PM
 * Author     Matt Thomas matt@betweenbrain.com
 * Copyright  Copyright (C) 2012 betweenbrain llc.
 * License    GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . DS . 'helper.php');

// get parameters from the module's configuration
$term = htmlspecialchars($params->get('term'));
$rpp  = htmlspecialchars($params->get('rpp'));
$type = $params->get('type');

// build the search URL
$url = 'http://search.twitter.com/search.json?q=';
$url .= '%23' . $term;
$url .= '&result_type=' . $type;
$url .= '&include_entities=1';
$url .= '&rpp=' . $rpp;

// get the items to display from the helper
$results = modYatmHelper::searchTwitter($url);

require(JModuleHelper::getLayoutPath('mod_yatm'));