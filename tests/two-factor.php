<?php

$admin = ($argv[1] ?? "") === "admin";
$editor = ($argv[2] ?? "") === "editor";
$multisite = ($argv[3] ?? "") === "multisite";
define("TWO_FACTOR_ENFORCE_ADMIN", $admin);
define("TWO_FACTOR_ENFORCE_EDITOR", $editor);
class Two_Factor_Email {
}
function add_action($name, $callback) {
  $callback();
}
function add_filter($name, $callback, $priority, $args) {
  $GLOBALS["policy"] = $callback;
}
function is_multisite() {
  return $GLOBALS["multisite"];
}
function is_super_admin($id) {
  return $id === 1;
}
function get_user_by($field, $id) {
  $roles = [
    1 => [],
    2 => ["administrator"],
    3 => ["editor"],
    4 => ["subscriber"],
  ];
  return isset($roles[$id]) ? (object) ["roles" => $roles[$id]] : false;
}
require dirname(__DIR__) . "/autoload/enforce-2fa.php";
$count = 0;
foreach ([1, 2, 3, 4, 999] as $id) {
  foreach (
    [[], ["Two_Factor_Totp"], ["Two_Factor_Totp", "Two_Factor_Email"]]
    as $providers
  ) {
    $required =
      ($admin && ($id === 2 || ($id === 1 && $multisite))) ||
      ($editor && $id === 3);
    $expected = $required && !$providers ? ["Two_Factor_Email"] : $providers;
    $actual = $policy($providers, $id);
    if ($actual !== $expected || $policy($actual, $id) !== $actual) {
      throw new RuntimeException("Unexpected 2FA policy for user " . $id);
    }
    $count++;
  }
}
echo "$count two-factor policy cases passed\n";
