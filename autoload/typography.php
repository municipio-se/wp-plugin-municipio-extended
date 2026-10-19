<?php

use Kirki\Compatibility\Kirki;

add_action("init", function () {
  $section_id = "municipio_customizer_section_typography";

  Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
    "type" => "color",
    "settings" => "page_title_color",
    "label" => __("Page title color", "municipio-extended"),
    "description" => __(
      "If no color is selected, the primary color will be used.",
      "municipio-extended",
    ),
    "section" => $section_id,
    "priority" => 10,
    "default" => null,
    "output" => [
      [
        "element" => ":root",
        "property" => "--page-title-color",
      ],
    ],
  ]);

  Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
    "type" => "color",
    "settings" => "quote_background_color",
    "label" => __("Quote background color", "municipio-extended"),
    "description" => __(
      "If no color is selected, the light secondary color will be used.",
      "municipio-extended",
    ),
    "section" => $section_id,
    "priority" => 100,
    "default" => null,
    "output" => [
      [
        "element" => ":root",
        "property" => "--quote-background-color",
      ],
    ],
  ]);
});

add_action(
  "init",
  function () {
    if (class_exists(\Municipio\Customizer::class)) {
      add_filter(
        "kirki_" . \Municipio\Customizer::KIRKI_CONFIG . "_styles",
        "mx_relative_base_font_size",
        20,
      );
    }
  },
  20,
);

/**
 * Run after the theme's priority-10 conversion: other typography values still
 * need its original pixel base. Only the resulting base uses the browser root.
 */
function mx_relative_base_font_size($styles) {
  $value = $styles["global"][":root"]["--font-size-base"] ?? null;
  if (
    !is_string($value) ||
    !preg_match('/^\s*(\d+(?:\.\d+)?|\.\d+)px\s*$/i', $value, $matches)
  ) {
    return $styles;
  }

  // 16 is the reference default, not the configured base or current user choice.
  $styles["global"][":root"]["--font-size-base"] =
    (float) $matches[1] / 16 . "rem";

  return $styles;
}
