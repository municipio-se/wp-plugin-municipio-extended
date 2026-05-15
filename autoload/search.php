<?php

use Elastic\Elasticsearch\ClientBuilder;
use ElasticPress\Utils;

add_filter("Municipio/Hook/searchFormValidation", "__return_false");

add_filter("ep_skip_query_integration", "__return_true");

function mx_enable_external_page_content_type() {
  return get_field("mx_enable_external_page_content_type", "option") ?? false;
}

/**
 * Accounts for the Municipio settings for hiding built-in post types.
 */
add_action(
  "init",
  function () {
    global $wp_post_types;

    if (isset($wp_post_types["post"])) {
      if (
        function_exists("get_field") &&
        get_option("options_disable_default_blog_post_type", 0)
      ) {
        $wp_post_types["post"]->exclude_from_search = true;
      }
    }
    if (isset($wp_post_types["page"])) {
      if (
        function_exists("get_field") &&
        get_field("disable_default_page_post_type", "option")
      ) {
        $wp_post_types["page"]->exclude_from_search = true;
      }
    }
  },
  12,
);

/**
 * Returns an array of searchable post types, based on the `exclude_from_search` property.
 * @param string $field Whether to return the post types as names or objects.
 * @return array
 */
function mx_get_searchable_post_types($field = "names") {
  if (!class_exists("\ElasticPress\Indexables")) {
    return;
  }
  /**
   * @var \ElasticPress\Indexable\Post $indexable
   */
  $indexable = \ElasticPress\Indexables::factory()->get("post");
  $post_types = $indexable->get_indexable_post_types();

  // Add attachment if documents are indexable
  /**
   * @var \ElasticPress\Feature\Documents\Documents $documents_feature
   */
  $documents_feature = \ElasticPress\Features::factory()->get_registered_feature(
    "documents",
  );
  $documents_feature_is_active = $documents_feature->is_active();
  if ($documents_feature_is_active) {
    $post_types["attachment"] = "attachment";
  }

  if ($field === "names") {
    return array_keys($post_types);
  }
  if ($field === "objects") {
    return array_map(function ($post_type) {
      return get_post_type_object($post_type);
    }, array_keys($post_types));
  }
}

function mx_search_perform_es_search($body) {
  $host = Utils\get_host();
  if (empty($host)) {
    throw new Exception("No Elasticsearch host defined.");
  }
  $hosts = [$host];
  // mx_error_log("Searching on hosts", $hosts);
  $index = \ElasticPress\Indexables::factory()
    ->get("post")
    ->get_index_name(null);
  if (empty($index)) {
    throw new Exception("No Elasticsearch index defined.");
  }
  // mx_error_log("Searching in index '$index'");

  $client = ClientBuilder::create()->setHosts($hosts)->build();

  return $client->search([
    "index" => $index,
    "body" => $body,
  ]);
}

