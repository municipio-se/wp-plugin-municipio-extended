<?php

add_filter("Modularity/Display/mod-text/viewData", function ($data) {
  if (!empty($data["post_content"])) {
    $data["post_content"] = wpautop($data["post_content"]);
    $data["post_content"] = do_shortcode($data["post_content"]);
    $data["post_content"] = mx_replace_builtin_classes($data["post_content"]);
  }

  if (mx_mod_text_use_color_presets()) {
    $presets = mx_mod_text_box_color_presets();
    $preset = $data["box_color_preset"] ?? "";
    $data["box_color"] = $presets[$preset]["color"] ?? "";
  }

  return $data;
});

function mx_replace_builtin_classes($content) {
  return str_replace(
    [
      //Old inline transition button
      "btn-theme-first",
      "btn-theme-second",
      "btn-theme-third",
      "btn-theme-fourth",
      "btn-theme-fifth",

      //Gutenberg block image
      "wp-block-image",
      "wp-element-caption",
      "<figcaption>",
    ],
    [
      //Old inline transition button
      "c-button c-button__filled c-button__filled--primary c-button--md",
      "c-button c-button__filled c-button__filled--secondary c-button--md",
      "c-button c-button__filled c-button__filled--secondary c-button--md",
      "c-button c-button__filled c-button__filled--secondary c-button--md",
      "c-button c-button__filled c-button__filled--secondary c-button--md",

      //Gutenberg block image
      "c-image",
      "c-image__caption",
      '<figcaption class="c-image__caption">',
    ],
    $content,
  );
}

function mx_process_content($content, $options = []) {
  if (!($options["wpautop"] ?? false)) {
    remove_filter("the_content", "wpautop");
  }
  $content = mx_replace_builtin_classes($content);
  $content = trim($content);
  $content = apply_filters("the_content", $content);
  $content = do_shortcode($content);
  if (!($options["wpautop"] ?? false)) {
    add_filter("the_content", "wpautop");
  }
  return $content;
}

/**
 * Whether the text module should use the preset dropdown instead of the
 * legacy color picker.
 *
 * Off by default to preserve existing behavior. Sites opt in via the
 * MUNICIPIO_EXTENDED_MOD_TEXT_USE_COLOR_PRESETS constant or the
 * mx_mod_text_use_color_presets filter.
 */
function mx_mod_text_use_color_presets(): bool {
  $enabled =
    defined("MUNICIPIO_EXTENDED_MOD_TEXT_USE_COLOR_PRESETS") &&
    constant("MUNICIPIO_EXTENDED_MOD_TEXT_USE_COLOR_PRESETS");

  /**
   * Filters whether text modules use palette-backed color presets.
   *
   * @since 2025.12.12
   *
   * @param bool $enabled Whether the preset dropdown replaces the legacy color picker.
   * @return bool Filtered preset dropdown state.
   */
  return (bool) apply_filters("mx_mod_text_use_color_presets", $enabled);
}

/**
 * Predefined background colors for the text module box.
 *
 * Defaults are resolved from the active project's Municipio color palette, so
 * each site automatically gets its own brand colors. Projects can add, remove
 * or recolor presets through the filter.
 *
 * @return array<string, array{label: string, color: string}>
 */
function mx_mod_text_box_color_presets(): array {
  $sources = [
    "primary" => ["color_palette_primary", "base"],
    "secondary" => ["color_palette_secondary", "base"],
    "complementary" => ["color_palette_complement", "default"],
    "info" => ["color_palette_state_info", "base"],
    "success" => ["color_palette_state_success", "base"],
    "warning" => ["color_palette_state_warning", "base"],
    "danger" => ["color_palette_state_danger", "base"],
    "white" => ["color_palette_monotone", "white"],
    "neutral" => ["color_palette_monotone", "light"],
    "card" => ["color_card", "background"],
    "background" => ["color_background", "background"],
  ];

  $labels = [
    "primary" => __("Primary", "municipio-extended"),
    "secondary" => __("Secondary", "municipio-extended"),
    "complementary" => __("Complementary", "municipio-extended"),
    "info" => __("Info", "municipio-extended"),
    "success" => __("Success", "municipio-extended"),
    "warning" => __("Warning", "municipio-extended"),
    "danger" => __("Danger", "municipio-extended"),
    "white" => __("White", "municipio-extended"),
    "neutral" => __("Neutral", "municipio-extended"),
    "card" => __("Card", "municipio-extended"),
    "background" => __("Background", "municipio-extended"),
  ];

  $palettes = class_exists(\Municipio\Helper\Color::class)
    ? \Municipio\Helper\Color::getPalettes(
      array_values(array_unique(array_column($sources, 0))),
    )
    : [];

  $presets = [];
  foreach ($sources as $key => [$option, $subKey]) {
    $presets[$key] = [
      "label" => $labels[$key],
      "color" => $palettes[$option][$subKey] ?? null,
    ];
  }

  // Drop presets that have no resolvable color on this project.
  $presets = array_filter($presets, fn($preset) => !empty($preset["color"]));

  /**
   * Filters palette-backed color presets for text modules.
   *
   * @since 2025.12.12
   *
   * @param array $presets Text module color presets keyed by preset ID.
   * @return array Filtered text module color presets.
   */
  return apply_filters("mx_mod_text_box_color_presets", $presets);
}

add_action("acf/init", function () {
  if (mx_mod_text_use_color_presets()) {
    acf_add_local_field([
      "key" => "field_mod_text_box_color_preset",
      "label" => __("Text box color", "municipio-extended"),
      "name" => "box_color_preset",
      "aria-label" => "",
      "type" => "select",
      "instructions" => "",
      "required" => 0,
      "conditional_logic" => 0,
      "wrapper" => [
        "width" => "",
        "class" => "",
        "id" => "",
      ],
      "choices" => [],
      "default_value" => "",
      "return_format" => "value",
      "allow_null" => 1,
      "multiple" => 0,
      "ui" => 0,
      "ajax" => 0,
      "placeholder" => __("None", "municipio-extended"),
      "parent" => "group_5891b49127038",
    ]);
    return;
  }

  acf_add_local_field([
    "key" => "field_mod_text_box_color",
    "label" => __("Text box color", "municipio-extended"),
    "name" => "box_color",
    "aria-label" => "",
    "type" => "color_picker",
    "instructions" => "",
    "required" => 0,
    "conditional_logic" => 0,
    "wrapper" => [
      "width" => "",
      "class" => "",
      "id" => "",
    ],
    "default_value" => 0,
    "ui_on_text" => "",
    "ui_off_text" => "",
    "ui" => 0,
    "parent" => "group_5891b49127038",
  ]);
});

// Populate the dropdown from the (per-project filterable) presets.
add_filter("acf/load_field/key=field_mod_text_box_color_preset", function (
  $field,
) {
  $choices = [];
  foreach (mx_mod_text_box_color_presets() as $key => $preset) {
    $choices[$key] = $preset["label"];
  }
  $field["choices"] = $choices;
  return $field;
});

// Default new text modules to article style and clarify the wording.
add_filter("acf/load_field/key=field_5891b6038c120", function ($field) {
  $field["label"] = __("Display style", "municipio-extended");
  $field["message"] = __(
    "Show the text as an article (without a box)",
    "municipio-extended",
  );
  $field["default_value"] = 1;
  return $field;
});
