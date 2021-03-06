=== Newspress, Newstex Publisher ===
Contributors: newstex
Tags: syndication, newstex
Requires at least: 3.1.x
Tested up to: 4.0
Stable tag: trunk

Newspress automatically syndicates posts made to your Wordpress blog directly to Newstex without having to worry about RSS feeds.


== Description ==

Newspress automatically syndicates posts made to your Wordpress blog directly to Newstex without having to worry about RSS
feeds. It's as simple as installing the plugin and entering your Newstex username/password.

Once your credentials are set up, every post you add/edit will automatically be sent to Newstex and processed.

After installing the plugin and entering your credentials, it is a good idea to edit an old post or create a new test post
in order to ensure that the plugin is working properly.


== Installation ==

1. Put the 'newspress' folder in the wp-content/plugins folder
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Under the Settings menu, select Newspress
1. Enter your Newstex username/password
1. It is recommended that you then edit an old post/create a new post and then check that it publishes properly
1. You're done, continue using Wordpress as you would normally and now your posts will be published to Newstex

== Frequently Asked Questions ==

= Do I need a Newstex account before sending stories? =

Yes, because we need your username/password to determine where to publish the story to.

= I don't see my stories publishing, is something wrong? =

Typically it takes a few minutes for Newstex to process a post from Wordpress. If the post doesn't show up in five or ten minutes:
1. Check that your username/password are correct.
1. Try editing the post and re-saving it.
1. Contact support [a t] newstex [d o t] com with your concerns.


== Changelog ==
= 0.9.7 =
Tested up to version 4.0

= 0.9.6 =
Bug fixes, Tested with newer versions of wordpress to make sure the POST handler uses the new WP_Post object instead of expecting an ID

= 0.9.5 =
Fixed up posting in newer versions of wordpress

= 0.9.4 =
Tested up to version 3.5

= 0.9.3 =
Switched to using apply_filters to properly format HTML content in posts

= 0.9.2 =
Fixed HTML formatting

= 0.9.1 =
Fixed message override on post publication, which is no longer needed

= 0.9.0 =
Updated to background calls to publish stories, speeding up the publication process. You should no longer experience delays when hitting "Publish" or "Update".

= 0.8.0 =
* Scheduled posts are now also sent to Newstex when they are published

= 0.7.1 =
* Now sends story language

= 0.7 =
* Categories are now read and sent to Newstex
* Bug fixes

= 0.6 =
* Clarified interface
* Now posts to content.newstex.us

= 0.5 =
* Admin page for selecting credentials
* Sends POST requests with story content

== Upgrade Notice ==
= 0.8.0 =
* Scheduled posts are now also sent to Newstex when they are published

= 0.7.1 =
0.7.1 supports tags and categories and sends the post's language

= 0.6 =
0.6 supports sending posts to Newstex to have your stories published to Newstex's audience

= 0.5 =
0.5 supports sending posts to Newstex to have your stories published to Newstex's audience