function mx_search_ajax_handler() {
  check_ajax_referer("mx_search", "nonce");

  $default_boosted_post_type_weight = 2;
  $post_types = mx_get_searchable_post_types("names");
  if (empty($post_types)) {
    return wp_send_json([
      "success" => false,
      "error" => "No post types are searchable.",
    ]);
  }
  $mx_search_settings_post_types =
    get_field("mx_search_settings_post_types", "option") ?: [];

  $data = json_decode(json_decode('"' . $_POST["data"] . '"'), true);

  // $fieldsToHighlight = [
  //   'post_title',
  //   'post_content_filtered',
  // ];

  $fields = [
    "post_title^10",
    "post_title.exact^10",
    "attachments.attachment.title^10",
    "post_content_filtered^1",
    "post_content_filtered.exact^1",
    "attachments.attachment.content^1",
    "search_keywords^10",
  ];

  $query = $data["s"];

  // mx_error_log("Searching for '$query'");

  $query = [
    "bool" => [
      "must" => [
        [
          // `combined_fields` allows us to search multiple fields with the same query
          "combined_fields" => [
            "query" => $query,
            "fields" => $fields,
            "boost" => 4,
            "minimum_should_match" => "100%", // Same as `"operator" => "and"`
          ],
        ],
        [
          "multi_match" => [
            "query" => $query,
            "fields" => $fields,
            "boost" => 2,
            "fuzziness" => 0,
          ],
        ],
        [
          "multi_match" => [
            "query" => $query,
            "fields" => $fields,
            "fuzziness" => "AUTO",
          ],
        ],
      ],
      "should" => [
        [
          "multi_match" => [
            "query" => $query,
            "type" => "phrase",
            "fields" => $fields,
            "boost" => 4,
          ],
        ],
        [
          "match" => [
            "search_keywords" => [
              "query" => $query,
              "boost" => 10,
            ],
          ],
        ],
      ],
    ],
  ];

  /**
   * Filters the base Elasticsearch bool query before it is wrapped in a function_score query.
   *
   * @param array $query Search bool query.
   * @param array $data Ajax request data.
   * @param array $settings_post_types Search settings keyed by post type.
   * @return array Filtered search bool query.
   */
  $query = apply_filters(
    "mx_search_es_query",
    $query,
    $data,
    $mx_search_settings_post_types,
  );

  $boosted_post_types = [];
  foreach ($post_types as $post_type) {
    if (!isset($mx_search_settings_post_types[$post_type])) {
      continue;
    }
    $boosted_post_types[$post_type] =
      $mx_search_settings_post_types[$post_type]["boost"] ?? 1;
  }
  /**
   * Filters post type boost weights before performing the search.
   *
   * @param array $boosted_post_types Post type boost weights.
   * @param array $data Ajax request data.
   * @return array Filtered post type boost weights.
   */
  $boosted_post_types = apply_filters(
    "mx_search_boosted_post_types",
    $boosted_post_types,
    $data,
  );
  $boosted_post_type_functions = array_map(
    function ($key, $value) use ($default_boosted_post_type_weight) {
      if (is_numeric($key)) {
        $key = $value;
        $value = $default_boosted_post_type_weight;
      }
      if ($value == 1) {
        return false;
      }
      return [
        "filter" => [
          "match" => [
            "post_type" => $key,
          ],
        ],
        "weight" => $value,
      ];
    },
    array_keys($boosted_post_types),
    $boosted_post_types,
  );

  /**
   * Filters generated post type boost functions before the function_score query is built.
   *
   * @param array $boosted_post_type_functions Elasticsearch function definitions.
   * @param array $data Ajax request data.
   * @return array Filtered Elasticsearch function definitions.
   */
  $boosted_post_type_functions = apply_filters(
    "mx_search_boosted_post_type_functions",
    $boosted_post_type_functions,
    $data,
  );

  $decaying_post_types = [];
  foreach ($post_types as $post_type) {
    if (!isset($mx_search_settings_post_types[$post_type])) {
      continue;
    }
    if (!$mx_search_settings_post_types[$post_type]["decay"] ?? false) {
      continue;
    }
    $decaying_post_types[] = $post_type;
  }
  /**
   * Filters post types using date decay before performing the search.
   *
   * @param array $decaying_post_types Post types with optional decay params.
   * @param array $data Ajax request data.
   * @return array Filtered decaying post type definitions.
   */
  $decaying_post_types = apply_filters(
    "mx_search_decaying_post_types",
    $decaying_post_types,
    $data,
  );
  $decaying_post_type_functions = array_map(
    function ($key, $value) {
      if (is_numeric($key)) {
        $key = $value;
        $value = [];
      }
      if (!is_array($value)) {
        throw new Exception("Decaying post type params must be an array.");
      }
      $value = array_merge(
        [
          "origin" => "now",
          "scale" => "30d",
          "decay" => 0.5,
          "field" => "post_date",
        ],
        $value,
      );
      $field = $value["field"];
      unset($value["field"]);

      return [
        "filter" => [
          "match" => [
            "post_type" => $key,
          ],
        ],
        "gauss" => [
          $field => $value,
        ],
      ];
    },
    array_keys($decaying_post_types),
    $decaying_post_types,
  );

  /**
   * Filters generated date decay functions before the function_score query is built.
   *
   * @param array $decaying_post_type_functions Elasticsearch function definitions.
   * @param array $data Ajax request data.
   * @return array Filtered Elasticsearch function definitions.
   */
  $decaying_post_type_functions = apply_filters(
    "mx_search_decaying_post_type_functions",
    $decaying_post_type_functions,
    $data,
  );

  $function_score = [
    "query" => $query,
    "score_mode" => "multiply", // How the scores of the functions are combined.
    "boost_mode" => "multiply", // How the result of the functions is combined with the base score of the document.
    "functions" => [
      ...array_filter(array_values($boosted_post_type_functions)),
      ...array_filter(array_values($decaying_post_type_functions)),
    ],
  ];

  /**
   * Filters the function_score query before performing the search.
   *
   * @param array $function_score Elasticsearch function_score query.
   * @param array $data Ajax request data.
   * @return array Filtered function_score query.
   */
  $function_score = apply_filters(
    "mx_search_es_function_score",
    $function_score,
    $data,
  );

  $es_body = [
    "query" => [
      "function_score" => $function_score,
    ],
    "highlight" => [
      "pre_tags" => ["<mark>"],
      "post_tags" => ["</mark>"],
      "fields" => [
        "post_title" => [
          "number_of_fragments" => 0,
        ],
        "attachments.attachment.title" => [
          "number_of_fragments" => 0,
        ],
        "post_content_filtered" => [
          "number_of_fragments" => 3,
          "fragment_size" => 150,
        ],
        "attachments.attachment.content" => [
          "number_of_fragments" => 3,
          "fragment_size" => 150,
        ],
      ],
    ],
    "from" => (($data["page"] ?: 1) - 1) * $data["hitsPerPage"],
    "size" => $data["hitsPerPage"],
  ];

  /**
   * Filters the final Elasticsearch request body before performing the search.
   *
   * @param array $es_body Elasticsearch request body.
   * @param array $data Ajax request data.
   * @return array Filtered Elasticsearch request body.
   */
  $es_body = apply_filters("mx_search_es_body", $es_body, $data);

  // mx_error_log("Searching with body", json_encode($es_body));

  try {
    $es_results = mx_search_perform_es_search($es_body);

    $total = $es_results["hits"]["total"]["value"] ?? null;
    // mx_error_log("Total: " . $total);

    $hit_source_mapping = [
      "title" => fn($hit) => mx_coalesce_string([
        $hit["highlight"]["attachments.attachment.title"] ?? "",
        $hit["_source"]["attachments"][0]["attachment"]["title"] ?? "",
        $hit["highlight"]["post_title"] ?? "",
        $hit["_source"]["post_title"] ?? "",
      ]),

      "excerpt" => fn($hit) => mx_coalesce_string([
        $hit["highlight"]["attachments.attachment.content"] ?? "",
        wp_trim_words(
          $hit["_source"]["attachments"][0]["attachment"]["content"] ?? "",
          40,
        ),
        $hit["highlight"]["post_content_filtered"] ?? "",
        wp_trim_words($hit["_source"]["post_content_filtered"] ?? "", 40),
      ]),

      "href" => fn($hit) => ($hit["_source"]["post_type"] ?? null) ==
      "attachment"
        ? $hit["_source"]["guid"]
        : $hit["_source"]["permalink"],

      "image" => fn($hit) => get_the_post_thumbnail_url(
        $hit["_source"]["post_id"],
      ),

      "date" => fn($hit) => $hit["_source"]["post_date"] ?? null,

      "type" => fn($hit) => $hit["_source"]["content_type_formatted"] ??
        (get_post_type_labels(
          get_post_type_object($hit["_source"]["post_type"]),
        )->singular_name ??
          null),

      "score" => fn($hit) => $hit["_score"] ?? null,
    ];

    $default_visible_fields = ["date"];

    /**
     * Filters the hit source mapping before transforming search hits.
     *
     * @param array $hit_source_mapping Mapping of result keys to callbacks.
     * @param array $es_body Elasticsearch request body.
     * @param array $data Ajax request data.
     * @return array Filtered hit source mapping.
     */
    $hit_source_mapping = apply_filters(
      "mx_search_hit_source_mapping",
      $hit_source_mapping,
      $es_body,
      $data,
    );

    $results = [
      "success" => true,
      "hits" => array_map(function ($hit) use (
        $es_results,
        $hit_source_mapping,
        $es_body,
        $default_visible_fields,
        $mx_search_settings_post_types,
        $data,
      ) {
        $post_type = $hit["_source"]["post_type"];
        $visible_fields =
          $mx_search_settings_post_types[$post_type]["visible_fields"] ??
          $default_visible_fields;
        $transformed_hit = array_combine(
          array_keys($hit_source_mapping),
          array_map(
            function ($fn, $field) use ($hit, $visible_fields) {
              return !in_array($field, ["date", "type"]) ||
                in_array($field, $visible_fields)
                ? $fn($hit)
                : null;
            },
            $hit_source_mapping,
            array_keys($hit_source_mapping),
          ),
        );

        /**
         * Filters a transformed Elasticsearch hit before it is returned.
         *
         * @param array $transformed_hit Transformed search hit.
         * @param array $hit Original Elasticsearch hit.
         * @param array $es_results Full Elasticsearch response.
         * @param array $es_body Elasticsearch request body.
         * @param array $data Ajax request data.
         * @return array Filtered transformed search hit.
         */
        return apply_filters(
          "mx_search_es_hit",
          $transformed_hit,
          $hit,
          $es_results,
          $es_body,
          $data,
        );
      }, $es_results["hits"]["hits"] ?? []),
      "total" => $total,
      "totalPages" => ceil($total / $data["hitsPerPage"]),
    ];

    /**
     * Filters the final AJAX search response before it is sent.
     *
     * @param array $results Search response.
     * @param array $es_results Full Elasticsearch response.
     * @return array Filtered search response.
     */
    $results = apply_filters("mx_search_results", $results, $es_results);

    wp_send_json($results);
  } catch (Exception $e) {
    /**
     * Filters whether search error logging is enabled.
     *
     * @since 2025.12.11
     *
     * @param bool $enabled Whether search errors should be logged.
     * @return bool Whether search errors should be logged.
     */
    if (apply_filters("mx_search_error_logging_enabled", WP_DEBUG && WP_DEBUG_LOG)) {
      // If WP_DEBUG_LOG is a string and belongs to a valid directory, use it as the log path. 
      // Otherwise, default to wp-content/debug.log
      $log_path = is_string(WP_DEBUG_LOG) && is_dir(dirname(WP_DEBUG_LOG))
        ? WP_DEBUG_LOG
        : WP_CONTENT_DIR . '/debug.log';

      /**
       * Filters the search error log file path.
       *
       * @since 2025.12.11
       *
       * @param string $log_path Search error log file path.
       * @return string Filtered search error log file path.
       */
      $log_path = apply_filters('mx_search_error_log_path', $log_path);

      // Log the error with a timestamp, site URL, search query, error message, file and line number.
      error_log(
        "[" . date("Y-m-d H:i:s") . "]" .
          " site=" . home_url() .
          " query=" . ($data["s"] ?? "(unknown)") .
          " error=" . $e->getMessage() .
          " in " . $e->getFile() . " on line " . $e->getLine() . PHP_EOL,
        3,
        $log_path,
      );
    }
    
    return wp_send_json([
      "success" => false,
      "error" => "An error occurred while searching.",
    ]);
  } finally {
    wp_die();
  }
}
add_action("wp_ajax_mx_search", "mx_search_ajax_handler");
add_action("wp_ajax_nopriv_mx_search", "mx_search_ajax_handler"); // For non-logged-in users

