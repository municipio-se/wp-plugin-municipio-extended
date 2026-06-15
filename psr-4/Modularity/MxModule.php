<?php

namespace MunicipioExtended\Modularity;

use Kirki\Compatibility\Kirki;
use Municipio\Customizer;
use Municipio\Customizer\KirkiPanelSection;

class MxModule extends \Modularity\Module {
  public function __construct(\WP_Post $post = null, $args = []) {
    parent::__construct($post, $args);
    $this->registerCustomizations();
  }

  protected function registerCustomizations() {
    $section_id = "municipio_customizer_section_mod_" . $this->slug;
    $section = KirkiPanelSection::create();
    $section->setPanel("municipio_customizer_panel_design_module");
    $section->setID($section_id);
    $section->setTitle($this->nameSingular);
    $section->setActiveCallback(fn() => post_type_exists("mod-" . $this->slug));
    $section->setFieldsCallback(
      fn() => $this->addCustomizationFields($section_id),
    );
    $section->register();
  }

  protected function getCustomizationFields() {
    return [];
  }

  protected function addCustomizationFields($section_id) {
    $fields = $this->getCustomizationFields();
    if (empty($fields)) {
      return;
    }
    foreach ($fields as $field) {
      Kirki::add_field(
        Customizer::KIRKI_CONFIG,
        $field + [
          "section" => $section_id,
        ],
      );
    }
  }

  /**
   * Get metadata for block or module.
   * @return array
   */
  protected function getFields() {
    $fields = parent::getFields() ?: [];
    $fields = array_combine(
      array_map(function ($key) {
        return preg_replace("/^mod_" . $this->slug . "_/", "", $key);
      }, array_keys($fields)),
      $fields,
    );
    return $fields;
  }

  /**
   * The post this module is rendered in the context of.
   * @return \WP_Post|null
   */
  protected function getCurrentPost() {
    /**
     * Filters the post a module is rendered in the context of.
     *
     * Defaults to the global `$post`, which is empty outside the loop
     * (e.g. REST requests) — hook in to supply the page being rendered.
     *
     * @param \WP_Post|null $post   Context post.
     * @param MxModule      $module Module instance.
     */
    return apply_filters("mx/module/current_post", get_post(), $this);
  }

  /**
   * Data array
   * @return array $data
   */
  public function data(): array {
    $kirki_fields = $this->getCustomizationFields();
    $data = [];
    foreach ($kirki_fields as $field) {
      $key = preg_replace(
        "/^mod_" . $this->slug . "_/",
        "",
        $field["settings"],
      );
      $data[$key] = Kirki::get_option("theme_custom", $field["settings"]);
    }
    $field_data = (array) $this->getFields();
    $data = array_merge($data, $field_data);
    return $data;
  }
}
