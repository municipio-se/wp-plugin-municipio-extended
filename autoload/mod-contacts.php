<?php

// XXX: This feature is disabled until it is finished

// add_filter(
//   "Modularity/Module/Template",
//   function ($template, $slug) {
//     if ($slug === "contacts") {
//       $template = "mod-contacts.blade.php";
//     }
//     return $template;
//   },
//   10,
//   2,
// );

// // Modify social media choices to change Twitter to X
// add_filter("acf/load_field", function ($field) {
//   if ($field["key"] === "field_5bf6a737c1b6c") {
//     if (isset($field["choices"]["twitter"])) {
//       $field["choices"]["x"] = "X";
//       unset($field["choices"]["twitter"]);
//     }
//   }
//   return $field;
// });
