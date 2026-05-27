<?php
/**
 * Plugin Name: Polylang Same Slug
 * Description: Allow same post slugs for different languages in Polylang
 */

if (!defined('ABSPATH')) {
    exit;
}

add_filter('wp_unique_post_slug', function ($slug, $post_ID, $post_status, $post_type, $post_parent, $original_slug) {
    if (!function_exists('pll_get_post_language') || $slug === $original_slug) {
        return $slug;
    }

    $post_lang = pll_get_post_language($post_ID, 'slug');
    if (!$post_lang) {
        return $slug;
    }

    global $wpdb;
    $conflict_id = $wpdb->get_var($wpdb->prepare(
        "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type = %s AND post_parent = %d AND ID != %d AND post_status != 'trash' LIMIT 1",
        $original_slug, $post_type, $post_parent, $post_ID
    ));

    if (!$conflict_id) {
        return $slug;
    }

    $conflict_lang = pll_get_post_language($conflict_id, 'slug');

    if ($conflict_lang && $conflict_lang !== $post_lang) {
        return $original_slug;
    }

    return $slug;
}, 10, 6);

add_action('parse_request', function ($wp) {
    if (is_admin() || !function_exists('pll_current_language')) {
        return;
    }

    $slug = null;
    if (isset($wp->query_vars['pagename'])) {
        $slug = basename($wp->query_vars['pagename']);
    } elseif (isset($wp->query_vars['name'])) {
        $slug = $wp->query_vars['name'];
    }

    if (!$slug) {
        return;
    }

    $current_lang = pll_current_language('slug');
    if (!$current_lang) {
        return;
    }

    $term = get_term_by('slug', $current_lang, 'language');
    if (!$term) {
        return;
    }

    global $wpdb;
    $post_id = $wpdb->get_var($wpdb->prepare(
        "SELECT p.ID FROM {$wpdb->posts} p
         INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
         WHERE p.post_name = %s
         AND p.post_status = 'publish'
         AND p.post_type IN ('post', 'page')
         AND tr.term_taxonomy_id = %d
         LIMIT 1",
        $slug, $term->term_taxonomy_id
    ));

    if ($post_id) {
        $wp->query_vars['p'] = $post_id;
        $wp->query_vars['post_type'] = get_post_type($post_id);
        unset($wp->query_vars['name'], $wp->query_vars['pagename']);
    }
}, 20);
