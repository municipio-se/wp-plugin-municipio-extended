<?php

// add_filter("Modularity/Module/ManualInput/DefaultValues", function ($values) {
//   $values["link_text"] = "";
//   return $values;
// });

add_filter("Modularity/Display/mod-manualinput/viewData", function ($data) {
  if (!empty($data["manualInputs"]) && is_array($data["manualInputs"])) {
    foreach ($data["manualInputs"] as $index => $input) {
      $metaKey = "manual_inputs_{$index}_content";
      if (!empty($data["meta"][$metaKey][0])) {
        // Extract content from meta
        $rawContent = $data["meta"][$metaKey][0];
        // Clean and process the content
        $cleanContent = trim(mx_replace_builtin_classes($rawContent));
        $processedContent = apply_filters(
          "the_content",
          do_shortcode($cleanContent),
        );
        // Assign the processed content back to manualInputs
        $data["manualInputs"][$index]["content"] = $processedContent;
      }
    }
  }
  return $data;
});
