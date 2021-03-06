<?php defined('_JEXEC') or die;

/**
 * File       default.php
 * Created    11/29/12 11:13 PM
 * Author     Matt Thomas matt@betweenbrain.com
 * Copyright  Copyright (C) 2012 The Solomon R. Guggenheim Foundation. All Rights Reserved.
 */

// Check for minimum Tweets
if (!$mintweets) {
    echo $fallback;
} else {
    if ($showterm) : ?>
    <h3 class="yatm">
        <a<?php if ($anchorclass) {
            echo ' class="' . $anchorclass . '"';
        } ?> href="http://twitter.com/search?q=<?php echo $term ?>">#<?php echo $term ?></a>
    </h3>
    <?php endif ?>
	<div data-looper="go" id="looper<?php echo $module->id; ?>" data-interval="false" class="looper side slide yatm<?php echo $moduleclass_sfx ?>">
		<div class="nav">
			<a data-looper="prev" class="prev" href="#looper<?php echo $module->id; ?>">Previous</a>
			<a data-looper="next" class="next" href="#looper<?php echo $module->id; ?>">Next</a>
		</div>
		<ul class="looper-inner">
            <?php foreach ($tweets as $i => $tweet) : ?>
	            <?php echo $yatm->loopStart($i) ?>
            <li class="tweet">
                <div class="user">
                    <a class="profile-image<?php echo $anchorclass ?>" href="https://twitter.com/<?php echo $tweet['from_user'] ?>">
                        <img class="profile-image" src="<?php echo $tweet['profile_image_url'] ?>" />
                    </a>
                    <a class="from-user<?php echo $anchorclass ?>" href="https://twitter.com/<?php echo $tweet['from_user'] ?>">
                        <?php echo $tweet['from_user_name'] ?>
                    </a>
                    <p class="at-user<?php echo $anchorclass ?>">
                        @<?php echo $tweet['from_user'] ?>
                    </p>
                </div>
                <p class="text">
                    <?php echo $tweet['text'] ?>
                </p>
                <a class="status<?php echo $anchorclass ?>" href="https://twitter.com/<?php echo $tweet['from_user'] . '/status/' . $tweet['id'] ?>"><?php echo substr($tweet['created_at'], 4, 7); ?></a>
                <ul class="actions">
                    <li>
                        <a class="reply" href="https://twitter.com/intent/tweet?in_reply_to=<?php echo $tweet['id'] ?>">
                            Reply
                        </a>
                    </li>
                    <li>
                        <a class="retweet" href="https://twitter.com/intent/retweet?tweet_id=<?php echo $tweet['id'] ?><?php if ($via) echo '&via=' . $via; ?>">Retweet
                        </a>
                    </li>
                    <li>
                        <a class="favorite" href="https://twitter.com/intent/favorite?tweet_id=<?php echo $tweet['id'] ?>">
                            Favorite
                        </a>
                    </li>
                </ul>
            </li>
	        <?php echo $yatm->loopEnd($i, $last) ?>
            <?php endforeach; ?>
        </ul>
    </div>
<?php } ?>