/**
 * Prepares posts for indexing.
 * - Adds a content_type field to the post_args array.
 * - Strips all HTML tags from the post_content_filtered field before indexing.
 * - Adds search_keywords field.
 */
add_filter(
  "ep_post_sync_args_post_prepare_meta",
  function ($post_args, $post_id) {
    /**
     * Filters the content type stored in the ElasticPress index.
     *
     * @param string $content_type Content type value.
     * @param array  $post_args Indexed post arguments.
     * @param int    $post_id Post ID.
     * @return string Filtered content type value.
     */
    $post_args["content_type"] = apply_filters(
      "mx_search_post_content_type",
      $post_args["post_type"],
      $post_args,
      $post_id,
    );

    /**
     * Filters the formatted content type stored in the ElasticPress index.
     *
     * @param string $label Formatted content type label.
     * @param array  $post_args Indexed post arguments.
     * @param int    $post_id Post ID.
     * @return string Filtered formatted content type label.
     */
    $post_args["content_type_formatted"] = apply_filters(
      "mx_search_post_content_type_formatted",
      get_post_type_object($post_args["post_type"])->labels->singular_name,
      $post_args,
      $post_id,
    );
    $post_args["post_content_filtered"] = mx_plain_text(
      $post_args["post_content_filtered"],
      ["exclude" => ".modularity-edit-module"],
    );
    $post_args["search_keywords"] = get_field("search_keywords", $post_id);
    return $post_args;
  },
  20,
  2,
);

