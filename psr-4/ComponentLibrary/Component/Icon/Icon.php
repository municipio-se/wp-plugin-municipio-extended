<?php

namespace MunicipioExtended\ComponentLibrary\Component\Icon;

use ComponentLibrary\Helper\Icons;
use MunicipioExtended\ComponentLibrary\Component\MxBaseController;

/**
 * Replaces \ComponentLibrary\Component\Icon\Icon
 */
class Icon extends MxBaseController {
  private $altTextPrefix = "Icon: ";
  private $altText = [
    "key" => "Label",
  ];
  private $altTextUndefined = "Undefined";

  public function originalInit() {
    //Extract array for easy access (fetch only)
    extract($this->data);

    $customSvgIcons = (new Icons($this->cache))->getIcons();
    $customIconName = $filled ? $icon . "Filled" : $icon;

    $this->data["svgFromLink"] = $this->iconIsSvg($icon);

    if ($this->data["svgFromLink"]) {
      $this->data["classList"][] = $this->getBaseClass() . "--svg-link";
    } elseif (array_key_exists($customIconName, $customSvgIcons)) {
      $this->data["svgElementFromFile"] = $customSvgIcons[$customIconName];
      $this->data["classList"][] = $this->getBaseClass() . "--svg-path";
    } else {
      $this->data["classList"] = array_merge($this->data["classList"] ?? [], [
        $this->createIconModifier($icon),
        $this->getBaseClass() . "--material",
        $this->getBaseClass() . "--material-" . $icon,
        "material-symbols-outlined",
      ]);

      $this->data["attributeList"]["material-symbol"] = $icon;
    }

    if (!empty($filled)) {
      $this->data["classList"][] = "material-symbols-outlined--filled";
    }

    if (!empty($customColor)) {
      $this->data["attributeList"]["style"] =
        "color:" . $customColor . ";" . "stroke:" . $customColor . ";";
    } else {
      $this->data["classList"][] = $this->setIconColorCssClass($color);
    }

    $this->data["label"] = $this->getSpacedLabel($label);
    $this->data["classList"][] = $this->setIconSizeCssClass($size);

    //Identify as an image
    $this->data["attributeList"]["role"] = "img";
    $this->data["attributeList"]["data-nosnippet"] = "";
    $this->data["attributeList"]["translate"] = "no";
    $this->data["attributeList"]["aria-label"] = $decorative
      ? ""
      : $this->getAltText($icon);
    $this->data["attributeList"]["alt"] = $decorative
      ? ""
      : $this->getAltText($icon);
    $this->data["attributeList"]["aria-hidden"] = $decorative
      ? "true"
      : "false";
  }

  private function iconIsSvg($icon) {
    if (!is_string($icon)) {
      return false;
    }

    return str_ends_with($icon, ".svg") !== false;
  }

  private function createIconModifier($icon) {
    if (is_null($icon)) {
      return "";
    }

    return $this->getBaseClass(str_replace("_", "-", $icon), true);
  }

  private function getSpacedLabel($label) {
    if ($label = trim($label)) {
      $label = " " . $label;
    }

    return $label;
  }

  private function setIconColorCssClass($color) {
    return !empty($color)
      ? $this->getBaseClass() . "--color-" . strtolower($color)
      : "";
  }

  private function setIconSizeCssClass($size) {
    $sizes = [
      "xs" => "16",
      "sm" => "24",
      "md" => "32",
      "lg" => "48",
      "xl" => "64",
      "xxl" => "80",
    ];

    return isset($sizes[$size])
      ? $this->getBaseClass() . "--size-" . $size
      : $this->getBaseClass() . "--size-inherit";
  }

  private function getAltText($icon) {
    if (array_key_exists($icon, $this->altText())) {
      return $this->altTextPrefix() . $this->altText()[$icon];
    }
    return $this->altTextPrefix() . $this->altTextUndefined();
  }

  private function altText(): array {
    if (function_exists("apply_filters")) {
      /**
       * Filters the icon alt text map.
       *
       * @param array $alt_text Alt text keyed by icon name.
       * @return array Filtered alt text map.
       */
      return apply_filters(
        $this->createFilterName($this) . "/" . ucfirst(__FUNCTION__),
        $this->altText,
      );
    }
    return $this->altText;
  }

  private function altTextPrefix(): string {
    if (function_exists("apply_filters")) {
      /**
       * Filters the prefix prepended to icon alt text.
       *
       * @param string $prefix Alt text prefix.
       * @return string Filtered alt text prefix.
       */
      return apply_filters(
        $this->createFilterName($this) . "/" . ucfirst(__FUNCTION__),
        $this->altTextPrefix,
      );
    }
    return $this->altTextPrefix;
  }

  private function altTextUndefined(): string {
    if (function_exists("apply_filters")) {
      /**
       * Filters the fallback alt text for unknown icons.
       *
       * @param string $alt_text Fallback alt text.
       * @return string Filtered fallback alt text.
       */
      return apply_filters(
        $this->createFilterName($this) . "/" . ucfirst(__FUNCTION__),
        $this->altTextUndefined,
      );
    }
    return $this->altTextUndefined;
  }

  public function init() {
    $data = &$this->data;

    // $data["useHbg"] =
    //   $data["useHbg"] ?? !$this->getKirkiOption("icon_mxui_enabled");
    // if ($data["useHbg"]) {
    //   return $this->originalInit();
    // }

    /**
     * The rest of this method transforms original data structure into MXUI data
     * structure
     */
    $available_colors = [
      "black" => "var(--color-black, #000)",
      "gray" => "var(--color-light, var(--color-light, #a3a3a3))",
      "default" => "var(--color-default, #f5f5f5)",
      "default-dark" => "var(--color-default-dark, #dadada)",
      "white" => "var(--color-white, #fff)",
      "primary" => "var(--color-primary, #ae0b05)",
      "secondary" => "var(--color-secondary, #ec6701)",
    ];
    if (
      !($data["customColor"] ?? null) && $data["color"] ??
      null && key_exists($available_colors, $data["color"])
    ) {
      $data["customColor"] = $available_colors[$data["color"]];
    }
    unset($data["color"]);

    if ($data["icon"] == "expand_more") {
      $data["classList"][] = "[.c-nav_&]:transition-transform";
      $data["classList"][] =
        "[.c-nav_.c-nav\_\_item.is-open>.c-nav\_\_item-wrapper_&]:rotate-180";
    }

    if (in_array("c-box__icon", $data["classList"])) {
      $data["classList"][] = "self-center";
      $data["classList"][] = "text-primary-contrasting";
      $data["classList"][] = "text-[5rem]";
      $data["classList"][] = "leading-[60px]";
    }
  }
}
