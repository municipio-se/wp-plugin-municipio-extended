<?php

$hooks = [];
function add_filter($name, $callback, $priority = 10) {
  global $hooks;
  $hooks[$name][$priority][] = $callback;
}
function add_action($name, $callback, $priority = 10) {
  add_filter($name, $callback, $priority);
}
function expect($actual, $expected, $message) {
  if ($actual !== $expected) {
    throw new RuntimeException($message . ": " . var_export($actual, true));
  }
}

require dirname(__DIR__) . "/autoload/typography.php";

$cases = [
  ["20px", "1.25rem"],
  ["18px", "1.125rem"],
  ["16px", "1rem"],
  ["20.5px", "1.28125rem"],
  [" .5PX ", "0.03125rem"],
  ["0px", "0rem"],
  ["1.25rem", "1.25rem"],
  ["125%", "125%"],
  ["1.25em", "1.25em"],
  ["calc(16px + 1rem)", "calc(16px + 1rem)"],
  ["var(--custom-size)", "var(--custom-size)"],
  ["invalid", "invalid"],
  ["20px; color:red", "20px; color:red"],
  [false, false],
  [null, null],
];

foreach ($cases as [$value, $expected]) {
  $styles = [
    "global" => [
      ":root" => ["--font-size-base" => $value, "--font-size-h1" => "2rem"],
    ],
  ];
  $result = mx_relative_base_font_size($styles);
  $styles["global"][":root"]["--font-size-base"] = $expected;
  expect($result, $styles, "Only the base value may change");
  expect(
    mx_relative_base_font_size($result),
    $result,
    "Conversion must be idempotent",
  );
}
foreach ([[], ["global" => []], ["global" => [":root" => []]]] as $styles) {
  expect(
    mx_relative_base_font_size($styles),
    $styles,
    "Absent settings must stay absent",
  );
}

// Use the actual dependency to catch regressions in conversion order.
require dirname(__DIR__) . "/vendor/municipio/municipio/library/Customizer.php";
require dirname(__DIR__) .
  "/vendor/municipio/municipio/library/Customizer/Applicators/Css.php";
$theme = new Municipio\Customizer\Applicators\Css();
foreach ($hooks["init"][20] as $callback) {
  $callback();
}
$name = "kirki_" . Municipio\Customizer::KIRKI_CONFIG . "_styles";
expect(
  in_array("mx_relative_base_font_size", $hooks[$name][20], true),
  true,
  "Register after the theme's priority 10",
);
$styles = [
  "global" => [
    ":root" => [
      "--font-size-base" => "20px",
      "--font-size-h1" => "40px",
      "--font-size-lead" => "24px",
      "--c-slider-item-font-size-base" => "24px",
    ],
  ],
];
$themeResult = $theme->filterFontSize($styles);
$result = mx_relative_base_font_size($themeResult);
expect(
  $themeResult["global"][":root"]["--font-size-h1"],
  "2rem",
  "Theme still uses its configured pixel base",
);
$themeResult["global"][":root"]["--font-size-base"] = "1.25rem";
expect($result, $themeResult, "Preserve every other typography value");
expect(
  (float) $result["global"][":root"]["--font-size-base"] * 16,
  20.0,
  "Default root preserves size",
);
expect(
  (float) $result["global"][":root"]["--font-size-base"] * 24,
  30.0,
  "Larger root scales base",
);

echo "Typography conversion, missing values, idempotence and theme-order checks passed.\n";