/**
 * Adds stemming
 */
add_filter("ep_post_mapping", function ($mapping) {
  $mapping["settings"]["analysis"]["filter"]["swedish_stemmer"] = [
    "type" => "stemmer",
    "name" => "swedish",
  ];
  $mapping["settings"]["analysis"]["analyzer"]["default"]["filter"][] =
    "swedish_stemmer";

  // TODO: Can we do this without having to explicitly define the fields? What about attachments?
  $mapping["mappings"]["properties"]["post_title"]["fields"]["exact"] = [
    "type" => "text",
    "analyzer" => "default_search",
  ];
  $mapping["mappings"]["properties"]["post_content_filtered"]["fields"][
    "exact"
  ] = [
    "type" => "text",
    "analyzer" => "default_search",
  ];
  return $mapping;
});

/**
 * Adds a "Search" ACF field group to all indexable post types.
 */
add_action("acf/init", function () {
  $post_types = mx_get_searchable_post_types("names");
  if (empty($post_types)) {
    return;
  }

  acf_add_local_field_group([
    "key" => "group_search",
    "title" => __("Search", "municipio-extended"),
    "fields" => [
      [
        "key" => "field_search_keywords",
        "label" => __("Keywords", "municipio-extended"),
        "name" => "search_keywords",
        "type" => "textarea",
        "instructions" => __(
          "Seperate keywords with commas or new lines.",
          "municipio-extended",
        ),
        "rows" => 3,
        "new_lines" => "lf",
      ],
    ],
    "position" => "side",
    "location" => array_map(function ($post_type) {
      return [
        [
          "param" => "post_type",
          "operator" => "==",
          "value" => $post_type,
        ],
      ];
    }, $post_types),
  ]);
});

