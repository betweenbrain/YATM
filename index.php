<!DOCTYPE html>
<html>
<head>
    <title></title>
    <script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
</head>
<body>

<?php

    /**
     * File       index.php
     * Created    11/28/12 6:44 PM
     * Author     Matt Thomas matt@betweenbrain.com
     * Copyright  Copyright (C) 2012 betweenbrain llc.
     * License    GNU GPL v3 or later
     */

    function searchTwitter($url = null, $array = true) {
        $curl = curl_init();

        curl_setopt_array($curl, Array(
            CURLOPT_USERAGENT      => "YetAnotherTwitterModule",
            CURLOPT_URL            => $url,
            CURLOPT_TIMEOUT        => 300,
            CURLOPT_CONNECTTIMEOUT => 60,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING       => 'UTF-8'
        ));

        $json = curl_exec($curl);
        // true for associateive array
        $results = json_decode($json, $array);

        return $results;
    }

    $url = 'http://search.twitter.com/search.json?q=';
    $url .= '%23GuggUBSMAP%20';
    $url .= '&result_type=mixed';
    $url .= '&include_entities=1';

    $results = searchTwitter($url);

    /*
    foreach ($results->results as $result) {
        echo $result->text;
    }
    */

    foreach ($results['results'] as $result) {

        echo '<pre>';
        echo print_r($result['text']);
        echo '</pre>';


        foreach ($result['entities']['hashtags'] as $hashtag) {

            $replacement    = '<a href="http://search.twitter.com/search?q=' . $hashtag['text'] . '">#' . $hashtag['text'] . '</a>';
            $result['text'] = str_replace('#' . $hashtag['text'], $replacement, $result['text']);

            /*
            echo '<pre>';
            echo print_r($hashtag);
            echo '</pre>';

            $start  = $hashtag['indices'][0];
            $length = $hashtag['indices'][1] - $start;
            $result['text'] = substr_replace($result['text'], $replacement, $start, $length);
            */
        }

        foreach ($result['entities']['user_mentions'] as $mention) {
            $replacement    = '<a href="http://twitter.com/' . $mention['screen_name'] . '">' . '@' . $mention['screen_name'] . '</a>';
            $result['text'] = str_replace('@' . $mention['screen_name'], $replacement, $result['text']);

            /*
            echo '<pre>';
            echo print_r($mention);
            echo '</pre>';

            $start       = $mention['indices'][0];
            $length      = $mention['indices'][1] - $start;
            $result['text'] = substr_replace($result['text'], $replacement, $start, $length);
            */
        }

        foreach ($result['entities']['urls'] as $urls) {

            $replacement    = '<a href="' . $urls['url'] . '">' . $urls['url'] . '</a>';
            $result['text'] = str_replace($urls['url'], $replacement, $result['text']);

            /*
            echo '<pre>';
            echo print_r($mention);
            echo '</pre>';

            $start          = $urls['indices'][0];
            $length         = $urls['indices'][1] - $start;
            $result['text'] = substr_replace($result['text'], $replacement, $start, $length);
            */
        }

        foreach ($result as $key => $value) {
            $tweet[$key] = $value;
        }

        ?>

    <div style="border: 1px solid #999; margin: 25px;">
        <img src="<?php echo $tweet['profile_image_url'] ?>" />
        <a href="https://twitter.com/<?php echo $tweet['from_user'] ?>"><?php echo $tweet['from_user_name'] ?></a>
        <p>@<?php echo $tweet['from_user'] ?></p>
        <p><?php echo $tweet['text'] ?></p>
        <p><?php echo  substr($tweet['created_at'], 4, 7); ?></p>
        <a href="https://twitter.com/intent/tweet?in_reply_to=<?php echo $tweet['id'] ?>">Reply</a><br />
        <a href="https://twitter.com/intent/retweet?tweet_id=<?php echo $tweet['id'] ?>&via=betweenbrain">Retweet</a><br />
        <a href="https://twitter.com/intent/favorite?tweet_id=<?php echo $tweet['id'] ?>">Favorite</a><br />
    </div>

        <?php
    }
?>

</body>
</html>

