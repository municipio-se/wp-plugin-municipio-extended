<?php

/**
 * Plugin Name: Municipio Extended
 * Description: Adds more features to Municipio.
 * Version: 2025.12.10
 * Author: Whitespace Dev
 * Text Domain: municipio-extended
 * Domain Path: /languages/
 */

define("MUNICIPIO_EXTENDED_PLUGIN_FILE", __FILE__);
define("MUNICIPIO_EXTENDED_PATH", dirname(__FILE__));
define("MUNICIPIO_EXTENDED_URL", rtrim(plugin_dir_url(__FILE__), "/"));
define(
  "MUNICIPIO_EXTENDED_AUTOLOAD_PATH",
  MUNICIPIO_EXTENDED_PATH . "/autoload",
);
define(
  "MUNICIPIO_EXTENDED_IS_MU",
  strpos(MUNICIPIO_EXTENDED_PATH, WPMU_PLUGIN_DIR) === 0,
);
define(
  "MUNICIPIO_EXTENDED_LANGUAGES_PATH",
  plugin_basename(dirname(__FILE__)) . "/languages",
);

add_action(
  "init",
  function () {
    if (MUNICIPIO_EXTENDED_IS_MU) {
      load_muplugin_textdomain(
        "municipio-extended",
        MUNICIPIO_EXTENDED_LANGUAGES_PATH,
      );
    } else {
      load_plugin_textdomain(
        "municipio-extended",
        false,
        MUNICIPIO_EXTENDED_LANGUAGES_PATH,
      );
    }
  },
  -10,
);

array_map(static function () {
  include_once func_get_args()[0];
}, glob(MUNICIPIO_EXTENDED_AUTOLOAD_PATH . "/*.php"));
