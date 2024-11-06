<?php

// add_filter("Modularity/Module/ManualInput/DefaultValues", function ($values) {
//   $values["link_text"] = "";
//   return $values;
// });

add_filter("Modularity/Display/mod-manualinput/viewData", function ($data) {
  if (!empty($data["manualInputs"]) && is_array($data["manualInputs"])) {
    foreach ($data["manualInputs"] as $index => $input) {
      if (!empty($input["content"])) {
        $data["manualInputs"][$index]["content"] = mx_replace_builtin_classes(
          $input["content"],
        );
      }
    }
  }
  return $data;
});
