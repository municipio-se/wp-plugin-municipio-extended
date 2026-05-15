<?php

add_filter(
  "Municipio/Customizer/Sections/Archive/archiveStyleChoices",
  function ($choices) {
    $choices["table"] = __("Table", "municipio-extended");
    return $choices;
  },
);

add_action(
  "Municipio/Customizer/Sections/Archive/init",
  function ($section_id, $archive) {
    $choices = array_map(function ($meta_field) {
      /**
       * Filters the display label for archive meta fields.
       *
       * @param string $label      Generated meta field label.
       * @param string $meta_field Meta field key.
       * @return string Filtered meta field label.
       */
      $label = apply_filters(
        "mx/meta_field/label",
        ucfirst(preg_replace("/_/", " ", $meta_field)),
        $meta_field,
      );
      if ($label != $meta_field) {
        $label .= " ($meta_field)";
      }
      return $label;
    }, $archive->dateSource ?? []);
    Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
      "type" => "select",
      "settings" => "archive_" . $archive->name . "_metas_to_display",
      "label" => esc_html__("Meta display", "municipio"),
      // 'description' => esc_html__('What meta fields', 'municipio'),
      "multiple" => 4,
      "section" => $section_id,
      "choices" => $choices,
      "sanitize_callback" => fn($values) => $values,
      "output" => [
        [
          "type" => "controller",
          "as_object" => true,
        ],
      ],
    ]);
  },
  10,
  2,
);

add_filter("Municipio/Helper/Post/postObject", function ($postObject) {
  $fields = get_theme_mod(
    "archive_" . get_post_type($postObject->ID) . "_metas_to_display",
    false,
  );

  $values = [];
  if (is_array($fields) && !empty($fields)) {
    foreach ($fields as $field) {
      $value = get_post_meta($postObject->ID, $field, true);
      if (!empty($value)) {
        $item = [];
        $item["value"] = $value;
        $item["field"] = $field;
        $values[] = $item;
      }
    }
  }
  $postObject->metaValues = $values;
  return $postObject;
});