/**
 * Takes the standard EP checkbox for excluding from search into account.
 */
add_filter("mx_search_es_query", function ($query) {
  $query["bool"]["must_not"][] = [
    "terms" => [
      "meta.ep_exclude_from_search.raw" => ["1"],
    ],
  ];
  return $query;
});

/**
 * Filter out post types that have been excluded via settings.
 */
add_filter(
  "mx_search_es_query",
  function ($query, $data, $mx_search_settings_post_types) {
    $excluded_post_types = [];
    foreach ($mx_search_settings_post_types as $post_type => $settings) {
      if ($settings["search_excluded"] ?? false) {
        $excluded_post_types[] = $post_type;
      }
    }
    if (!empty($excluded_post_types)) {
      $query["bool"]["must_not"][] = [
        "terms" => [
          "post_type.raw" => $excluded_post_types,
        ],
      ];
    }
    return $query;
  },
  10,
  3,
);

/**
 * Filter documents by mime type
 */
add_filter("mx_search_es_query", function ($query) {
  /**
   * @var \ElasticPress\Feature\Documents\Documents $feature
   */
  $feature = \ElasticPress\Features::factory()->get_registered_feature(
    "documents",
  );
  $mime_types = $feature->get_allowed_ingest_mime_types();
  $mime_types[] = ""; // This let's us query non-attachments as well as attachments.

  $mime_types = array_unique(array_values($mime_types));

  $query["bool"]["must"][] = [
    "terms" => [
      "post_mime_type" => $mime_types,
    ],
  ];
  return $query;
});

/**
 * Removes unsupported features from the ElasticPress admin menu.
 */
add_action(
  "admin_menu",
  function () {
    $menu_slug =
      defined("EP_IS_NETWORK") &&
      EP_IS_NETWORK &&
      !Utils\is_top_level_admin_context()
        ? "elasticpress"
        : "elasticpress-weighting";
    remove_submenu_page("elasticpress", $menu_slug);

    remove_submenu_page("elasticpress", "edit.php?post_type=ep-pointer");

    // remove_submenu_page("elasticpress", "elasticpress-synonyms");
  },
  60,
);

