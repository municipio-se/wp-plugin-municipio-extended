<?php
add_filter("wp_mail_from", function ($email) {
  return getenv("WP_MAIL_FROM") ?: "no-reply@whitespace.email";
});

add_filter("wp_mail_from_name", function ($name) {
  return getenv("WP_MAIL_FROM_NAME") ?: "Whitespace";
});
