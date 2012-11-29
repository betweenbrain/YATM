<?php

/**
 * File       index.php
 * Created    11/28/12 6:44 PM
 * Author     Matt Thomas matt@betweenbrain.com
 * Copyright  Copyright (C) 2012 betweenbrain llc.
 * License    GNU GPL v3 or later
 */

$url    = 'http://search.twitter.com/search.json?q=%23constructfw&result_type=mixed';
$json   = @file_get_contents($url);
$result = json_decode($json, true);

// print_r($json);

foreach ($result['results'] as $data) {
    foreach ($data as $key => $value) {
        //echo $key . ': ' . $value . '<br/>';
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