/**
 * Hides the unsupported ep-pointer post type from wp-admin.
 */
add_action(
  "init",
  function () {
    $post_type = "ep-pointer";
    $post_type_object = get_post_type_object($post_type);
    if ($post_type_object) {
      $post_type_object->show_ui = false;
      $post_type_object->show_in_menu = false;
    }
  },
  20,
);

/**
 * Adds a field group to the acf-options-search page
 */
add_action(
  "init",
  function () {
    if (!function_exists("acf_add_local_field_group")) {
      return;
    }
    $post_types = mx_get_searchable_post_types("objects");
    if (empty($post_types)) {
      return;
    }

    acf_add_local_field_group([
      "key" => "group_mx_search_settings",
      "title" => __("Search settings", "municipio-extended"),
      "fields" => [
        [
          "key" => "field_mx_search_settings_enable_external_page_content_type",
          "label" => __(
            "Enable “external pages” content type",
            "municipio-extended",
          ),
          "name" => "mx_enable_external_page_content_type",
          "type" => "true_false",
          "ui" => 1,
          "default_value" => 0,
          "instructions" => __(
            "Check this box to enable the “external pages” content type which you can use to add external pages to the site search.",
            "municipio-extended",
          ),
        ],
        [
          "key" => "field_mx_search_settings_post_types",
          "label" => __("Post type settings", "municipio-extended"),
          "name" => "mx_search_settings_post_types",
          "type" => "group",
          "layout" => "horizontal",
          "sub_fields" => array_map(function ($post_type) {
            return [
              "key" => "field_mx_search_settings_post_types_{$post_type->name}",
              "label" => $post_type->label,
              "name" => $post_type->name,
              "type" => "group",
              "layout" => "horizontal",
              "sub_fields" => [
                [
                  "key" => "field_mx_search_settings_post_types_{$post_type->name}_search_excluded",
                  "label" => __("Exclude from search", "municipio-extended"),
                  "name" => "search_excluded",
                  "type" => "true_false",
                  "ui" => 1,
                  "default_value" => 0,
                  "wrapper" => [
                    "width" => "50%",
                  ],
                ],
                [
                  "key" => "field_mx_search_settings_post_types_{$post_type->name}_boost",
                  "label" => __("Boost", "municipio-extended"),
                  "name" => "boost",
                  "type" => "number",
                  "min" => 1,
                  "instructions" => __(
                    "The boost factor for this post type.",
                    "municipio-extended",
                  ),
                  "default_value" => 1,
                  "wrapper" => [
                    "width" => "50%",
                  ],
                  "conditional_logic" => [
                    "field" => "field_mx_search_settings_post_types_{$post_type->name}_search_excluded",
                    "operator" => "!=",
                    "value" => "1",
                  ],
                ],
                [
                  "key" => "field_mx_search_settings_post_types_{$post_type->name}_decay",
                  "label" => __("Decay", "municipio-extended"),
                  "name" => "decay",
                  "type" => "true_false",
                  "ui" => 1,
                  "instructions" => __(
                    "Whether older posts should have lower scores.",
                    "municipio-extended",
                  ),
                  "default_value" => 0,
                  "wrapper" => [
                    "width" => "50%",
                  ],
                  "conditional_logic" => [
                    "field" => "field_mx_search_settings_post_types_{$post_type->name}_search_excluded",
                    "operator" => "!=",
                    "value" => "1",
                  ],
                ],
                [
                  "key" => "field_mx_search_settings_post_types_{$post_type->name}_visible_fields",
                  "label" => __("Visible fields", "municipio-extended"),
                  "name" => "visible_fields",
                  "type" => "checkbox",
                  "choices" => [
                    "date" => _x(
                      "Date",
                      "Search Settings Visible Fields Choice",
                      "municipio-extended",
                    ),
                    "type" => _x(
                      "Type",
                      "Search Settings Visible Fields Choice",
                      "municipio-extended",
                    ),
                  ],
                  "instructions" => __(
                    "Which fields to show or hide in the search results.",
                    "municipio-extended",
                  ),
                  "default_value" => ["type"],
                  "wrapper" => [
                    "width" => "50%",
                  ],
                  "conditional_logic" => [
                    "field" => "field_mx_search_settings_post_types_{$post_type->name}_search_excluded",
                    "operator" => "!=",
                    "value" => "1",
                  ],
                ],
              ],
            ];
          }, $post_types),
        ],
      ],
      "location" => [
        [
          [
            "param" => "options_page",
            "operator" => "==",
            "value" => "acf-options-search",
          ],
        ],
      ],
    ]);
  },
  20,
);

