<?php

/**
 * File       index.php
 * Created    11/28/12 6:44 PM
 * Author     Matt Thomas matt@betweenbrain.com
 * Copyright  Copyright (C) 2012 betweenbrain llc.
 * License    GNU GPL v3 or later
 */

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

?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
</head>
<body>
<?php

$url = 'http://search.twitter.com/search.json?q=';
$url .= '%23joomla';
$url .= '&result_type=mixed';
$url .= '&include_entities=1';
$url .= '&rpp=100';

$results = searchTwitter($url);

foreach ($results['results'] as $result) {

    /*
    echo '<pre>';
    var_dump($result);
    echo '</pre>';
    */

    $badflag = filterTweet($result);

    // If all is happy in the land of Oz, proceed.
    if (!$badflag) {

        $result = linkEntities($result);

        // Render each Tweet
        ?>
	<div style="border: 1px solid #999; margin: 25px;">
		<img src="<?php echo $result['profile_image_url'] ?>" />
		<a href="https://twitter.com/<?php echo $result['from_user'] ?>"><?php echo $result['from_user_name'] ?></a>
		<p>@<?php echo $result['from_user'] ?></p>
		<p><?php echo $result['text'] ?></p>
		<p><a href="https://twitter.com/<?php echo $result['from_user'].'/status/'.$result['id']?>"><?php echo  substr($result['created_at'], 4, 7); ?></a></p>
		<a href="https://twitter.com/intent/tweet?in_reply_to=<?php echo $result['id'] ?>">Reply</a><br />
		<a href="https://twitter.com/intent/retweet?tweet_id=<?php echo $result['id'] ?>&via=betweenbrain">Retweet</a><br />
		<a href="https://twitter.com/intent/favorite?tweet_id=<?php echo $result['id'] ?>">Favorite</a><br />
	</div>
        <?php
    }
}
?>
</body>
</html>

