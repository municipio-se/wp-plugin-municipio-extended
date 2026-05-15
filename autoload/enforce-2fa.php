<?php

// For some reasen the config variables didn't load properly. I fixed it by wrapping the
// code in an init action for now. TODO: Investigate why the config variables didn't load
// properly and fix the issue.
add_action("init", function () {
  add_filter(
    "two_factor_enabled_providers_for_user",
    function ($providers, $user_id) {
      $force_roles = [];

      if (defined("TWO_FACTOR_ENFORCE_ADMIN") && TWO_FACTOR_ENFORCE_ADMIN) {
        $force_roles[] = "administrator";
      }

      if (defined("TWO_FACTOR_ENFORCE_EDITOR") && TWO_FACTOR_ENFORCE_EDITOR) {
        $force_roles[] = "editor";
      }

      $user = get_user_by("id", $user_id);

      if (empty(array_intersect($force_roles, $user->roles))) {
        return $providers;
      }

      if (empty($providers) && class_exists("Two_Factor_Email")) {
        $providers[] = "Two_Factor_Email";
      }

      return $providers;
    },
    10,
    2,
  );
});
