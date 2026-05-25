<?php

add_filter("Modularity/Display/mod-text/viewData", function ($data) {
  if (!empty($data["post_content"])) {
    $data["post_content"] = wpautop($data["post_content"]);
    $data["post_content"] = do_shortcode($data["post_content"]);
    $data["post_content"] = mx_replace_builtin_classes($data["post_content"]);
  }

  $presets = mx_mod_text_box_color_presets();
  $preset = $data["box_color_preset"] ?? "";
  $data["box_color"] = $presets[$preset]["color"] ?? "";

  return $data;
});

function mx_replace_builtin_classes($content) {
  return str_replace(
    [
      // Old inline transition button
      "btn-theme-first",
      "btn-theme-second",
      "btn-theme-third",
      "btn-theme-fourth",
      "btn-theme-fifth",

      // Gutenberg block image
      "wp-block-image",
      "wp-element-caption",
      "<figcaption>",
    ],
    [
      // Old inline transition button
      "c-button c-button__filled c-button__filled--primary c-button--md",
      "c-button c-button__filled c-button__filled--secondary c-button--md",
      "c-button c-button__filled c-button__filled--secondary c-button--md",
      "c-button c-button__filled c-button__filled--secondary c-button--md",
      "c-button c-button__filled c-button__filled--secondary c-button--md",

      // Gutenberg block image
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
 * Predefined background colors for the text module box.
 *
 * Defaults are resolved from the active project's Municipio color palette, so
 * each site automatically gets its own brand colors. Projects can add, remove
 * or recolor presets through the filter.
 *
 * @return array<string, array{label: string, color: string}>
 */
function mx_mod_text_box_color_presets(): array {
  $palettes = class_exists(\Municipio\Helper\Color::class)
    ? \Municipio\Helper\Color::getPalettes()
    : [];

  $base = function (string $option) use ($palettes): ?string {
    return $palettes[$option]["base"] ?? null;
  };

  $presets = [
    "primary" => [
      "label" => __("Primary", "municipio-extended"),
      "color" => $base("color_palette_primary"),
    ],
    "secondary" => [
      "label" => __("Secondary", "municipio-extended"),
      "color" => $base("color_palette_secondary"),
    ],
    "complement" => [
      "label" => __("Complement", "municipio-extended"),
      "color" => $base("color_palette_complement"),
    ],
  ];

  // Drop presets that have no resolvable color on this project.
  $presets = array_filter($presets, fn($preset) => !empty($preset["color"]));

  return apply_filters("MunicipioExtended/ModText/BoxColorPresets", $presets);
}

add_action("acf/init", function () {
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
});

// Populate the dropdown from the (per-project filterable) presets.
add_filter("acf/load_field/key=field_mod_text_box_color_preset", function (
  $field
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
