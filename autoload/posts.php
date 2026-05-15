<?php

/**
 * Puts the archive for posts at /nyheter
 */
add_action("init", function () {
  // Add rewrite rule if posts are not disabled
  if (!get_option("options_disable_default_blog_post_type", 0)) {
    add_rewrite_rule('^nyheter$', "index.php?category_name=", "top");
  }
});

add_filter(
  "post_type_archive_link",
  function ($link, $post_type) {
    if (
      $post_type == "post" &&
      !get_option("options_disable_default_blog_post_type", 0)
    ) {
      return home_url("/nyheter/");
    }
    return $link;
  },
  10,
  2,
);

/**
 * Makes sure the first link in the breadcrumbs is always the same
 */
add_filter("Municipio/Breadcrumbs/Items", function ($pageData) {
  if (!get_option("options_disable_default_blog_post_type", 0)) {
    array_shift($pageData);
    array_unshift($pageData, [
      "label" => __("Home"),
      "href" => get_home_url(),
      "current" => is_front_page() ? true : false,
      "icon" => "home",
    ]);
    return $pageData;
  }
  return $pageData;
});

/**
 * Fixes the archive link for posts
 */
add_filter("option_page_for_posts", "__return_null");

add_filter(
  "Modularity/Module/Posts/archiveUrl",
  function ($archive_url, $post_type) {
    if (!get_option("options_disable_default_blog_post_type", 0)) {
      if (!$archive_url) {
        $archive_url = get_post_type_archive_link($post_type) ?: false;
      }
      return $archive_url;
    }
    return $archive_url;
  },
  10,
  2,
);

add_filter(
  "register_post_type_args",
  function ($args, $post_type) {
    if (!isset($args["rewrite"]) || $args["rewrite"] === true) {
      $args["rewrite"] = [
        "with_front" => false,
      ];
      // error_log(var_export(["post_type" => $post_type], true));
    } elseif (is_array($args["rewrite"])) {
      /**
       * Filters post types that should keep the WordPress front base in rewrites.
       *
       * @param string[] $post_types Post type names.
       * @return string[] Filtered post type names.
       */
      if (
        !in_array($post_type, apply_filters("mx_post_types_with_front", []))
      ) {
        // if (($args["rewrite"]["with_front"] ?? null) !== false) {
        //   error_log(var_export(["post_type" => $post_type], true));
        // }
        $args["rewrite"]["with_front"] = false;
      }
    }
    return $args;
  },
  10,
  2,
);

add_filter(
  "register_taxonomy_args",
  function ($args, $taxonomy) {
    if (!isset($args["rewrite"]) || $args["rewrite"] === true) {
      $args["rewrite"] = [
        "with_front" => false,
      ];
      // error_log(var_export(["taxonomy" => $taxonomy], true));
    } elseif (is_array($args["rewrite"])) {
      /**
       * Filters taxonomies that should keep the WordPress front base in rewrites.
       *
       * @param string[] $taxonomies Taxonomy names.
       * @return string[] Filtered taxonomy names.
       */
      if (!in_array($taxonomy, apply_filters("mx_taxonomies_with_front", []))) {
        // if (($args["rewrite"]["with_front"] ?? null) !== false) {
        //   error_log(var_export(["taxonomy" => $taxonomy], true));
        // }
        $args["rewrite"]["with_front"] = false;
      }
    }
    return $args;
  },
  10,
  2,
);
