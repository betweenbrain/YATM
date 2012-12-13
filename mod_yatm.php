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
// Application object
$app = JFactory::getApplication();
// Global document object
$doc = JFactory::getDocument();
// Instantiate our class
$yolanda = new modYatmHelper($params);
// Fetch the Tweets
$tweets = $yolanda->fetchTweets();
// Fallback message
$fallback = $params->get('fallback');
// @via parameter
$via = htmlspecialchars($params->get('via'));
// Render output
require JModuleHelper::getLayoutPath('mod_yatm');

// Load JS
if ($params->get('loadjs')) {
    $doc->addScript('http://platform.twitter.com/widgets.js');
    if ($params->get('loadjquery')) {
        $doc->addScript('http://code.jquery.com/jquery.min.js');
    }
    $doc->addScript(JURI::base(TRUE) . '/modules/mod_yatm/tmpl/js/jquery.carousel.min.js');
    $js = '(function ($) {
            $().ready(function () {
                $("div.yatm").carousel({
                    dispItems        :' . htmlspecialchars($params->get('dispItems')) . ',
                    loop             :' . ($params->get('loop') ? 'true' : 'false') . ',
                    autoSlide        :' . ($params->get('autoSlide') ? 'true' : 'false') . ',
                    autoSlideInterval:' . htmlspecialchars($params->get('autoSlideInterval')) . ',
                    nextBtn          : "<a class=\"next\" title=\"Next\">Next</a>",
                    prevBtn          : "<a class=\"prev\" title=\"Previous\">Previous</a>",
                });
            });
        })(jQuery)';
    // Nicefy JS
    $js = preg_replace(array('/\s{2,}+/', '/\t/', '/\n/'), '', $js);
    $doc->addScriptDeclaration($js);
}

// Load CSS
if ($params->get('loadcss')) {
    // Check for template override of CSS
    if (file_exists(JPATH_SITE . '/templates/' . $app->getTemplate() . '/css/mod_yatm/yatm.css')) {
        $doc->addStyleSheet(JURI::base(TRUE) . '/templates/' . $app->getTemplate() . '/css/mod_yatm/yatm.css');
    } else {
        $doc->addStyleSheet(JURI::base(TRUE) . '/modules/mod_yatm/tmpl/css/yatm.css');
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