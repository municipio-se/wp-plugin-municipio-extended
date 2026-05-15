<?php

/*
1. Temporary solution to allow editors access to the Redirection tool

(according to https://redirection.me/developer/permissions/ an official version
will be released in the future)
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
      "tools.php?page=redirection.php",
      "",
      "dashicons-redo",
      25,
    );
    return "edit_posts";
  } else {
    return false;
  }
});

/*
2. Clean up editor permissions and capabilities
*/

add_filter("admin_init", function () {
  if (current_user_can("administrator")) {
    return;
  }
  if (current_user_can("editor")) {
    global $menu;
    global $submenu;
    $roleObject = get_role("editor");
    // Remove all current capabilities
    $currentCapabilities = $roleObject->capabilities;
    foreach ($currentCapabilities as $cap => $value) {
      $roleObject->remove_cap($cap);
    }
    // Add allowed capabilities
    $allowedCapabilities = [
      "moderate_comments",
      "manage_categories",
      "manage_links",
      "upload_files",
      "unfiltered_html",
      "edit_posts",
      "edit_others_posts",
      "edit_published_posts",
      "publish_posts",
      "edit_pages",
      "edit_posts",
      "read",
      "level_7",
      "level_6",
      "level_5",
      "level_4",
      "level_3",
      "level_2",
      "level_1",
      "level_0",
      "edit_others_pages",
      "edit_published_pages",
      "publish_pages",
      "delete_pages",
      "delete_others_pages",
      "delete_published_pages",
      "delete_posts",
      "delete_others_posts",
      "delete_published_posts",
      "delete_private_posts",
      "edit_private_posts",
      "read_private_posts",
      "delete_private_pages",
      "edit_private_pages",
      "read_private_pages",
      "edit_module",
      "edit_modules",
      "edit_other_modules",
      "publish_modules",
      "read_modules",
      "delete_module",
      "edit_theme_options",
      "manage_options",
      "nestedpages",
      "gform_full_access",
    ];
    foreach ($allowedCapabilities as $key => $cap) {
      $roleObject->add_cap($cap, true);
    }
    // Clean up admin menu and submenus
    $allowedMenuItems = [
      "index.php",
      // Posts
      "edit.php",
      "post-new.php",
      "edit-tags.php?taxonomy=category",
      "edit-tags.php?taxonomy=post_tag",
      // Media
      "upload.php",
      "media-new.php",
      // Pages
      "nestedpages",
      "admin.php?page=nestedpages",
      "edit.php?post_type=page",
      "post-new.php?post_type=page",
      "options.php?page=modularity-editor&id=single-page",
      // Events
      "edit.php?post_type=event",
      // Users
      "profile.php",
      // Forms
      "edit.php?post_type=form-submissions",
    ];
    /*
    Temporary handling of custom post types & taxonomies from:
    arvidsjaur.se
    trelleborgvaxer.se
    bildenavtrelleborg.se
    soderslattsgymnasiet.trelleborg.se
    trelleborg.se
    hoor.se
    salabostader.se
    medborgarhuset.eslov.se
    utveckla.eslov.se
    foretag.eslov.se
    programforoffentligmiljo.eslov.se
    eslov.se
    */
    $customPostTypes = [
      "anslag",
      "alert",
      "project",
      "api-resource",
      "modal-content",
      "operational-status",
      "bulletin-board",
      "nyheter",
      "driftinformation",
      "external_page",
      "fragor-svar",
      "common-alert",
      "area",
      "projekt",
      "driftsinformation",
      "pressmeddelanden",
      "job-listing",
      "offentlig-konst",
      "manadens-konst",
      "anslagstavla",
    ];
    foreach ($customPostTypes as $cpt) {
      $allowedMenuItems[] = "edit.php?post_type=" . $cpt;
      $allowedMenuItems[] = "post-new.php?post_type=" . $cpt;
    }

    // Allow editors to manage Modularity content
    $all_post_types = get_post_types();
    $mod_post_types = array_filter($all_post_types, function ($post_type_name) {
      return strpos($post_type_name, "mod-") === 0;
    });
    foreach ($mod_post_types as $post_type_name) {
      $allowedMenuItems[] = "edit.php?post_type=" . $post_type_name;
      $allowedMenuItems[] = "post-new.php?post_type=" . $post_type_name;
    }
    $allowedMenuItems[] = "modularity";

    $customTaxonomies = [
      "project_status",
      "project_category",
      "platser",
      "teman",
      "operational-status-category",
      "operational-status-status",
      "bulletin-board-category",
      "external_page_content_type",
      "common-alert-type",
      "amne",
      "job-listing-category",
      "job-listing-source",
      "anslagstyp",
      "plats",
      "status",
      "detaljplanering",
      "dialog",
      "byggstatus",
      "markanvisning",
    ];
    foreach ($customTaxonomies as $ct) {
      $allowedMenuItems[] = "edit-tags.php?taxonomy=" . $ct;
    }
    /*
    End temporary code
    */
    if (
      current_user_can("editor") &&
      get_field("editor_access_to_redirection", "options") == 1
    ) {
      $allowedMenuItems[] = "tools.php?page=redirection.php";
    }
    foreach ($menu as $key => $menuItem) {
      if (
        !in_array($menuItem[1], $allowedCapabilities) ||
        !in_array($menuItem[2], $allowedMenuItems)
      ) {
        unset($menu[$key]);
      } else {
        if (isset($submenu[$menuItem[2]])) {
          foreach ($submenu[$menuItem[2]] as $k1 => $subMenuItem) {
            if (
              !in_array($subMenuItem[2], $allowedMenuItems) ||
              !in_array($subMenuItem[1], $allowedCapabilities)
            ) {
              unset($submenu[$menuItem[2]][$k1]);
            }
          }
        }
      }
    }
  }
});