/**
 * Register "External pages" post type and "Content type" taxonomy
 */
add_action("init", function () {
  register_post_type("external_page", [
    "label" => __("External pages", "municipio-extended"),
    "labels" => [
      "name" => __("External pages", "municipio-extended"),
      "singular_name" => __("External page", "municipio-extended"),
    ],
    "public" => true,
    "show_ui" => mx_enable_external_page_content_type(),
    "show_in_menu" => true,
    "show_in_nav_menus" => false,
    "show_in_admin_bar" => false,
    "show_in_rest" => false,
    "exclude_from_search" => false,
    "supports" => ["title", "editor"],
    "menu_icon" => "dashicons-admin-links",
  ]);
  register_taxonomy("external_page_content_type", "external_page", [
    "label" => __("Content type", "municipio-extended"),
    "labels" => [
      "name" => __("Content types", "municipio-extended"),
      "singular_name" => __("Content type", "municipio-extended"),
    ],
    "public" => false,
    "show_ui" => true,
    "show_in_menu" => true,
    "show_in_nav_menus" => false,
    "show_in_admin_bar" => false,
    "show_in_rest" => false,
    "hierarchical" => false,
    "multiple" => false,
  ]);
});

/**
 * Add "External page attributes" field group with required URL field
 */
add_action("acf/init", function () {
  acf_add_local_field_group([
    "key" => "group_external_page_attributes",
    "title" => __("External page attributes", "municipio-extended"),
    "fields" => [
      [
        "key" => "field_external_page_attributes_url",
        "label" => __("URL", "municipio-extended"),
        "name" => "url",
        "type" => "url",
        "instructions" => __(
          "The URL to the external page.",
          "municipio-extended",
        ),
        "required" => true,
      ],
    ],
    "location" => [
      [
        [
          "param" => "post_type",
          "operator" => "==",
          "value" => "external_page",
        ],
      ],
    ],
  ]);
});

/**
 * Use the URL field as the permalink for external pages
 */
add_filter(
  "post_type_link",
  function ($post_link, $post, $leavename, $sample) {
    if ($post && get_post_type($post) == "external_page") {
      $post_link = get_field("url", $post) ?: $post_link;
    }
    return $post_link;
  },
  10,
  4,
);

/**
 * Use the content type taxonomy as the content type for external pages
 */
add_filter(
  "mx_search_post_content_type",
  function ($content_type, $post_args, $post_id) {
    if ($post_args["post_type"] == "external_page") {
      $term_slug =
        get_the_terms($post_id, "external_page_content_type")[0]->slug ?? null;
      if ($term_slug ?? null) {
        $content_type .= ":" . $term_slug;
      }
    }
    return $content_type;
  },
  10,
  3,
);
add_filter(
  "mx_search_post_content_type_formatted",
  function ($content_type_formatted, $post_args, $post_id) {
    if ($post_args["post_type"] == "external_page") {
      $term_name =
        get_the_terms($post_id, "external_page_content_type")[0]->name ?? null;
      $content_type_formatted = $term_name ?? $content_type_formatted;
    }
    return $content_type_formatted;
  },
  10,
  3,
);

/**
 * Use the file type as the content type for attachments
 */
add_filter(
  "mx_search_post_content_type_formatted",
  function ($content_type_formatted, $post_args, $post_id) {
    if (
      $post_args["post_type"] == "attachment" &&
      !empty($post_args["post_mime_type"])
    ) {
      $content_type_formatted = _x(
        "Document",
        "Attachment Search Result Type Label",
        "municipio-extended",
      );
    }
    return $content_type_formatted;
  },
  10,
  3,
);
