<?php

require dirname(__DIR__) . "/vendor/autoload.php";

use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\HtmlString;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;

// Isolate the template from WordPress while keeping Blade's escaping and loops.
function mx_attrs($attributes) {
  return new HtmlString('class="bg-[--color-background-card]"');
}
function mx_get_icon($name) {
  return new HtmlString("<span>decorative icon</span>");
}
function _e($text, $domain) {
  echo $text;
}

set_error_handler(function ($severity, $message, $file, $line) {
  throw new ErrorException($message, 0, $severity, $file, $line);
});

$files = new Filesystem();
$compiler = new BladeCompiler($files, sys_get_temp_dir());
$template = $compiler->compileString(
  file_get_contents(
    dirname(__DIR__) . "/views/mxui/contacts/contact-details.blade.php",
  ),
);

function renderContact(array $contact): DOMXPath {
  global $files, $template;
  $__env = new Factory(
    new EngineResolver(),
    new FileViewFinder($files, []),
    new Dispatcher(),
  );
  ob_start();
  try {
    eval("?>" . $template);
    $html = ob_get_contents();
  } finally {
    ob_end_clean();
  }
  $dom = new DOMDocument();
  $dom->loadHTML(
    '<meta charset="utf-8">' . $html,
    LIBXML_NOERROR | LIBXML_NOWARNING,
  );
  return new DOMXPath($dom);
}

function expect(bool $condition, string $message): void {
  if (!$condition) {
    throw new RuntimeException($message);
  }
}

$cases = [
  "empty" => [[], 0],
  "address only" => [
    ["address" => "Lilla Torg 1", "other" => "Other details"],
    0,
  ],
  "job only" => [["work_title" => "Rektor"], 1],
  "department only" => [["administration_unit" => "Skola"], 1],
  "false phone" => [["phone" => false], 0],
  "blank fields" => [
    [
      "work_title" => " \t",
      "administration_unit" => " ",
      "email" => " ",
      "phone" => [["number" => " "], []],
      "opening_hours" => " \r\n\t",
    ],
    0,
  ],
  "mixed phones" => [
    ["phone" => [["number" => ""], ["number" => "0413-283 98"]]],
    1,
  ],
  "hours" => [["opening_hours" => "\n Monday 9–17\r\n \nTuesday 9–17\n"], 2],
  "all contact details" => [
    [
      "work_title" => "Rektor",
      "administration_unit" => "Skola",
      "phone" => [["number" => "0413-283 98"]],
      "email" => "gunilla.torell@hoor.se",
    ],
    4,
  ],
];

foreach ($cases as $name => [$contact, $rows]) {
  $xpath = renderContact($contact);
  expect(
    $xpath->query("//li")->length === $rows,
    "$name: unexpected list rows",
  );
  expect(
    $xpath->query("//ul[not(li)] | //li[not(normalize-space())]")->length === 0,
    "$name: empty list or row",
  );
  if ($name === "blank fields") {
    expect(
      $xpath->query("//h3")->length === 0,
      "Blank opening hours must not have a heading",
    );
  }
  if ($name === "address only") {
    expect(
      str_contains($xpath->document->textContent, "Lilla Torg 1") &&
        str_contains($xpath->document->textContent, "Other details"),
      "Independent content was lost",
    );
  }
  foreach (
    $xpath->query('//a[@itemprop="telephone" or @itemprop="email"]')
    as $link
  ) {
    expect(!$link->hasAttribute("aria-label"), "$name: link name overridden");
    expect(
      $xpath->query('./span[@aria-hidden="true"]', $link)->length === 1,
      "$name: icon exposed",
    );
    $text = trim(
      $xpath->query("./span[not(@aria-hidden)]", $link)->item(0)->textContent,
    );
    expect(
      $text !== "" &&
        substr(
          $link->getAttribute("href"),
          strpos($link->getAttribute("href"), ":") + 1,
        ) === $text,
      "$name: contact text and target differ",
    );
  }
}
echo count($cases) . " contact rendering scenarios passed.\n";
