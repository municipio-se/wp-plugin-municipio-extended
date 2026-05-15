<?php

use Municipio\Customizer;
use Kirki\Compatibility\Kirki;

/**
 * Adds a customizer section for MXUI components.
 */
add_action("init", function () {
  $section_id = "municipio_customizer_section_component_card";

  Kirki::add_section($section_id, [
    "panel" => "municipio_customizer_panel_design_component",
    "title" => __("Cards", "municipio-extended"),
    "priority" => 170,
  ]);

  Kirki::add_field(Customizer::KIRKI_CONFIG, [
    "section" => $section_id,
    "type" => "checkbox",
    "settings" => "card_mxui_enabled",
    "label" => __("Use MXUI version", "municipio-extended"),
    "default" => false,
  ]);

  $section_id = "municipio_customizer_section_component_segment";

  Kirki::add_section($section_id, [
    "panel" => "municipio_customizer_panel_design_component",
    "title" => __("Segments", "municipio-extended"),
    "priority" => 170,
  ]);

  $section_id = "municipio_customizer_section_header";

  Kirki::add_field(Customizer::KIRKI_CONFIG, [
    "section" => $section_id,
    "type" => "select",
    "settings" => "main_menu_style",
    "label" => __("Style for main menu", "municipio-extended"),
    "default" => "standard",
    "priority" => 10,
    "choices" => [
      "standard" => __("Standard", "municipio-extended"),
      "compact" => __("Compact", "municipio-extended"),
    ],
    "output" => [["type" => "controller"]],
  ]);

  Kirki::add_field(Customizer::KIRKI_CONFIG, [
    "section" => $section_id,
    "type" => "select",
    "settings" => "tab_menu_placing",
    "label" => __("Placing for tab menu", "municipio-extended"),
    "default" => "default",
    "priority" => 10,
    "choices" => [
      "default" => __("Standard", "municipio-extended"),
      "above" => __("Above", "municipio-extended"),
    ],
    "output" => [["type" => "controller"]],
  ]);

  Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
    "section" => $section_id,
    "type" => "select",
    "settings" => "tab_menu_button_size",
    "label" => __("Tab menu button size", "municipio-extended"),
    "default" => "md",
    "priority" => 10,
    "choices" => [
      "sm" => __("Small", "municipio-extended"),
      "md" => __("Medium", "municipio-extended"),
      "lg" => __("Large", "municipio-extended"),
    ],
    "output" => [["type" => "controller"]],
  ]);

  $section_id = "municipio_customizer_panel_content_types_page";

  Kirki::add_field(Customizer::KIRKI_CONFIG, [
    "section" => $section_id,
    "type" => "checkbox",
    "settings" => "section_start_page_enabled",
    "label" => __("Section start page", "municipio-extended"),
    "default" => false,
  ]);
});

function mx_get_fallback_image() {
  $logotypeEmblem = Kirki::get_option(
    Customizer::KIRKI_CONFIG,
    "logotype_emblem",
  );
  return $logotypeEmblem;
}

/**
 * Overrides the default way of setting placeholder images on cards.
 */
function mxui_component_data_emblem_filter_cb($data) {
  if (!empty($data["hasPlaceholder"]) && $data["hasPlaceholder"] === true) {
    if (!is_array($data["image"])) {
      $data["image"] = [];
    }
    $logotypeEmblem = mx_get_fallback_image();
    if ($logotypeEmblem) {
      $data["image"]["src"] = $logotypeEmblem;
    } else {
      $data["image"] = null;
    }
    $data["hasPlaceholder"] = false; // Prevents next filter from changing this
  }
  return $data;
}
add_filter(
  "ComponentLibrary/Component/Card/Data",
  "mxui_component_data_emblem_filter_cb",
  9, // Run before Municipio default filter
  1,
);
add_filter(
  "ComponentLibrary/Component/Block/Data",
  "mxui_component_data_emblem_filter_cb",
  9, // Run before Municipio default filter
  1,
);
add_filter(
  "ComponentLibrary/Component/Segment/Data",
  "mxui_component_data_emblem_filter_cb",
  9, // Run before Municipio default filter
  1,
);

add_action("municipio_customizer_section_registered", function ($section) {
  // Check if this is the desired section
  if (
    strpos($section->getID(), "municipio_customizer_panel_content_types") !==
    false
  ) {
    $sectionId = $section->getID();

    // Get the post type from the section ID
    $postType = str_replace(
      "municipio_customizer_panel_content_types_",
      "",
      $sectionId,
    );

    // Fetch taxonomies for the post type
    $taxonomies = get_object_taxonomies($postType, "objects");

    $taxonomyChoices = [];
    foreach ($taxonomies as $taxonomy) {
      if ($taxonomy->public) {
        $taxonomyChoices[$taxonomy->name] = $taxonomy->label;
      }
    }

    // Add the taxonomy field
    if (!empty($taxonomyChoices)) {
      Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
        "type" => "multicheck",
        "settings" => $sectionId . "_taxonomies",
        "label" => esc_html__("Display taxonomies", "municipio-extended"),
        "description" => esc_html__(
          "Select which taxonomies to display for this post type.",
          "municipio-extended",
        ),
        "section" => $sectionId,
        "default" => array_keys($taxonomyChoices),
        "choices" => $taxonomyChoices,
      ]);
    }

    // Add the placement field
    Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
      "type" => "select",
      "settings" => $sectionId . "_taxonomy_placement",
      "label" => esc_html__("Taxonomy placement", "municipio-extended"),
      "description" => esc_html__(
        "Select where to display taxonomy terms for this post type.",
        "municipio-extended",
      ),
      "section" => $sectionId,
      "default" => "under_header",
      "choices" => [
        "under_header" => esc_html__("Under header", "municipio-extended"),
        "after_content" => esc_html__("After content", "municipio-extended"),
      ],
    ]);
  }
});

/**
 * Adds a customizer section for MXUI components.
 */
add_action("init", function () {
  $section_id = "municipio_customizer_section_search";

  Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
    "section" => $section_id,
    "type" => "text",
    "settings" => "hero_search_placeholder",
    "label" => __("Hero search placeholder", "municipio-extended"),
    "default" => "",
    "priority" => 10,
    "output" => [["type" => "controller"]],
  ]);
});

/**
 * Filter to modify the hero search placeholder.
 */
add_filter("Municipio/Search/Hero_search_placeholder", function ($placeholder) {
  // Fetch the custom placeholder from the Customizer
  $customPlaceholder = get_theme_mod("hero_search_placeholder", "");

  // Return the custom placeholder or default
  return !empty($customPlaceholder) ? $customPlaceholder : $placeholder;
});

function mxui_debug_enabled() {
  /**
   * Filters whether MXUI debug output is enabled.
   *
   * @param bool $enabled Whether debug output is enabled.
   * @return bool Whether debug output is enabled.
   */
  return apply_filters(
    "mxui/debug_enabled",
    defined("MXUI_DEBUG") ? constant("MXUI_DEBUG") : false,
  );
}

add_filter(
  "Municipio/Content/ImageNormalized",
  function ($normalized, \DOMElement $image) {
    if ($image->getAttribute("data-mxui-type") === "image") {
      return true;
    }
    return $normalized;
  },
  10,
  2,
);

add_action(
  "customize_register",
  function (\WP_Customize_Manager $wp_customize) {
    $control = $wp_customize->get_control("business_header_alignment");

    if ($control instanceof \WP_Customize_Control) {
      $control->choices["business-center"] = esc_html__("Center", "municipio");
    }
  },
  999,
);
