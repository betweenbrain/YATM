# YATM

Yet Another Twitter Module (YATM) is unlike any other Joomla! Twitter module. It provides a filtered display of Twitter search results based on a particular search query, with a number of configurable parameters to further tailor the display.

## Module Parameters

* Search Term: The term that you are searching Twitter for.
* Retweet Via: The Twitter user to retweet via.
* Minimum Results: The minimum number of filtered Tweets required to display a result.
* Maximum Results: Determines the maximum number of results requested from Twitter. The more the merrier, but slower things may happen, and longer the cycle to rotate through all of the Tweets.
* Search Result Type: Per https://dev.twitter.com/docs/api/1/get/search
    * mixed: Include both popular and real time results in the response.
    * recent: return only the most recent results in the response
    * popular: return only the most popular results in the response.
* Banned Words: A comma separated list of words used to trigger filtering out a Tweet.
* Banned Tweeters: A comma separated list of Tweeters to filter the Tweets of.

## Advanced Parameters
### Caching
Caching is a complicated beast with YATM. If caching is enabled, the first time the module is loaded (page load), the raw results are filtered and displayed to the visitor. Then, a cache of the raw results are cached to a file. During the second page load, the raw results cache is used for filtering and display (not the live results) and a cache of the filtered results is created. Then, when the page is loaded a third time, the cache of filtered results is used for display and then checked to see if it meets the minimum number of Tweets for display, and if it does, a backup cache of the filtered cache is created.

The raw and filtered results caches are replaced if:
* They expire per the `Cache Age` parameter
* Caching is disabled

The backup of the filtered cache is replaced the page load following the creation of the filtered cache is created AND if the filtered cache meets the minimum number of Tweets for display. The backup of the filtered cache never expires, it is only overwritten if a conditions are met.

The filtered results cache will always be used first, unless:
* It expires and the raw results cache exists, in which case, the raw results cache is read and filtered for display and caching.
* Neither the raw and filtered results caches exist, in which case, the backup of the filtered cache is used.

In any case, if none of the cache files exist, and Twitter does not return the minimum umber of required Tweets, the `Fallback Message` is displayed and the built-in JavaScript options are disabled automatically.

## Other Parameters
The default JavaScript used by this module is jQuery Carousel from http://www.thomaslanciaux.pro/jquery/jquery_carousel.htm
* See that page for more details
### JavaScript
* Load JavaScript:
   * Yes means yes.
   * No means no and all of the JavaScript parameters are null and void.
* Load jQuery: You'll likely only use this for testing. When enable, it loads the latest minified version of jQuery from their CDN. I'd turn this off, and use a better solution, when you go live.
* Display Items: How many items to display per rotation.
* Loop (yes/no): I bet you can guess.
* Auto Slide: If yes, start sliding the results after the `Auto Slide Interval`

### CSS
* Load CSS... well, if you have to ask...
* Container Width: If you aren't overriding the CSS (see below) this sets the width of the outer container.
* Item Width: The width of each item to display. 
   * Hint - this usually equals Container Width ÷ Display Items
* Button Distance - If you aren't overriding the CSS (seriously, see below) this is the horizontal distance from the container to position the forward and back elements.


---
# Overrides
If the `Load CSS` parameter is set to *yes*, you can easily override the default CSS by creating the stylesheet `/templates/yourtemplate/css/mod_yatm/yatm.css`. Otherwise, if it is set to *no*, you can add styles directly to your template's stylesheets.

---
# Status
Let's just call it Alpha for now. It works, but may change drastically at a moment's notice.

---
# Compatibility
Tested with Joomla! 1.5

---
# Warranties, Culpability, Free Stuff
![You didn't see anything](http://24.media.tumblr.com/tumblr_me7k9knNtH1rlerpuo1_500.gif)




