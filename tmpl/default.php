<?php defined('_JEXEC') or die;
/**
 * File       default.php
 * Created    11/29/12 11:13 PM
 * Author     Matt Thomas matt@betweenbrain.com
 * Copyright  Copyright (C) 2012 betweenbrain llc.
 * License    GNU GPL v3 or later
 */
?>
<div class="yatm<?php echo $params->get('moduleclass_sfx'); ?>">
	<ul>
        <?php foreach ($results->results as $result) {
        // Filter unwanted Tweets
        $badflag = $tweet->filterTweet($result, $params);
        // Load only non-filtered tweets
        if (!$badflag) {
            // Link Tweet entities
            $tweet->linkEntities($result);
            ?>
			<li class="tweet">
				<div class="user">
					<a class="profile-image" href="https://twitter.com/<?php echo $result->from_user ?>">
						<img class="profile-image" src="<?php echo $result->profile_image_url ?>" />
					</a>
					<a class="from-user" href="https://twitter.com/<?php echo $result->from_user ?>">
                        <?php echo $result->from_user_name ?>
					</a>
					<p class="at-user">
						@<?php echo $result->from_user ?>
					</p>
				</div>
				<p class="text">
                    <?php echo $result->text ?>
				</p>
				<a class="status" href="https://twitter.com/<?php echo $result->from_user . '/status/' . $result->id ?>"><?php echo  substr($result->created_at, 4, 7); ?></a>

				<ul class="actions">
					<li>
						<a class="reply" href="https://twitter.com/intent/tweet?in_reply_to=<?php echo $result->id ?>">
							Reply
						</a>
					</li>
					<li>
						<a class="retweet" href="https://twitter.com/intent/retweet?tweet_id=<?php echo $result->id ?><?php if ($via) { echo '&via=' . $via; } ?>">
							Retweet
						</a>
					</li>
					<li>
						<a class="favorite" href="https://twitter.com/intent/favorite?tweet_id=<?php echo $result->id ?>">
							Favorite
						</a>
					</li>
				</ul>
			</li>
            <?php
        }
    }
        ?>
	</ul>
</div>
