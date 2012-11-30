<?php defined('_JEXEC') or die;

/**
 * File       default.php
 * Created    11/29/12 11:13 PM
 * Author     Matt Thomas matt@betweenbrain.com
 * Copyright  Copyright (C) 2012 betweenbrain llc.
 * License    GNU GPL v3 or later
 */

// Returns a reference to the global document object
$doc = JFactory::getDocument();
$doc->addScript('http://platform.twitter.com/widgets.js');

foreach ($results['results'] as $result) {

    $badflag = modYatmHelper::filterTweet($result);

    if (!$badflag) {

        $result = modYatmHelper::linkEntities($result);
        ?>
	<div style="border: 1px solid #999; margin: 25px;">
		<img src="<?php echo $result['profile_image_url'] ?>" />
		<a href="https://twitter.com/<?php echo $result['from_user'] ?>"><?php echo $result['from_user_name'] ?></a>
		<p>@<?php echo $result['from_user'] ?></p>
		<p><?php echo $result['text'] ?></p>
		<p>
			<a href="https://twitter.com/<?php echo $result['from_user'] . '/status/' . $result['id']?>"><?php echo  substr($result['created_at'], 4, 7); ?></a>
		</p>
		<a href="https://twitter.com/intent/tweet?in_reply_to=<?php echo $result['id'] ?>">Reply</a><br />
		<a href="https://twitter.com/intent/retweet?tweet_id=<?php echo $result['id'] ?>&via=betweenbrain">Retweet</a><br />
		<a href="https://twitter.com/intent/favorite?tweet_id=<?php echo $result['id'] ?>">Favorite</a><br />
	</div>
    <?php
    }
}