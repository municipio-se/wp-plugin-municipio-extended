<?php

add_filter(
  "pre_attachment_url_to_postid",
  function (int|null $post_id, string $url) {
    $transient_key = "attachment_url_to_postid_" . md5($url);
    $cached = get_transient($transient_key);
    if ($cached !== false) {
      return $cached;
    }
    return null; // Let WP continue if not cached
  },
  10,
  2,
);

add_filter(
  "attachment_url_to_postid",
  function ($post_id, $url) {
    if ($post_id) {
      $transient_key = "attachment_url_to_postid_" . md5($url);
      // Cache for 1 year (60*60*24*365 seconds)
      set_transient($transient_key, $post_id, YEAR_IN_SECONDS);
    }
    return $post_id;
  },
  10,
  2,
);

// Clear transient cache when an attachment is deleted
add_action("delete_attachment", function ($post_id) {
  $url = wp_get_attachment_url($post_id);
  if ($url) {
    $transient_key = "attachment_url_to_postid_" . md5($url);
    delete_transient($transient_key);
  }
});

// Clear transient cache if the attachment URL changes
add_action(
  "attachment_updated",
  function ($post_id, $post_after, $post_before) {
    $old_url = isset($post_before->ID)
      ? wp_get_attachment_url($post_before->ID)
      : null;
    $new_url = wp_get_attachment_url($post_id);
    if ($old_url && $old_url !== $new_url) {
      $transient_key = "attachment_url_to_postid_" . md5($old_url);
      delete_transient($transient_key);
    }
    // Optionally clear the new URL as well, in case it was previously cached
    if ($new_url && $old_url !== $new_url) {
      $transient_key = "attachment_url_to_postid_" . md5($new_url);
      delete_transient($transient_key);
    }
  },
  10,
  3,
);
