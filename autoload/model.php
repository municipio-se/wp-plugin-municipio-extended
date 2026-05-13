<?php

function mx_get_model($class, ...$args) {
  return \MunicipioExtended\Model\Model::create($class, ...$args);
}

function mx_get_post($post_id = null, ...$args) {
  if (!$post_id) {
    $post_id = get_the_ID();
  }
  if (!$post_id) {
    return null;
  }
  try {
    return mx_get_model("WpPost", $post_id, ...$args);
  } catch (\Exception $e) {
    return null;
  }
}

function mx_get_post_meta($post_id = null, ...$args) {
  if (!$post_id) {
    $post_id = get_the_ID();
  }
  if (!$post_id) {
    return null;
  }
  return mx_get_model("WpPostMeta", $post_id, ...$args);
}

function mx_get_post_type($post_type = null, ...$args) {
  if (!$post_type) {
    $post = get_post();
    if (!$post) {
      return null;
    }
    $post_type = $post->post_type;
  }
  if (!$post_type) {
    return null;
  }
  return mx_get_model("WpPostType", $post_type, ...$args);
}

function mx_get_menu_item($post_id = null, ...$args) {
  if (!$post_id) {
    $post_id = get_the_ID();
  }
  if (!$post_id) {
    return null;
  }
  return mx_get_model("WpMenuItem", $post_id, ...$args);
}

function mx_get_image($attachment_id, $size = null) {
  if (!$attachment_id) {
    return null;
  }
  if (mx_is_image($attachment_id)) {
    return $attachment_id->toSize($size);
  }
  return mx_get_model("WpImage", $attachment_id, $size);
}

function mx_is_image($value) {
  return $value instanceof \MunicipioExtended\Model\WpImageInterface;
}

function mx_get_icon($input, ...$args) {
  if (!$input) {
    return null;
  }
  if (mx_is_icon($input)) {
    return $input;
  }
  return mx_get_model("Icon", $input, ...$args);
}

function mx_is_icon($value) {
  return $value instanceof \MunicipioExtended\Model\IconInterface;
}

function mx_date($value, ...$args) {
  if (!$value) {
    return null;
  }
  if ($value instanceof \MunicipioExtended\Model\Date) {
    if (!empty($args)) {
      return $value->toFormat($args[0]);
    }
    return $value;
  }
  if (is_string($value)) {
    $timezone = function_exists("wp_timezone")
      ? wp_timezone()
      : new DateTimeZone(wp_timezone_string() ?: "UTC");

    $date = date_create_immutable($value, $timezone);
    $value = $date ? $date->getTimestamp() : strtotime($value);
  }
  return mx_get_model("Date", $value, ...$args);
}
