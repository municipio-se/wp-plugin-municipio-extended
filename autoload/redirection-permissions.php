<?php

/*
A temporary solution to allow editors access to the Redirection tool.

According to https://redirection.me/developer/permissions/ an official version
will be released in the future.
*/

add_action("acf/init", function () {
  acf_add_local_field_group([
    "key" => "group_mx_options_redirection",
    "title" => __("Redirection", "municipio-extended"),
    "fields" => [
      0 => [
        "key" => "field_mx_options_redirection_editor_access_to_redirection",
        "label" => __(
          "Allow Editors access to Redirection",
          "municipio-extended",
        ),
        "name" => "editor_access_to_redirection",
        "type" => "true_false",
        "instructions" => "",
        "required" => 0,
        "conditional_logic" => 0,
        "wrapper" => [
          "width" => "",
          "class" => "",
          "id" => "",
        ],
        "message" => "",
        "default_value" => 0,
        "ui" => 0,
        "ui_on_text" => "",
        "ui_off_text" => "",
      ],
    ],
    "location" => [
      0 => [
        0 => [
          "param" => "options_page",
          "operator" => "==",
          "value" => "acf-options-theme-options",
        ],
      ],
    ],
    "menu_order" => 0,
    "position" => "normal",
    "style" => "default",
    "label_placement" => "top",
    "instruction_placement" => "label",
    "hide_on_screen" => "",
    "active" => 1,
    "description" => "",
  ]);
});

add_filter("redirection_role", function () {
  if (current_user_can("administrator")) {
    return "manage_options";
  } elseif (
    current_user_can("editor") &&
    get_field("editor_access_to_redirection", "options") == 1
  ) {
    add_menu_page(
      "redirection_link",
      "Redirection",
      "read",
      "&#47;wp-admin/tools.php?page=redirection.php",
      "",
      "dashicons-redo",
      25,
    );
    return "edit_posts";
  } else {
    return false;
  }
});
