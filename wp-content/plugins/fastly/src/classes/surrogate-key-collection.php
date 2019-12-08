<?php

/**
 * Collects all Surrogate Keys to add to an individual response.
 */
class Purgely_Surrogate_Key_Collection
{

    const FASTLY_TEMPLATE_KEY_PREFIX = 'tm-';

    /**
     * The surrogate key values.
     *
     * @var array The surrogate keys that will be set.
     */
    private $_keys = array();

    /**
     * Template types
     * @var array
     */
    static public $types = array(
        'single',
        'preview',
        'page',
        'archive',
        'date',
        'year',
        'month',
        'day',
        'time',
        'author',
        'category',
        'tag',
        'tax',
        'search',
        'feed',
        'comment_feed',
        'trackback',
        'home',
        '404',
        'paged',
        'admin',
        'attachment',
        'singular',
        'robots',
        'posts_page',
        'post_type_archive',
    );

    public $custom_ttl = false;

    /**
     * Construct the object.
     *
     * @param  WP_Query $wp_query The main query object.
     */
    public function __construct($wp_query)
    {
        // Register the keys that need to be set for the current request, starting with post IDs.
        $keys = $this->_add_key_post_ids($wp_query);

        // Get the query type.
        $template_key = $this->_add_key_query_type($wp_query);

        // Get all taxonomy terms and author info if on a single post.
        $term_keys = array();

        if ($wp_query->is_single()) {
            $taxonomies = apply_filters('purgely_taxonomy_keys', (array)get_taxonomies());

            foreach ($taxonomies as $taxonomy) {
                $term_keys = array_merge($term_keys, $this->_add_key_terms_single($wp_query->post->ID, $taxonomy));
            }

            // Get author information.
            $term_keys = array_merge($term_keys, $this->_add_key_author($wp_query->post));

        } else {
            if ($wp_query->is_category() || $wp_query->is_tag() || $wp_query->is_tax()) {
                $term_keys = $this->_add_key_terms_taxonomy();
            }
        }

        // Merge, de-dupe, and prune empties.
        $keys = array_merge(
            $keys,
            $template_key,
            $term_keys
        );

        $keys = array_unique($keys);
        $keys = array_filter($keys);

        // If there is always purge key existing, remove all others
        $always_purged = Purgely_Related_Surrogate_Keys::get_always_purged_types();
        foreach($always_purged as $k) {
            if (in_array($k, $template_key)) {
                $keys = $template_key;
                break;
            }
        }

        $this->set_keys($keys);
    }

    /**
     * Add a key for each post ID to all pages that include the post.
     *
     * @param  WP_Query $wp_query The main query.
     * @return array       $keys        The "post-{ID}" keys.
     */
    private function _add_key_post_ids($wp_query)
    {
        $keys = array();

        foreach ($wp_query->posts as $post) {
            $keys[] = 'p-' . absint($post->ID);
        }

        return $keys;
    }

    /**
     * Determine the type of WP template being displayed.
     *
     * @param WP_Query $wp_query The query object to inspect.
     * @return array $key The template key.
     */
    private function _add_key_query_type($wp_query)
    {
        $template_type = '';
        $key = '';

        /**
         * This function has the potential to be called in the admin context. Unfortunately, in the admin context,
         * $wp_query, is not a WP_Query object. Bad things happen when call_user_func is applied below. As such, lets' be
         * cautious and make sure that the $wp_query object is indeed a WP_Query object.
         */
        if (is_a($wp_query, 'WP_Query')) {
            // List of all "is" calls.
            $types = $this::$types;

            /**
             * Foreach "is" call, if it is a callable function, call and see if it returns true. If it does, we know what type
             * of template we are currently on. Break the loop and return that value.
             */
            foreach ($types as $type) {
                $callable = array($wp_query, 'is_' . $type);
                if (method_exists($wp_query, 'is_' . $type) && is_callable($callable)) {
                    if (true === call_user_func($callable)) {
                        $template_type = $type;
                        break;
                    }
                }
            }
        }

        // Only set the key if it exists.
        if (!empty($template_type)) {
            $key = self::FASTLY_TEMPLATE_KEY_PREFIX . $template_type;
        }

        $this->set_custom_ttl($template_type);

        return (array)$key;
    }

    public function set_custom_ttl($template_type)
    {
        $custom_ttls = Purgely_Settings::get_setting('custom_ttl_templates');
        $ttl = isset($custom_ttls[$template_type]) ? $custom_ttls[$template_type] : false;
        $this->custom_ttl = (int)$ttl;
    }

    public function get_custom_ttl()
    {
        return $this->custom_ttl;
    }

    /**
     * Get the term keys for every term associated with a post.
     *
     * @param  int $post_id Post ID.
     * @param  string $taxonomy The taxonomy to look for associated terms.
     * @param  WP_Query $wp_query The current wp_query to investigate.
     * @return array              The term slug/taxonomy combos for the post.
     */
    private function _add_key_terms_single($post_id, $taxonomy)
    {
        $keys = array();
        $terms = get_the_terms($post_id, $taxonomy);

        if ($terms) {
            foreach ($terms as $term) {
                if (isset($term->term_id)) {
                    $keys[] = 't-' . $term->term_id;
                }
            }
        }

        return $keys;
    }

    /**
     * Get the term keys for taxonomies.
     *
     * @return array The taxonomy combos for the post.
     */
    private function _add_key_terms_taxonomy()
    {
        $keys = array();

        $queried_object = get_queried_object();
        // archive page? author page? single post?

        if (!empty($queried_object->term_id) && !empty($queried_object->taxonomy)) {
            $keys[] = 't-' . absint($queried_object->term_id);
        }

        return $keys;
    }


    /**
     * Get author related to this post.
     *
     * @param  WP_Post $post The post object to search for related author information.
     * @return array               The related author key.
     */
    private function _add_key_author($post)
    {
        $author = absint($post->post_author);
        $key = array();

        if ($author > 0) {
            $key[] = 'a-' . absint($author);
        }

        return $key;
    }

    /**
     * Set the keys variable.
     *
     * @param  array $keys Array of Purgely_Surrogate_Key objects.
     * @return void
     */
    public function set_keys($keys)
    {
        $this->_keys = $keys;
    }

    /**
     * Set an individual key.
     *
     * @param  Purgely_Surrogate_Keys_Header $key Purgely_Surrogate_Key object.
     * @return void
     */
    public function set_key($key)
    {
        $keys = $this->get_keys();
        $keys[] = $key;

        $this->set_keys($keys);
    }

    /**
     * Get all of the keys to be sent in the headers.
     *
     * @return array    Array of Purgely_Surrogate_Key objects
     */
    public function get_keys()
    {
        return $this->_keys;
    }
}
