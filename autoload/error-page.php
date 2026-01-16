<?php

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

/**
 * Fix the "Go back" button on 404 pages.
 *
 * The ComponentLibrary TagSanitizer strips "javascript:" from href values for security,
 * which breaks the history.go(-1) navigation. This filter converts the link to a button
 * with an onclick handler instead.
 */
add_filter('ComponentLibrary/Component/Button/Data', function ($data) {
    if (!empty($data['href']) && str_starts_with($data['href'], 'javascript:')) {
        $data['attributeList']['onclick'] = substr($data['href'], strlen('javascript:'));
        unset($data['href']);
        $data['componentElement'] = 'button';
    }
    return $data;
});
