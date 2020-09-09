=== Pantheon Advanced Page Cache ===
Contributors: getpantheon, danielbachhuber
Tags: pantheon, cdn, cache
Requires at least: 4.7
Tested up to: 5.5
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automatically clear related pages from Pantheon's Edge when you update content. High TTL. Fresh content. Visitors never wait.

== Description ==

[![Travis CI](https://travis-ci.org/pantheon-systems/pantheon-advanced-page-cache.svg?branch=master)](https://travis-ci.org/pantheon-systems/pantheon-advanced-page-cache) [![CircleCI](https://circleci.com/gh/pantheon-systems/pantheon-advanced-page-cache.svg?style=svg)](https://circleci.com/gh/pantheon-systems/pantheon-advanced-page-cache)

For sites wanting fine-grained control over how their responses are represented in their edge cache, Pantheon Advanced Page Cache is the golden ticket. Here's a high-level overview of how the plugin works:

1. When a response is generated, the plugin uses surrogate keys based on WordPress' main `WP_Query` object to "tag" the response with identifers for the data used in the response. See the "Adding Custom Keys" section for including your own surrogate keys.
2. When WordPress data is modified, the plugin triggers a purge request for the data's corresponding surrogate keys.

Because of its surrogate key technology, Pantheon Advanced Page Cache empowers WordPress sites with a significantly more accurate cache purge mechanism, and generally higher cache hit rate. It even works with the WordPress REST API.

Go forth and make awesome! And, once you've built something great, [send us feature requests (or bug reports)](https://github.com/pantheon-systems/pantheon-advanced-page-cache/issues).

== Installation ==

To install Pantheon Advanced Page Cache, follow these steps:

1. Install the plugin from WordPress.org using the WordPress dashboard.
2. Activate the plugin.

To install Pantheon Advanced Page Cache in one line with WP-CLI:

    wp plugin install pantheon-advanced-page-cache --activate

== How It Works ==

Pantheon Advanced Page Cache makes heavy use of surrogate keys, which enable responses to be "tagged" with identifiers that can then later be used in purge requests. For instance, a home page response might include the `Surrogate-Key` header with these keys:

    Surrogate-Key: front home post-43 user-4 post-41 post-9 post-7 post-1 user-1

Similarly, a `GET` requests to `/wp-json/wp/v2/posts` might include the `Surrogate-Key` header with these keys:

    Surrogate-Key: rest-post-collection rest-post-43 rest-post-43 rest-post-9 rest-post-7 rest-post-1

Because cached responses include metadata describing the data therein, surrogate keys enable more flexible purging behavior like:

* When a post is updated, clear the cache for the post's URL, the homepage, any index view the post appears on, and any REST API endpoints the post is present in.
* When an author changes their name, clear the cache for the author's archive and any post they've authored.

There is a limit to the number of surrogate keys in a response, so we've optimized them based on a user's expectation of a normal WordPress site. See the "Emitted Keys" section for full details on which keys are included, and the "Adding Custom Keys" section following for information on how to add your own.

= Adding Custom Keys =

By default, Pantheon Advanced Page Cache generates surrogate keys based on an interpretation of the main `WP_Query` query object. Because WordPress sends headers before the page is rendered, you need to use the `pantheon_wp_main_query_surrogate_keys` filter to include additional surrogate keys for any data present on the page.

For example, to include surrogate keys for a sidebar rendered on the homepage, you can filter the keys using the `is_home()` template tag:

    /**
     * Add surrogate key for the featured content sidebar rendered on the homepage.
     */
    add_filter( 'pantheon_wp_main_query_surrogate_keys', function( $keys ){
	    if ( is_home() ) {
            $keys[] = 'sidebar-home-featured';
        }
        return $keys;
    });

Then, when sidebars are updated, you can use the `pantheon_wp_clear_edge_keys()` helper function to emit a purge event specific to the surrogate key:

    /**
     * Trigger a purge event for the featured content sidebar when widgets are updated.
     */
    add_action( 'update_option_sidebars_widgets', function() {
        pantheon_wp_clear_edge_keys( array( 'sidebar-home-featured' ) );
    });

Similarly, to include surrogate keys for posts queried on the homepage, you can pre-fetch the posts before the page is rendered:

    /**
     * An example of pre-fetching a WP_Query to tag the
     * response with queried data. You'd use `papcx_wp_query()`
     * a second time within your template to use the data.
     */
    add_filter( 'pantheon_wp_main_query_surrogate_keys', function( $keys ) {
        if ( is_home() ) {
            $query = papcx_wp_query( array(
                'post_type' => 'page',
            ) );
            foreach( $query->posts as $post ) {
                $keys[] = 'post-' . $post->ID;
            }
        }
        return $keys;
    });

    /**
     * Register a 'papc-non-persistent' cache group to cache data
     * in a non-persistent manner. We only want data in this group
     * to be cached within the page request.
     */
    add_action( 'init', function(){
        wp_cache_add_non_persistent_groups( array( 'papc-non-persistent' ) );
    });

    /**
     * Helper function to instantiate a WP_Query object only
     * once per page request.
     *
     * @param array $args Arguments to pass to WP_Query.
     * @return WP_Query
     */
    function papcx_wp_query( $args = array() ) {
        $cache_key = md5( serialize( $args ) );
        // WP_Query object will be in cache the second time we use the function.
        $cache_value = wp_cache_get( $cache_key, 'papc-non-persistent' );
        if ( false !== $cache_value ) {
            return $cache_value;
        }
        $query = new WP_Query( $args );
        wp_cache_set( $cache_key, $query, 'papc-non-persistent' );
        return $query;
    }

Because Pantheon Advanced Page Cache already handles WordPress post purge events, there's no additional call to `pantheon_wp_clear_edge_keys()`.

Lastly, the `pantheon_wp_rest_api_surrogate_keys` filter lets you filter surrogate keys present in a REST API response.

Need a bit more power? In addition to `pantheon_wp_clear_edge_keys()`, there are two additional helper functions you can use:

* `pantheon_wp_clear_edge_paths( $paths = array() )` - Purge cache for one or more paths.
* `pantheon_wp_clear_edge_all()` - Warning! With great power comes great responsibility. Purge the entire cache, but do so wisely.

== WP-CLI Commands ==

This plugin implements a variety of [WP-CLI](https://wp-cli.org) commands. All commands are grouped into the `wp pantheon cache` namespace.

    $ wp help pantheon cache
    
    NAME
    
      wp pantheon cache
    
    DESCRIPTION
    
      Manage the Pantheon Advanced Page Cache.
    
    SYNOPSIS
    
      wp pantheon cache <command>
    
    SUBCOMMANDS
    
      purge-all       Purge the entire page cache.
      purge-key       Purge one or more surrogate keys from cache.
      purge-path      Purge one or more paths from cache.

Use `wp help pantheon cache <command>` to learn more about each command.

== Debugging ==

By default, Pantheon's infrastructure strips out the `Surrogate-Key` response header before responses are served to clients. The contents of this header can be viewed as `Surrogate-Key-Raw` by adding on a debugging header to the request.

A direct way of inspecting headers is with `curl -I`. This command will make a request and show just the response headers. Adding `-H "Pantheon-Debug:1"` will result in `Surrogate-Key-Raw` being included in the response headers. The complete command looks like this:

    curl -IH "Pantheon-Debug:1" https://scalewp.io/

Piping to `grep` will filter the output down to just the `Surrogate-Key-Raw` header:

    curl -IH "Pantheon-Debug:1" https://scalewp.io/ | grep -i Surrogate-Key-Raw

== Emitted Keys and Purge Events =

= Emitted Keys on Traditional Views =

**Home `/`**

* Emits surrogate keys: `home`, `front`, `post-<id>` (all posts in main query)

**Single post `/2016/10/14/surrogate-keys/`**

* Emits surrogate keys: `single`, `post-<id>`, `post-user-<id>`, `post-term-<id>` (all terms assigned to post)

**Author archive `/author/pantheon/`**

* Emits surrogate keys: `archive`, `user-<id>`, `post-<id>` (all posts in main query)

**Term archive `/tag/cdn/`**

* Emits surrogate keys: `archive`, `term-<id>`, `post-<id>` (all posts in main query)

**Day archive `/2016/10/14/`**

* Emits surrogate keys: `archive`, `date`, `post-<id>` (all posts in main query)

**Month archive `/2016/10/`**

* Emits surrogate keys: `archive`, `date`, `post-<id>` (all posts in main query)

**Year archive `/2016/`**

* Emits surrogate keys: `archive`, `date`, `post-<id>` (all posts in main query)

**Search `/?s=<search>`**

* Emits surrogate keys: `search`, either `search-results` or `search-no-results`, `post-<id>` (all posts in main query)

**Not found (404)**

* Emits surrogate keys: `404`

= Emitted Keys on REST API Endpoints =

**Posts**

* `/wp-json/wp/v2/posts` emits surrogate keys: `rest-post-collection`, `rest-post-<id>`
* `/wp-json/wp/v2/posts/<id>` emits surrogate keys: `rest-post-<id>`

**Pages**

* `/wp-json/wp/v2/pages` emits surrogate keys: `rest-page-collection`, `rest-post-<id>`
* `/wp-json/wp/v2/pages/<id>` emits surrogate keys: `rest-post-<id>`

**Categories**

* `/wp-json/wp/v2/categories` emits surrogate keys: `rest-category-collection`, `rest-term-<id>`
* `/wp-json/wp/v2/categories/<id>` emits surrogate keys: `rest-term-<id>`

**Tags**

* `/wp-json/wp/v2/tags` emits surrogate keys: `rest-post_tag-collection`, `rest-term-<id>`
* `/wp-json/wp/v2/tags/<id>` emits surrogate keys: `rest-term-<id>`

**Comments**

* `/wp-json/wp/v2/comments` emits surrogate keys: `rest-comment-collection`, `rest-comment-post-<post-id>`, `rest-comment-<id>`
* `/wp-json/wp/v2/comments/<id>` emits surrogate keys: `rest-comment-post-<post-id>`, `rest-comment-<id>`

**Users**

* `/wp-json/wp/v2/users` emits surrogate keys: `rest-user-collection`, `rest-user-<id>`
* `/wp-json/wp/v2/users/<id>` emits surrogate keys: `rest-user-<id>`

**Settings**

* `/wp-json/wp/v2/settings` emits surrogate keys: `rest-setting-<name>`

= Purge Events =

Different WordPress actions cause different surrogate keys to be purged, documented here.

**wp_insert_post / transition_post_status / before_delete_post / delete_attachment**

* Purges surrogate keys: `home`, `front`, `404`, `post-<id>`, `user-<id>`, `term-<id>`, `rest-<type>-collection`, `rest-comment-post-<id>`
* Affected views: homepage, single post, any page with 404 header, any archive where post displays, author archive, term archive, REST API collection and resource endpoints

**clean_post_cache**

* Purges surrogate keys: `post-<id>`, `rest-post-<id>`
* Affected views: single post, REST API resource endpoint

**created_term / edited_term / delete_term**

* Purges surrogate keys: `term-<id>`, `post-term-<id>`, `rest-<taxonomy>-collection`
* Affected views: term archive, any post where the term is assigned, REST API collection and resource endpoints

**clean_term_cache**

* Purges surrogate keys: `term-<id>`, `rest-term-<id>`
* Affected views: term archive, REST API resource endpoint

**wp_insert_comment / transition_comment_status**

* Purges surrogate keys: `rest-comment-collection`, `rest-comment-<id>`
* Affected views: REST API collection and resource endpoints

**clean_comment_cache**

* Purges surrogate keys: `rest-comment-<id>`
* Affected views: REST API resource endpoint

**clean_user_cache**

* Purges surrogate keys: `user-<id>`, `rest-user-<id>`
* Affected views: author archive, any post where the user is the author

**updated_option**

* Purges surrogate keys: `rest-setting-<name>`
* Affected views: REST API resource endpoint

== Changelog ==

= 1.0.0 (March 2, 2020) =
* Plugin is stable.

= 0.3.1 (October 27th, 2019) =
* Fixes reversed argument order with use of `implode()` [[#139](https://github.com/pantheon-systems/pantheon-advanced-page-cache/pull/139)].
* Various PHPCS cleanup [[#127](https://github.com/pantheon-systems/pantheon-advanced-page-cache/pull/127)].

= 0.3.0 (November 27th, 2017) =
* Emits '404' surrogate key on 404s; purges when purging the homepage [[#107](https://github.com/pantheon-systems/pantheon-advanced-page-cache/pull/107)].
* Adds more specific filters for modifying surrogate keys in different contexts [[#109](https://github.com/pantheon-systems/pantheon-advanced-page-cache/pull/109)].
* Cleans up codebase according to WordPress Coding Standards [[#110](https://github.com/pantheon-systems/pantheon-advanced-page-cache/pull/110), [#116](https://github.com/pantheon-systems/pantheon-advanced-page-cache/pull/116)].

= 0.2.1 (October 25th, 2017) =
* Ensures use of `?_embed` emits correct surrogate keys [[#103](https://github.com/pantheon-systems/pantheon-advanced-page-cache/pull/103)].

= 0.2.0 (August 10th, 2017) =
* Automatically trims large lists of surrogate keys that break Nginx and Varnish limits for header size.

= 0.1.5 (May 24th, 2017) =
* Disables emitting surrogate keys for the admin, unless explicitly added by filter.

= 0.1.4 (March 7th, 2017) =
* Emits `feed` surrogate key for RSS feeds, and purges when posts are created, modified, or deleted.

= 0.1.3 (March 1st, 2017) =
* Prevents error notices by only accessing `$rest_base` property of post types and taxonomies when set.

= 0.1.2 (December 6th, 2016) =
* Permits admins to flush cache for a specific page if the `delete_others_posts` capability has been deleted.

= 0.1.1 (November 30th, 2016) =
* Drops settings UI in favor of including it in Pantheon's WordPress upstream.

= 0.1.0 (November 23rd, 2016) =
* Initial release.
