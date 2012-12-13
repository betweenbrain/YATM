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
    <?php
    // Check for minimum Tweets
    if (!$mintweets) {
        echo $fallback;
    } else { ?>
		<ul>
            <?php foreach ($tweets as $tweet) : ?>
			<li class="tweet">
				<div class="user">
					<a class="profile-image" href="https://twitter.com/<?php echo $tweet['from_user'] ?>">
						<img class="profile-image" src="<?php echo $tweet['profile_image_url'] ?>" />
					</a>
					<a class="from-user" href="https://twitter.com/<?php echo $tweet['from_user'] ?>">
                        <?php echo $tweet['from_user_name'] ?>
					</a>
					<p class="at-user">
						@<?php echo $tweet['from_user'] ?>
					</p>
				</div>
				<p class="text">
                    <?php echo $tweet['text'] ?>
				</p>
				<a class="status" href="https://twitter.com/<?php echo $tweet['from_user'] . '/status/' . $tweet['id'] ?>"><?php echo substr($tweet['created_at'], 4, 7); ?></a>
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
            <?php endforeach; ?>
		</ul>
        <?php } ?>
</div>
