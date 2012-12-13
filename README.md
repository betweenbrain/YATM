# YATM

Yet Another Twitter Module (YATM) is unlike any other Joomla! Twitter module. It provides a filtered display of Twitter search results based on a prticular search query, with a number of configurable parameters to further tailor the display.

## Module Parameters

* Search Term: The term that you are searching Twitter for.
* Retweet Via: The Twitter user to retweet via
* Minimum Results: The minimun number of filtered Tweets required to display a result. 
* Maximum Results: Determines the maximum number of results requested from Twitter. The more the merrier, but slower things may happen, and longer the cycle to rotate through all of the Tweets.
* Search Result Type: Per https://dev.twitter.com/docs/api/1/get/search
    * mixed: Include both popular and real time results in the response.
    * recent: return only the most recent results in the response
    * popular: return only the most popular results in the response.
* Banned Words: A comma separated list of words used to triger filtering out a Tweet.
* Banned Tweeters: A comma separated list of Tweeters to filter the Tweets of.

## Advanced Parameters
### Caching
Caching is a complicated beast with YATM. If caching is enabled, the first time the module is loaded (page load), the raw results are filtered and displayed to the visitor. Then, a cache of the raw results are cached to a file. During the second page load, the raw results cache is used for filtering and display (not the live results) and a cache of the filtered results is created. Then, when the page is loaded a third time, the cache of filtered results is used for display and then checked to see if it meets the minimun number of Tweets for diaplay, and if it does, a backup cache of the filtered cache is created.

The raw and filtered results caches are replaced if:
* They expire per the `Cache Age` parameter
* Caching is disabled

The backup of the filtered cache is replaced the page laod following the creation of the filtered cache is created AND if the filtered cache meets the minimum number of Tweets for display.

The filtered results cache will always be used first, unless:
* It expires and the raw results cache exists, in which cache the raw results cache is read and filtered for disply and caching.
* Neither the raw and filtered results caches exist, in whic cache the backup of the filtered cache is used.

The backup of the filtered cache never expires, it is only overwritten if a conditions are met.




