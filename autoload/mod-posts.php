<?php

/**
 * Adds mixed option to display modes
 */
add_filter(
  "acf/load_field/key=field_571dfd4c0d9d9",
  function ($field) {
    $field["choices"]["mixed"] = _x(
      "Cards and list",
      "Posts Module Display Mode",
      "municipio-extended",
    );

    $options_to_remove = ["items", "news", "grid", "features-grid"];

    foreach ($options_to_remove as $option) {
      if (isset($field["choices"][$option])) {
        unset($field["choices"][$option]);
      }
    }
    return $field;
  },
  99,
);

add_filter(
  "/Modularity/externalViewPath",
  function ($paths) {
    $paths["mod-posts"][] = mx_get_default_module_view_path("mod-posts");
    $paths["mod-posts"][] = MUNICIPIO_EXTENDED_PATH . "/views/mod-posts";
    return $paths;
  },
  2,
);

add_filter("Modularity/Module/Posts/TemplateController/Mixed", function () {
  return "MunicipioExtended\\Modularity\\ModPosts\\TemplateController\\MixedTemplate";
});

add_filter("Modularity/Display/mod-posts/viewData", function ($data) {
  $data["lang"]["readMore"] = "";
  return $data;
});

add_action("acf/init", function () {
  acf_add_local_field([
    "key" => "field_571e01e7f248d",
    "label" => __("Visa taxonomier", "modularity"),
    "name" => "show_taxonomies_slider",
    "aria-label" => "",
    "type" => "true_false",
    "instructions" => "",
    "required" => 0,
    "parent" => "group_571dfd3c07a77",
    "conditional_logic" => [
      0 => [
        0 => [
          "field" => "field_571dfd4c0d9d9",
          "operator" => "==",
          "value" => "index",
        ],
      ],
    ],
    "wrapper" => [
      "width" => "",
      "class" => "",
      "id" => "",
    ],
    "message" => "",
    "default_value" => 0,
    "ui_on_text" => "",
    "ui_off_text" => "",
    "ui" => 1,
  ]);
});

add_action("acf/init", function () {
  acf_add_local_field([
    "key" => "field_taxonomy_selection_in_fields",
    "label" => __("Välj taxonomier", "municipio-extended"),
    "name" => "taxonomy_selection_in_fields",
    "type" => "checkbox",
    "parent" => "group_571dfd3c07a77",
    "conditional_logic" => [
      [
        [
          "field" => "field_571e01e7f248d",
          "operator" => "==",
          "value" => 1,
        ],
      ],
    ],
    "choices" => [],
    "layout" => "horizontal",
  ]);
});

// Add taxonomies to the cards
add_filter("acf/load_field/key=field_taxonomy_selection_in_fields", function (
  $field,
) {
  $post_type = get_field("posts_data_post_type");

  if (!$post_type && isset($_GET["post_type"])) {
    $post_type = sanitize_text_field($_GET["post_type"]);
  }

  if (!$post_type) {
    $field["choices"] = [];
    return $field;
  }

  $taxonomies = get_taxonomies([], "objects");
  // For each taxonomy, add it as an option
  foreach ($taxonomies as $taxonomy) {
    if ($taxonomy->public) {
      if (!empty($taxonomy->label)) {
        if ($taxonomy->label === "Custom Results") {
          $field["choices"][$taxonomy->name] = $taxonomy->label;
        } else {
          // Get post types for this taxonomy
          $post_types = get_taxonomy($taxonomy->name)->object_type;
          $post_types_string = implode(", ", $post_types);
          // Add taxonomy label with post types in parentheses
          $field["choices"][$taxonomy->name] =
            $taxonomy->label . " (" . $post_types_string . ")";
        }
      }
    }
  }

  return $field;
});

add_filter(
  "Modularity/Module/Posts/Helper/getPosts/data",
  function ($data, $fields, $post) {
    if (empty($fields["show_taxonomies_slider"])) {
      return $data;
    }

    if (!is_array($data)) {
      $data = [];
    }

    $data["taxonomiesToDisplay"] = !empty(
      $fields["taxonomy_selection_in_fields"]
    )
      ? $fields["taxonomy_selection_in_fields"]
      : [];

    // Modify the post data to include tags
    if (!empty($data["taxonomiesToDisplay"])) {
      $tags = [];
      foreach ($data["taxonomiesToDisplay"] as $taxonomy_name) {
        $terms = wp_get_post_terms($post->ID, $taxonomy_name);
        if (!empty($terms) && !is_wp_error($terms)) {
          foreach ($terms as $term) {
            $tags[] = [
              "label" => $term->name,
              "href" => get_term_link($term),
            ];
          }
        }
      }
      $post->taxonomies = $data["taxonomiesToDisplay"];
      $post->taxonomy_terms = $tags;
    }

    return $data;
  },
  10,
  3,
);

// Remove taxonomy field for the post
add_action(
  "admin_init",
  function () {
    if (function_exists("acf_remove_local_field_group")) {
      // Taxonomies field for Post
      acf_remove_local_field_group("group_630645d822841");
    }
  },
  20,
);

// add_filter("Modularity/Display/mod-posts/viewData", function ($data) {
//   $data["lang"]["readMore"] = "";
//   return $data;
// });
