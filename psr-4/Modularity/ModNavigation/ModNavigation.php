<?php

namespace MunicipioExtended\Modularity\ModNavigation;

use MunicipioExtended\Modularity\MxModule;

class ModNavigation extends MxModule {
  public $slug = "navigation";
  public $supports = [];

  public function init() {
    $this->nameSingular = _x(
      "Navigation",
      "Post Type Singular Name",
      "municipio-extended",
    );
    $this->namePlural = _x(
      "Navigation modules",
      "Post Type General Name",
      "municipio-extended",
    );
    $this->description = __(
      "Outputs a menu or manually selected links",
      "municipio-extended",
    );

    // Arrow Circle Right from https://fluenticons.co/
    $icon_svg =
      '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 2.001c5.524 0 10 4.477 10 10s-4.476 10-10 10c-5.522 0-10-4.477-10-10s4.478-10 10-10Zm.781 5.469-.084-.073a.75.75 0 0 0-.883-.007l-.094.08-.072.084a.75.75 0 0 0-.007.883l.08.094 2.719 2.72H7.75l-.102.006a.75.75 0 0 0-.641.642L7 12l.007.102a.75.75 0 0 0 .641.641l.102.007h6.69l-2.72 2.72-.073.085a.75.75 0 0 0 1.05 1.05l.083-.073 4.002-4 .072-.085a.75.75 0 0 0 .008-.882l-.08-.094-4-4.001-.085-.073.084.073Z" fill="#212121"/></svg>';
    $this->icon = "data:image/svg+xml;base64," . base64_encode($icon_svg);
  }

  /**
   * Get the template file for the module
   * @return string
   */
  public function template(): string {
    return "mod-navigation.blade.php";
  }

  /**
   * Want to add more fields for this module in the customizer? Add them to the
   * array in this method. Ensure "settings" value starts with
   * "mod_navigation_". It will then be available in the blade template.
   */
  protected function getCustomizationFields() {
    return [
      [
        "type" => "select",
        "settings" => "mod_navigation_bar_style",
        "label" => _x(
          "Style for format “bar”",
          "Navigation Module Customization Field Label",
          "municipio-extended",
        ),
        "default" => "outline",
        "priority" => 10,
        "choices" => [
          "outline" => _x(
            "Outline",
            "Navigation Module Bar Style Choice",
            "municipio-extended",
          ),
          "solid" => _x(
            "Solid",
            "Navigation Module Bar Style Choice",
            "municipio-extended",
          ),
        ],
      ],
      [
        "type" => "select",
        "settings" => "mod_navigation_tree_style",
        "label" => _x(
          "Style for format “tree”",
          "Navigation Module Customization Field Label",
          "municipio-extended",
        ),
        "default" => "standard",
        "priority" => 10,
        "choices" => [
          "standard" => _x(
            "Standard",
            "Navigation Module Tree Style Choice",
            "municipio-extended",
          ),
          "highlighted" => _x(
            "Highlighted",
            "Navigation Module Tree Style Choice",
            "municipio-extended",
          ),
        ],
      ],
      [
        "type" => "select",
        "settings" => "mod_navigation_grid_style",
        "label" => _x(
          "Style for format “grid”",
          "Navigation Module Customization Field Label",
          "municipio-extended",
        ),
        "default" => "default",
        "priority" => 10,
        "choices" => [
          "default" => _x(
            "Standard",
            "Navigation Module Grid Style Choice",
            "municipio-extended",
          ),
          "blocks" => _x(
            "Blocks",
            "Navigation Module Grid Style Choice",
            "municipio-extended",
          ),
        ],
      ],
    ];
  }

  protected function getField($field, ...$args) {
    return get_field($field, $this->ID, ...$args);
  }

  protected function getItems() {
    $depth =
      $this->getField("mod_navigation_depth") ?:
      ($this->getField("mod_navigation_format") === "tree"
        ? 2
        : 1);
    switch ($this->getField("mod_navigation_source")) {
      case "children":
        return $this->getChildren($depth);
      case "siblings":
        return $this->getSiblings();
      case "manual":
        return $this->getManualItems();
      case "menu":
        return $this->getMenuItems($depth);
      default:
        return $this->getManualItems();
    }
  }

