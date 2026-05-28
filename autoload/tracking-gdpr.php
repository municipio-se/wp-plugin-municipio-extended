<?php

/**
 * Renders content iframes through MXUI while preserving iframe metadata.
 *
 * The wstg-iframe element forwards iframe-* attributes to the real iframe it
 * creates after consent, so existing titles must stay in the component data.
 */
add_filter(
  "wstg_content_iframe_replacement",
  function ($content, $data) {
    $height = $data["node"]->getAttribute("height") ?: null;
    $title = $data["node"]->getAttribute("title") ?: null;

    return '<div class="tailwind">' .
      mx_render_view("mxui.iframe", [
        "service" => $data["video_service"],
        "id" => $data["video_id"],
        "url" => $data["url"],
        "height" => $height,
        "title" => $title,
      ]) .
      "</div>";
  },
  10,
  2,
);
