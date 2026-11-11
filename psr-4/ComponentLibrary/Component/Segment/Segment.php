<?php

namespace MunicipioExtended\ComponentLibrary\Component\Segment;

use Illuminate\Support\HtmlString;
use Kirki\Compatibility\Kirki;
use Municipio\Customizer;
use MunicipioExtended\ComponentLibrary\Component\MxBaseController;

class Segment extends MxBaseController {
  /**
   * Copied from original init function. Modifications are marked in the code
   * below.
   */
  public function originalInit() {
    //Extract array for eazy access (fetch only)
    extract($this->data);

    // Original:
    // $file_path = __DIR__ . "/partials/" . $layout . ".blade.php";

    // Original file uses __DIR__, but that won't work here.
    // Replacement:
    $original_class_name = "\ComponentLibrary\Component\Segment\Segment";
    $reflector = new \ReflectionClass($original_class_name);
    $original_class_file = $reflector->getFileName();
    $file_path =
      dirname($original_class_file) . "/partials/" . $layout . ".blade.php";

    $this->data["floatingSlotHasData"] = $this->slotHasData("floating");

    if (!file_exists($file_path)) {
      $layout = "full-width";
      $this->data["layout"] = $layout;
    }

    // Set the layout
    if ($layout) {
      $this->data["classList"][] = "c-segment--" . $layout;
    }

    if (!empty($icon)) {
      $this->data["icon"]["classList"][] = $this->getBaseClass("icon");
    }

    if (!isset($this->data["displayIcon"])) {
      $this->data["displayIcon"] = true;
    }

    // If no link and exactly one button, use that button as link
    if (!$this->data["link"] && ($buttons && count($buttons) === 1)) {
      $this->data["link"] = $buttons[0]["href"];
      $this->data["linkTarget"] =
        $buttons[0]["target"] ??
        ($buttons[0]["attributeList"]["target"] ?? "_self");
    }

    if (!empty($hasPlaceholder)) {
      $this->data["classList"][] = $this->getBaseClass("svg-background", true);
      $image = $image["src"];
    }

    $this->data["imageClassList"] = [];

    if ($this->data["content"] == strip_tags($this->data["content"], [])) {
      // Create paragraphs
      $paragraphs = preg_split("/\r\n|\n|\r/", $this->data["content"]);
      foreach ($paragraphs as &$part) {
        if (empty($part)) {
          continue;
        }
        $part = "<p>{$part}</p>";
      }
      $this->data["content"] = implode("", $paragraphs);
    }

    // Remove padding
    if (!$paddingTop) {
      $this->data["classList"][] = "u-padding__top--0";
      $this->data["imageClassList"][] = "u-margin__top--0";
    }

    if (!$paddingBottom) {
      $this->data["classList"][] = "u-padding__bottom--0";
      $this->data["imageClassList"][] = "u-margin__bottom--0";
    }

    // Set text color
    if ($stretch) {
      $this->data["classList"][] = "c-segment--stretch";
    }

    // Set text color
    if ($textColor) {
      $this->data["classList"][] = "c-segment--text-" . $textColor;
    }

    // Height
    if ($height) {
      $this->data["classList"][] = "c-segment--height-" . $height;
    }

    // Text Size
    if ($textSize) {
      $this->data["classList"][] = "c-segment--text-" . $textSize;
    }

    // Text Alignment
    if ($textAlignment) {
      $this->data["classList"][] = "c-segment--alignment-" . $textAlignment;
    }

    // Column reverse
    if ($reverseColumns) {
      $this->data["classList"][] = "c-segment--reverse";
    }

    // Add overlay class
    if ($layout === "full-width" && ($title || $content) && !empty($image)) {
      $this->data["classList"][] = "c-segment" . "--has-overlay";
    }

    //Stringify image classlist
    $this->data["imageClass"] = implode("", $this->data["imageClassList"]);

    //Create image style tag
    $this->data["imageStyle"] = [];

    //Add image to image styles
    if ($image) {
      $this->data["imageStyle"]["background-image"] = "url('" . $image . "')";
    } else {
      $this->data["classList"][] = $this->getBaseClass("no-image", true);
    }

    if (!empty($contentAlignment)) {
      $this->data["classList"][] =
        $this->getBaseClass() . "--content-" . $contentAlignment;
    }

    if (!empty($contentBackground)) {
      $this->data["classList"][] =
        $this->getBaseClass() . "--content-background-" . $contentBackground;
    }

    if (is_object($imageFocus)) {
      $imageFocus = (array) $imageFocus;
    }

    //Add background position to image styles
    if (is_array($imageFocus) && array_filter((array) $imageFocus)) {
      $this->data["imageStyle"]["background-position"] =
        $imageFocus["left"] . "% " . $imageFocus["top"] . "%";
    }

    //Stringify image styles
    $this->data["imageStyleString"] = self::buildInlineStyle(
      $this->data["imageStyle"],
    );

    // Handle background data (wrapper)
    if ($background) {
      if (preg_match('^#(?:[0-9a-fA-F]{3}){1,2}$^', $background)) {
        $this->data["attributeList"]["style"] =
          "background-color: " . $background . ";";
      } else {
        $this->data["classList"][] = "u-color__bg--" . $background;
      }
    }
  }

  public function init() {
    $this->data["useHbg"] =
      $this->data["useHbg"] ??
      $this->hasContext("module.sections.full") ||
        $this->hasContext("module.sections.split") ||
        $this->hasContext("module.sections.featured");
    if ($this->data["useHbg"]) {
      return $this->originalInit();
    }

    /**
     * The rest of this method transforms original data structure into MXUI data
     * structure
     */

    $this->data["classList"] = array_diff($this->data["classList"], [
      "modularity-event-hero",
    ]);

    if ($this->hasContext("module.manual-input.segment")) {
      $this->data["headingVariant"] ??= "typography-h2";
      $this->data["proseWrap"] ??= true;
      $this->data["content"] = mx_prosify($this->data["content"]);
    }

    if ($this->hasContext("module.sections.card")) {
      $this->data["proseWrap"] ??= true;
      $this->data["content"] = mx_prosify($this->data["content"]);
    }

    $this->data["buttons"] = array_filter(
      $this->data["buttons"] ?? [] ?: [],
      function ($button) {
        return !empty($button["href"]);
      },
    );

    if (
      !empty($this->data["buttons"]) &&
      empty($this->data["link"]) &&
      count($this->data["buttons"]) === 1 &&
      empty($this->data["buttons"][0]["text"])
    ) {
      $this->data["link"] = $this->data["buttons"][0]["href"] ?? null;
      $this->data["linkTarget"] =
        $this->data["buttons"][0]["target"] ??
        ($this->data["buttons"][0]["attributeList"]["target"] ?? "_self");
      $this->data["buttons"] = [];
    }
  }
}
