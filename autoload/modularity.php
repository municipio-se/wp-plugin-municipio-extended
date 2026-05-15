<?php

add_filter(
  "Modularity/Display/BeforeModule",
  function ($beforeModule, $args, $postType, $postId) {
    if (preg_match("/^(.*?)>(.*)$/s", $beforeModule, $matches)) {
      /**
       * Filters attributes added to the outer Modularity module wrapper.
       *
       * @param array $attrs    Wrapper attributes.
       * @param array $args     Module display arguments.
       * @param string $postType Module post type.
       * @param int   $postId   Module post ID.
       * @return array Filtered wrapper attributes.
       */
      $attrs = apply_filters(
        "mx/module_wrapper_attrs",
        [],
        $args,
        $postType,
        $postId,
      );
      $beforeModule = $matches[1] . mx_attrs($attrs) . ">" . $matches[2];
    }
    return $beforeModule;
  },
  10,
  4,
);

function mx_module_groups_enabled() {
  return get_field("mx_module_groups_enabled", "options") ?? false;
}

function mx_group_modules($modules, $ignore_backgrounds = false) {
  $groups = [];
  $i = -1;
  foreach ($modules as $key => $module) {
    $background = $ignore_backgrounds
      ? ""
      : (is_array($module)
        ? $module["background"] ?? ""
        : $module->background ?? "");
    if ($i === -1 || $groups[$i]["background"] !== $background) {
      $i++;
      $groups[$i] = [
        "background" => $background,
        "modules" => [],
      ];
    }
    $groups[$i]["modules"][$key] = $module;
  }
  return $groups;
}

function mx_flatten_module_groups($groups) {
  $modules = [];
  $i = 0;
  $boundaries = [];
  foreach ($groups as $group) {
    $boundary = (object) [
      "ID" => -1,
      "post_type" => "mod-group-boundary",
      "group_idx" => $i++,
      "background" => $group["background"],
    ];
    $modules[] = $boundary;
    $boundaries[] = $boundary;
    foreach ($group["modules"] as $module) {
      $modules[] = $module;
    }
  }
  $boundary = (object) [
    "ID" => -1,
    "post_type" => "mod-group-boundary",
    "group_idx" => $i,
    "background" => $group["background"],
  ];
  $modules[] = $boundary;
  $boundaries[] = $boundary;
  foreach ($boundaries as $key => $boundary) {
    $boundary->is_first_group_boundary = $key === 0;
    $boundary->is_last_group_boundary = $key === count($boundaries) - 1;
  }
  return $modules;
}

add_action(
  "Modularity/Editor/getModule",
  function ($module, $args) {
    if (isset($args["background"])) {
      $module->background = $args["background"];
    }
  },
  10,
  2,
);

function mx_get_module_group_background_options() {
  return [
    "" => __("No background", "municipio-extended"),
    "complementary" => __("Complementary color", "municipio-extended"),
    "secondary" => __("Secondary color", "municipio-extended"),
    "white" => __("White", "municipio-extended"),
    "neutral" => __("Neutral color", "municipio-extended"),
    "card" => __("Card color", "municipio-extended"),
    "background" => __("Background color", "municipio-extended"),
  ];
}

function mx_get_module_group_background_class($value) {
  $classes = [
    "" => "mx-module-group--bg-transparent",
    "complementary" => "mx-module-group--bg-complementary layer-white",
    "secondary" => "mx-module-group--bg-secondary layer-white",
    "white" => "mx-module-group--bg-white",
    "neutral" => "mx-module-group--bg-neutral layer-white",
    "card" => "mx-module-group--bg-card layer-background",
    "background" => "mx-module-group--bg-background",
  ];
  $class = $classes[$value] ?? $classes[""];
  return $class;
}

function mx_sidebar_supports_module_group_backgrounds($sidebar) {
  $unsupported_sidebars = ["left-sidebar", "right-sidebar"];
  return !in_array($sidebar, $unsupported_sidebars);
}

function mx_should_ignore_module_group_backgrounds(
  $sidebar,
  $context,
  $visible_sidebars,
) {
  // Ignore backgrounds in sidebars that do not support it
  $ignore_backgrounds = !mx_sidebar_supports_module_group_backgrounds($sidebar);
  // Ignore backgrounds in content area if there are sidebars visible
  if (
    $sidebar === "content-area" &&
    (in_array("right-sidebar", $visible_sidebars) ||
      in_array("left-sidebar", $visible_sidebars))
  ) {
    $ignore_backgrounds = true;
  }

  /**
   * Filters whether module group backgrounds should be ignored.
   *
   * @param bool   $ignore_backgrounds Whether backgrounds should be ignored.
   * @param string $sidebar            Sidebar ID.
   * @param mixed  $context            Modularity display context.
   * @param array  $visible_sidebars   Visible sidebar IDs.
   * @return bool Whether backgrounds should be ignored.
   */
  $ignore_backgrounds = apply_filters(
    "mx_should_ignore_module_group_backgrounds",
    $ignore_backgrounds,
    $sidebar,
    $context,
    $visible_sidebars,
  );
  return $ignore_backgrounds;
}

add_action("acf/init", function () {
  acf_add_local_field_group([
    "key" => "group_mx_theme_options_modularity",
    "title" => "Moduler",
    "fields" => [
      [
        "key" => "field_mx_theme_options_modularity_module_groups_enabled",
        "label" => "Aktivera modulgrupper",
        "name" => "mx_module_groups_enabled",
        "type" => "true_false",
        "ui" => 1,
      ],
    ],
    "location" => [
      [
        [
          "param" => "options_page",
          "operator" => "==",
          "value" => "acf-options-temainstallningar",
        ],
      ],
    ],
    "label_placement" => "left",
    "instruction_placement" => "label",
  ]);
});

add_filter(
  "Modularity/Display/modules",
  function ($modules, $options, $context) {
    if (!mx_module_groups_enabled()) {
      return $modules;
    }
    $visible_sidebars = array_keys($modules);
    $modules = array_combine(
      array_keys($modules),
      array_map(
        function ($sidebar, $sidebar_id) use ($context, $visible_sidebars) {
          $sidebar["modules"] = mx_flatten_module_groups(
            mx_group_modules(
              $sidebar["modules"],
              ignore_backgrounds: mx_should_ignore_module_group_backgrounds(
                $sidebar_id,
                $context,
                $visible_sidebars,
              ),
            ),
          );
          return $sidebar;
        },
        $modules,
        array_keys($modules),
      ),
    );
    return $modules;
  },
  10,
  3,
);

add_filter(
  "Modularity/Display/pre_outputModule",
  function ($output, $module, $sidebar, $moduleSettings) {
    if (!mx_module_groups_enabled()) {
      return $output;
    }
    if ($module->post_type === "mod-group-boundary") {
      $output = "";
      if (!$module->is_first_group_boundary) {
        $output .= "</div>";
      }
      if (!$module->is_last_group_boundary) {
        $output .=
          "<div " .
          mx_attrs([
            "class" => clsx([
              "mx-module-group",
              mx_get_module_group_background_class($module->background),
              "o-grid",
            ]),
          ]) .
          ">";
      }
    }
    return $output;
  },
  10,
  4,
);

add_filter("Municipio/views/partials/sidebar/classes", function ($classes) {
  if (!mx_module_groups_enabled()) {
    return $classes;
  }
  $classes = array_diff($classes, ["o-grid"]);
  return $classes;
});
