<?php

/**
 * Disable verbose page rules in WordPress Multisite.
 *
 * Multisite enables use_verbose_page_rules by default, which causes non-existent
 * pages to return 200 OK with frontpage content instead of 404. This happens because
 * WordPress validates page existence before accepting rewrite matches, and when no
 * page exists it falls back to the frontpage with empty query_vars.
 *
 * Requires rewrite flush after activation: wp rewrite flush or Settings → Permalinks → Save.
 */
add_action('init', function () {
    global $wp_rewrite;
    $wp_rewrite->use_verbose_page_rules = false;
}, 1);

function mx_get_custom_404_page() {
  static $custom_page;
  if (isset($custom_page)) {
    return $custom_page;
  }
  $custom_page = get_page_by_path("page-not-found") ?: false;
  return $custom_page;
}

function is_custom_404() {
  $custom_404_page = mx_get_custom_404_page();
  return $custom_404_page && $custom_404_page->ID === get_queried_object_id();
}

add_action(
  "wp",
  function () {
    if (is_404()) {
      $custom_404_page = mx_get_custom_404_page();
      if ($custom_404_page) {
        // Reset query flags so that WordPress treats this as a regular page.
        global $wp_query, $post;

        $wp_query->is_404 = false;
        $wp_query->is_front_page = false;
        $wp_query->is_page = true;
        $wp_query->is_singular = true;
        $wp_query->queried_object_id = $post->ID;
        $wp_query->post_count = 1;
        $wp_query->current_post = -1;
        $wp_query->posts = [$post];
        $post = $custom_404_page;

        setup_postdata($post);
      }
    }
  },
  10,
);

add_action(
  "init",
  function () {
    // Theme is not always loaded, e.g. when running database updates.
    if (!defined("MUNICIPIO_PATH")) {
      define("MUNICIPIO_PATH", get_template_directory() . "/");
    }
    \Municipio\Helper\Template::add(
      __("Error page", "municipio-extended"),
      \Municipio\Helper\Template::locateTemplate("error-template.blade.php"),
      "all",
    );
  },
  11,
);
