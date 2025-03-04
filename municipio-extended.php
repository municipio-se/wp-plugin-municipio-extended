<?php

/**
 * Plugin Name: Municipio Extended
 * Description: Adds more features to Municipio.
 * Version: 24.110.0
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
  "MUNICIPIO_EXTENDED_LANGUAGES_PATH",
  plugin_basename(dirname(__FILE__)) . "/languages",
);

add_action("plugins_loaded", function () {
  load_plugin_textdomain(
    "municipio-extended",
    false,
    MUNICIPIO_EXTENDED_LANGUAGES_PATH,
  );
});

add_action("muplugins_loaded", function () {
  load_muplugin_textdomain(
    "municipio-extended",
    MUNICIPIO_EXTENDED_LANGUAGES_PATH,
  );
});

array_map(static function () {
  include_once func_get_args()[0];
}, glob(MUNICIPIO_EXTENDED_AUTOLOAD_PATH . "/*.php"));
