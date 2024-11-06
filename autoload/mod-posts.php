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

// add_filter("Modularity/Display/mod-posts/viewData", function ($data) {
//   $data["lang"]["readMore"] = "";
//   return $data;
// });
