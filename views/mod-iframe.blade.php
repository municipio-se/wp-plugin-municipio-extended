@if (!$hideTitle && !empty($postTitle))
  @typography([
      'id' => 'mod-text-' . $ID . '-label',
      'element' => 'h2',
      'variant' => 'h2',
      'classList' => ['module-title']
  ])
    {!! $postTitle !!}
  @endtypography
@endif

<div class="tailwind">
  @component('mxui.iframe', [
      'url' => $url,
      'height' => $height,
      'title' => mx_coalesce_string([
          $description ?? null,
          $postTitle ?? null,
          $post_title ?? null,
      ]) ?: null,
  ])
  @endcomponent
</div>
