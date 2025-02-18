<?php

$mods = get_posts([
  "post_type" => "mod-posts",
  "posts_per_page" => -1,
  "meta_query" => [
    [
      "key" => "posts_taxonomy_type",
      "operator" => "EXISTS",
    ],
  ],
]);

$total = count($mods);
$count = 0;

foreach ($mods as $mod) {
  mx_migration_breakpoint(function () use ($count, $total) {
    mx_migration_progress_log(
      "$count of $total taxonomy filters on Posts modules improved",
    );
  });
  if (get_field("mod_posts_filtering", $mod->ID)) {
    continue;
  }
  $taxonomy_name = get_field("posts_taxonomy_type", $mod->ID);
  $term_slug = get_field("posts_taxonomy_value", $mod->ID);
  $term = get_term_by("slug", $term_slug, $taxonomy_name);
  $result = add_row(
    "mod_posts_filtering",
    [
      "taxonomy" => $taxonomy_name,
      "operator" => "IN",
      "term_{$taxonomy_name}" => $term->term_id,
    ],
    $mod->ID,
  );
  // $result = update_field(
  //   "field_mod_posts_filtering",
  //   [
  //     [
  //       "field_mod_posts_filtering_taxonomy" => $taxonomy_name,
  //       "field_mod_posts_filtering_operator" => "IN",
  //       "field_mod_posts_filtering_term_{$taxonomy_name}" => $term->term_id,
  //     ],
  //   ],
  //   $mod->ID,
  // );
  error_log(var_export($result, true));
  // update_field(
  //   "mod_posts_filtering",
  //   [
  //     "taxonomy" => $taxonomy_name,
  //     "operator" => "IN",
  //     $term_field => $term_id,
  //   ],
  //   $mod->ID,
  // );
  // update_field("mod_posts_filtering_0_taxonomy", $term, $mod->ID);
}
