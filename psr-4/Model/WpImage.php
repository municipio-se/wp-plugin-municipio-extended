<?php

namespace MunicipioExtended\Model;

class WpImage extends WpPost implements WpImageInterface {
  protected $size;

  public function __construct($post, $size = null, $data = []) {
    if (is_array($post) && isset($post["src"])) {
      $data = array_merge($data, $post);
      $post = $post["src"];
    }
    if (is_string($post) && !is_numeric($post)) {
      $post = attachment_url_to_postid($post) ?: $post;
    }
    if (
      is_string($post) &&
      !is_numeric($post) &&
      preg_match("/^(.+?)(-scaled)?(-\d+x\d+)?(\.[a-z0-9]+)$/", $post, $matches)
    ) {
      $candidates = [
        $matches[1] . $matches[4],
        $matches[1] . $matches[2] . $matches[4],
      ];
      foreach ($candidates as $candidate) {
        $matching_post = attachment_url_to_postid($candidate) ?: null;
        if ($matching_post) {
          $post = $matching_post;
          break;
        }
      }
      if ($post && $matches[3]) {
        $meta = get_post_meta($post, "_wp_attachment_metadata", true);
        if (
          isset($meta["sizes"]) &&
          is_array($meta["sizes"]) &&
          !empty($meta["sizes"])
        ) {
          foreach ($meta["sizes"] as $key => $value) {
            if (
              "-" . $value["width"] . "x" . $value["height"] ===
              $matches[3]
            ) {
              $size = $key;
              break;
            }
          }
        }
        $size ??= substr($matches[3], 1);
      }
    }
    $size ??= "thumbnail";
    parent::__construct($post, $data);
    $this->size = $size;
  }

  protected static function getAllImageSizes() {
    global $_wp_additional_image_sizes;
    $default_image_sizes = get_intermediate_image_sizes();
    $additional_image_sizes = array_keys($_wp_additional_image_sizes);
    return array_merge($default_image_sizes, $additional_image_sizes);
  }

  public function toSize(?string $size) {
    if (!$size) {
      return $this;
    }
    return new self($this->post_id, $size, $this->data);
  }

  public function get(string $name): mixed {
    $image_sizes = self::getAllImageSizes();
    if (in_array($name, $image_sizes)) {
      return $this->toSize($name);
    }
    return parent::get($name);
  }

  public function has(string $name): bool {
    $image_sizes = self::getAllImageSizes();
    return in_array($name, $image_sizes) || parent::has($name);
  }

  public function getSrc() {
    return wp_get_attachment_image_url($this->post_id, $this->size);
  }

  public function getSrcset() {
    return wp_get_attachment_image_srcset($this->post_id, $this->size);
  }

  public function getAlt() {
    return get_post_meta($this->post_id, "_wp_attachment_image_alt", true);
  }
}