  protected function getChildren($depth = 1, $post_id = null) {
    if ($depth <= 0) {
      return null;
    }
    $post = get_post($post_id ?? $this->getCurrentPost());
    if (!$post) {
      return [];
    }

    /**
     * Filters whether child navigation should use the Nested Pages menu.
     *
     * @since 2025.12.1
     *
     * @param bool     $use_np Whether to use the Nested Pages menu.
     * @param \WP_Post $post   Current post.
     * @param string   $source Navigation source, here `children`.
     * @param string   $slug   Module slug.
     * @param int      $id     Module ID.
     * @return bool Whether to use the Nested Pages menu.
     */
    $use_np = apply_filters(
      "mx_mod_navigation_use_nested_pages",
      false,
      $post,
      "children",
      $this->slug,
      $this->ID,
    );
    if ($use_np) {
      $np_menu = get_option("nestedpages_menu");
      if ($np_menu) {
        $menu_items = wp_get_associated_nav_menu_items($post->ID);
        $menu_items = array_filter($menu_items, function ($menu_item_id) use (
          $np_menu,
        ) {
          return has_term($np_menu, "nav_menu", $menu_item_id);
        });
        foreach ($menu_items as $menu_item_id) {
          $items = self::getMenuItemsByMenu($np_menu, $depth, $menu_item_id);
          if (!empty($items)) {
            return $items;
          }
        }
      }
    }

    // Use the regular page tree
    $args = [
      "post_parent" => $post->ID,
      "post_type" => $post->post_type,
      "nopaging" => true,
      "post_status" => "publish",
      "orderby" => "menu_order",
      "order" => "ASC",
      "meta_query" => [
        "relation" => "OR",
        [
          "key" => "hide_in_menu",
          "value" => "1",
          "compare" => "!=",
        ],
        [
          "key" => "hide_in_menu",
          "compare" => "NOT EXISTS",
        ],
      ],
    ];
    $child_posts = get_posts($args);
    $items = array_map(function ($post) use ($depth) {
      return [
        "id" => $post->ID,
        "post" => $post,
        "title" =>
          get_field("custom_menu_title", $post->ID) ?: $post->post_title,
        "href" => get_permalink($post->ID),
        "image" => mx_get_image(get_post_thumbnail_id($post)),
        "icon" => get_field("page_navigation_icon", $post->ID),
        "description" => get_field("page_navigation_description", $post->ID),
        "color" => get_field("page_apperance_theme_color", $post->ID),
        "children" => $this->getChildren($depth - 1, $post->ID),
      ];
    }, $child_posts);
    return $items;
  }

  protected function getSiblings() {
    $post = $this->getCurrentPost();
    if (!$post) {
      return [];
    }

    /**
     * Filters whether sibling navigation should use the Nested Pages menu.
     *
     * @since 2025.12.1
     *
     * @param bool     $use_np Whether to use the Nested Pages menu.
     * @param \WP_Post $post   Current post.
     * @param string   $source Navigation source, here `siblings`.
     * @param string   $slug   Module slug.
     * @param int      $id     Module ID.
     * @return bool Whether to use the Nested Pages menu.
     */
    $use_np = apply_filters(
      "mx_mod_navigation_use_nested_pages",
      false,
      $post,
      "siblings",
      $this->slug,
      $this->ID,
    );
    if ($use_np) {
      $np_menu = get_option("nestedpages_menu");
      if ($np_menu) {
        $menu_items = wp_get_associated_nav_menu_items($post->ID);
        $menu_items = array_filter($menu_items, function ($menu_item_id) use (
          $np_menu,
        ) {
          return has_term($np_menu, "nav_menu", $menu_item_id);
        });
        foreach ($menu_items as $menu_item_id) {
          $items = self::getMenuItemsByMenu($np_menu, 1, $menu_item_id);
          if (!empty($items)) {
            return $items;
          }
        }
      }
    }

    // Use the regular page tree
    $args = [
      "post_parent" => $post->post_parent,
      "post_type" => $post->post_type,
      "post__not_in" => [$post->ID],
      "nopaging" => true,
      "post_status" => "publish",
      "orderby" => "menu_order",
      "meta_query" => [
        "relation" => "OR",
        [
          "key" => "hide_in_menu",
          "value" => "1",
          "compare" => "!=",
        ],
        [
          "key" => "hide_in_menu",
          "compare" => "NOT EXISTS",
        ],
      ],
    ];
    $sibling_posts = get_posts($args);
    $items = array_map(function ($post) {
      return [
        "id" => $post->ID,
        "post" => $post,
        "title" =>
          get_field("custom_menu_title", $post->ID) ?: $post->post_title,
        "href" => get_permalink($post->ID),
        "image" => mx_get_image(get_post_thumbnail_id($post)),
        "icon" => get_field("page_navigation_icon", $post->ID),
        "description" => get_field("page_navigation_description", $post->ID),
        "color" => get_field("page_apperance_theme_color", $post->ID),
      ];
    }, $sibling_posts);
    return $items;
  }

