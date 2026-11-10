<?php

// Register after configuration constants have been loaded.
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

      // Network administrators may have no local administrator role.
      $force_super_admin =
        defined("TWO_FACTOR_ENFORCE_ADMIN") &&
        TWO_FACTOR_ENFORCE_ADMIN &&
        is_multisite() &&
        is_super_admin($user_id);

      if (
        !$user ||
        (!$force_super_admin &&
          empty(array_intersect($force_roles, $user->roles)))
      ) {
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
