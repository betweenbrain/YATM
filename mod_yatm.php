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
$type = $params->get('type');
$rpp  = htmlspecialchars($params->get('rpp'));

$doc  = JFactory::getDocument();

// JavaScript
if ($params->get('loadjs')) {
    $doc->addScript('http://platform.twitter.com/widgets.js');
    if ($params->get('loadjquery')) {
        $doc->addScript('http://code.jquery.com/jquery.js');
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

// CSS
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

// build the search URL
$url = 'http://search.twitter.com/search.json?q=';
$url .= '%23' . $term;
$url .= '&result_type=' . $type;
$url .= '&include_entities=1';
$url .= '&rpp=' . $rpp;

// get the items to display from the helper
$results = modYatmHelper::searchTwitter($url);

require(JModuleHelper::getLayoutPath('mod_yatm'));