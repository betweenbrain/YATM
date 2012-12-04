<?php defined('_JEXEC') or die;

/**
 * File       default.php
 * Created    11/29/12 11:13 PM
 * Author     Matt Thomas matt@betweenbrain.com
 * Copyright  Copyright (C) 2012 betweenbrain llc.
 * License    GNU GPL v3 or later
 */

//instantiate our class
$tweet = new modYatmHelper();
$via   = $term = htmlspecialchars($params->get('via'));
?>
<div class="yatm<?php echo $params->get('moduleclass_sfx'); ?>">
	<ul>
        <?php foreach ($results->results as $result) {

        // Filter unwanted Tweets
        $badflag = $tweet->filterTweet($result, $params);

        if (!$badflag) {
            // Link Tweet entities
            $tweet->linkEntities($result);
            ?>
			<li>
				<a class="from-user" href="https://twitter.com/<?php echo $result->from_user ?>">
					<img class="profile-image" src="<?php echo $result->profile_image_url ?>" />
				</a>
				<a class="from-user" href="https://twitter.com/<?php echo $result->from_user ?>">
                    <?php echo $result->from_user_name ?>
				</a>
				<p class="at-user">
					@<?php echo $result->from_user ?>
				</p>
				<p class="text">
                    <?php echo $result->text ?>
				</p>
				<p class="status">
					<a href="https://twitter.com/<?php echo $result->from_user . '/status/' . $result->id ?>"><?php echo  substr($result->created_at, 4, 7); ?></a>
				</p>
				<a class="reply" href="https://twitter.com/intent/tweet?in_reply_to=<?php echo $result->id ?>">
					Reply
				</a>
				<a class="retweet" href="https://twitter.com/intent/retweet?tweet_id=<?php echo $result->id ?><?php if ($via) {echo '&via=' . $via;} ?>">
					Retweet
				</a>
				<a class="favorite" href="https://twitter.com/intent/favorite?tweet_id=<?php echo $result->id ?>">
					Favorite
				</a>
			</li>
            <?php
        }
    }
        ?>
	</ul>
</div>