  public static function getMenuItemsByMenu(
    $menu_slug,
    $depth = 1,
    $post_parent = 0,
  ) {
    $menu_items = wp_get_nav_menu_items($menu_slug);
    $menu_items = array_filter($menu_items, function ($item) use (
      $post_parent,
    ) {
      return $item->menu_item_parent == $post_parent;
    });
    if (empty($menu_items)) {
      return [];
    }

    return array_map(function (\WP_Post $item) use ($menu_slug, $depth) {
      $item = mx_get_menu_item($item);
      return [
        "id" => $item->id,
        "href" => $item->url,
        "title" => $item->title,
        "image" => $item->image,
        "icon" => $item->icon,
        "color" => $item->ownThemeColor,
        "description" => $item->description ?: $item->menuDescription,
        "children" => self::getMenuItemsByMenu(
          $menu_slug,
          $depth - 1,
          $item->ID,
        ),
      ];
    }, $menu_items);
  }

  protected function getMenuItems($depth = 1, $post_parent = 0) {
    if ($depth <= 0) {
      return null;
    }

    $menu_slug = $this->getField("mod_navigation_menu");
    if (empty($menu_slug)) {
      return [];
    }

    return self::getMenuItemsByMenu($menu_slug, $depth, $post_parent);
  }

  protected function getManualItems() {
    $items = $this->getField("mod_navigation_items");
    if (empty($items)) {
      return [];
    }
    return array_map(function ($item) {
      $post_id = url_to_postid($item["link"]["url"]);
      $post = $post_id ? get_post($post_id) : null;
      return [
        "post" => $post,
        "title" =>
          $item["link"]["title"] ?:
          get_field("custom_menu_title", $post->ID) ?:
          $post->post_title,
        "href" => $item["link"]["url"],
        "image" => $post ? mx_get_image(get_post_thumbnail_id($post)) : null,
        "icon" =>
          $item["icon"] ?:
          ($post
            ? get_field("page_navigation_icon", $post->ID)
            : null),
        "description" => $post
          ? get_field("page_navigation_description", $post->ID)
          : null,
        "color" =>
          $item["color"] ?:
          ($post
            ? get_field("page_apperance_theme_color", $post->ID)
            : null),
        "buttonVariant" => $item["button_variant"],
      ];
    }, $items);
  }

  /**
   * Data array
   * @return array $data
   */
  public function data(): array {
    $data = parent::data();
    $data["items"] = $this->getItems();

    /**
     * Filters whether an empty navigation module should be hidden.
     *
     * @param bool   $hide Whether the module should be hidden.
     * @param string $slug Module slug.
     * @param int    $id   Module ID.
     * @param array  $data Module data.
     * @return bool Whether the module should be hidden.
     */
    $data["hideIfEmpty"] = apply_filters(
      "mx/mod_navigation/hide_if_empty",
      !$data["show_if_empty"],
      $this->slug,
      $this->ID,
      $data,
    );
    return $data;
  }
}
