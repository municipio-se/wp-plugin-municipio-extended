<?php

add_filter(
  "/Modularity/externalViewPath",
  function ($paths) {
    $paths["mod-navigation"] = MUNICIPIO_EXTENDED_PATH . "/views";
    return $paths;
  },
  2,
  1,
);

add_action("plugins_loaded", function ($array) {
  if (function_exists("modularity_register_module")) {
    modularity_register_module(
      MUNICIPIO_EXTENDED_PATH . "/psr-4/Modularity/ModNavigation",
      "ModNavigation",
    );
  }
});

function get_all_menus() {
  $menus = wp_get_nav_menus();
  $choices = [];

  foreach ($menus as $menu) {
    $choices[$menu->slug] = $menu->name;
  }

  return $choices;
}

add_action("acf/init", function () {
  acf_add_local_field_group([
    "key" => "group_mod_navigation",
    "title" => _x(
      "Navigation module",
      "Navigation Module Field Group Title",
      "municipio-extended",
    ),
    "fields" => array_values(
      /**
       * Filters the ACF fields registered for the navigation module.
       *
       * @param array $fields Navigation module field definitions keyed by field name.
       * @return array Filtered field definitions.
       */
      apply_filters("mx_mod_navigation_fields", [
        "mod_navigation_format" => [
          "key" => "field_mod_navigation_format",
          "label" => _x(
            "Format",
            "Navigation Module Field Label",
            "municipio-extended",
          ),
          "name" => "mod_navigation_format",
          "graphql_field_name" => "format",
          "show_in_graphql" => 1,
          "type" => "select",
          "required" => 1,
          "return_format" => "value",
          "choices" => [
            "list" => _x(
              "List",
              "Navigation Module Format Choice",
              "municipio-extended",
            ),
            "grid" => _x(
              "Grid",
              "Navigation Module Format Choice",
              "municipio-extended",
            ),
            "bar" => _x(
              "Bar",
              "Navigation Module Format Choice",
              "municipio-extended",
            ),
            "tree" => _x(
              "Tree",
              "Navigation Module Format Choice",
              "municipio-extended",
            ),
            "cards" => _x(
              "Cards",
              "Navigation Module Format Choice",
              "municipio-extended",
            ),
            "buttons" => _x(
              "Buttons",
              "Navigation Module Format Choice",
              "municipio-extended",
            ),
            "inline" => _x(
              "Inline",
              "Navigation Module Format Choice",
              "municipio-extended",
            ),
          ],
        ],
        "mod_navigation_source" => [
          "key" => "field_mod_navigation_source",
          "label" => _x(
            "Source",
            "Navigation Module Field Label",
            "municipio-extended",
          ),
          "name" => "mod_navigation_source",
          "graphql_field_name" => "source",
          "show_in_graphql" => 1,
          "type" => "select",
          "required" => 0,
          "return_format" => "value",
          "choices" => [
            "children" => _x(
              "Child pages",
              "Navigation Module Source Choice",
              "municipio-extended",
            ),
            "siblings" => _x(
              "Sibling pages",
              "Navigation Module Source Choice",
              "municipio-extended",
            ),
            "menu" => _x(
              "Menu",
              "Navigation Module Source Choice",
              "municipio-extended",
            ),
            "manual" => _x(
              "Manually selected",
              "Navigation Module Source Choice",
              "municipio-extended",
            ),
          ],
        ],
        "mod_navigation_menu" => [
          "key" => "field_mod_navigation_menu",
          "label" => _x(
            "Menu",
            "Navigation Module Field Label",
            "municipio-extended",
          ),
          "name" => "mod_navigation_menu",
          "graphql_field_name" => "menu",
          "show_in_graphql" => 1,
          "type" => "select",
          "return_format" => "value",
          "choices" => get_all_menus(),
          "allow_null" => 1,
          "conditional_logic" => [
            [
              [
                "field" => "field_mod_navigation_source",
                "operator" => "==",
                "value" => "menu",
              ],
            ],
          ],
        ],
        "mod_navigation_items" => [
          "key" => "field_mod_navigation_items",
          "label" => _x(
            "Items",
            "Navigation Module Field Label",
            "municipio-extended",
          ),
          "name" => "mod_navigation_items",
          "graphql_field_name" => "items",
          "show_in_graphql" => 1,
          "type" => "repeater",
          "conditional_logic" => [
            [
              [
                "field" => "field_mod_navigation_source",
                "operator" => "==",
                "value" => "manual",
              ],
            ],
          ],
          "sub_fields" => [
            [
              "key" => "field_mod_navigation_item_link",
              "label" => _x(
                "Link",
                "Navigation Module Field Label",
                "municipio-extended",
              ),
              "name" => "link",
              "type" => "link",
              "required" => 1,
              "wrapper" => ["width" => "75%"],
            ],
            [
              "key" => "field_mod_navigation_color",
              "label" => _x(
                "Color",
                "Navigation Module Field Label",
                "municipio-extended",
              ),
              "name" => "color",
              "type" => "color_picker",
              "wrapper" => ["width" => "25%"],
              "conditional_logic" => [
                [
                  [
                    "field" => "field_mod_navigation_format",
                    "operator" => "==",
                    "value" => "grid",
                  ],
                ],
              ],
            ],
            [
              "key" => "field_mod_navigation_button_variant",
              "label" => _x(
                "Variant",
                "Navigation Module Field Label",
                "municipio-extended",
              ),
              "name" => "button_variant",
              "type" => "select",
              "choices" => [
                "primary" => _x(
                  "Primary",
                  "Navigation Module Button Variant Choice",
                  "municipio-extended",
                ),
                "secondary" => _x(
                  "Secondary",
                  "Navigation Module Button Variant Choice",
                  "municipio-extended",
                ),
                "default" => _x(
                  "Default",
                  "Navigation Module Button Variant Choice",
                  "municipio-extended",
                ),
              ],
              "default_value" => "default",
              "wrapper" => ["width" => "25%"],
              "conditional_logic" => [
                [
                  [
                    "field" => "field_mod_navigation_format",
                    "operator" => "==",
                    "value" => "buttons",
                  ],
                ],
              ],
            ],
            [
              "key" => "field_mod_navigation_item_icon",
              "label" => _x(
                "Icon",
                "Navigation Module Field Label",
                "municipio-extended",
              ),
              "name" => "icon",
              "type" => "select",
              "choices" => [],
              "ui" => 1,
              "allow_null" => 1,
              "wrapper" => ["width" => "25%"],
              "conditional_logic" => [
                [
                  [
                    "field" => "field_mod_navigation_format",
                    "operator" => "!=",
                    "value" => "tree",
                  ],
                ],
              ],
            ],
          ],
        ],
        "mod_navigation_show_if_empty" => [
          "key" => "field_mod_navigation_show_if_empty",
          "label" => _x(
            "Display this module even when there are no items",
            "Navigation Module Field Label",
            "municipio-extended",
          ),
          "name" => "mod_navigation_show_if_empty",
          "graphql_field_name" => "showIfEmpty",
          "show_in_graphql" => 1,
          "type" => "true_false",
          "required" => 0,
          "default_value" => 0,
          "ui" => 1,
        ],
        "mod_navigation_empty_message" => [
          "key" => "field_mod_navigation_empty_message",
          "label" => _x(
            "Message to display when there are no items",
            "Navigation Module Field Label",
            "municipio-extended",
          ),
          "name" => "mod_navigation_empty_message",
          "graphql_field_name" => "emptyMessage",
          "show_in_graphql" => 1,
          "type" => "wysiwyg",
          "required" => 0,
          "default_value" => "",
          "conditional_logic" => [
            [
              [
                "field" => "field_mod_navigation_show_if_empty",
                "operator" => "==",
                "value" => "1",
              ],
            ],
          ],
        ],
        // [
        //   "key" => "field_mod_navigation_depth",
        //   "label" => _x("Depth", "Navigation Module Field Label", "municipio-extended"),
        //   "name" => "mod_navigation_depth",
        //   "graphql_field_name" => "depth",
        //   "show_in_graphql" => 1,
        //   "type" => "number",
        //   "required" => 0,
        //   "default_value" => 1,
        //   "min" => 1,
        //   "conditional_logic" => [
        //     [
        //       [
        //         "field" => "field_mod_navigation_source",
        //         "operator" => "==",
        //         "value" => "children",
        //       ],
        //     ],
        //   ],
        // ],
      ]),
    ),
    "location" => [
      [
        [
          "param" => "post_type",
          "operator" => "==",
          "value" => "mod-navigation",
        ],
      ],
    ],
    "show_in_graphql" => 1,
  ]);
});

add_filter("acf/load_field/key=field_mod_navigation_item_icon", function (
  $field,
) {
  return mx_add_icons_list($field);
});
