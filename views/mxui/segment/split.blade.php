{{--
containerAware
dateBadge
height
icon
layout
overlay
reverseColumns
sub_title

buttons
--}}

<!-- mxui.segment.split -->
<section
  {{ mx_attrs(
      [
          'class' => [
              'flex flex-row min-h-full group',
              'relative left-1/2 transform -translate-x-1/2 m-0 w-[100vw] rounded-none',
              $classList,
          ],
      ],
      $attributeList,
  ) }}>
  <div class="-mt-8 mx-4 w-auto">
    @component('mxui.card', [
        'heading' => $title,
        'content' => new \Illuminate\Support\HtmlString($content),
        'date' => $date,
        'meta' => $meta,
        'tags' => $tags,
        'link' => $link,
        'attributeList' => ['target' => $linkTarget ?? '_self'],
        'wrapContent' => true,
        'classList' => ['[--card-px:2.5rem] [--card-py:1.5rem] isolate'],
        'expandLinkCover' => true,
    ])
    @endcomponent
  </div>
  <div
    {{ mx_attrs([
        'class' => [
            'w-full',
            '-order-1',
            'aspect-video bg-secondary first:last:mb-0',
            'overflow-hidden border-none rounded-[var(--c-segment-image-border-radius,var(--radius-lg,calc(var(--base,8px)*1.5)))]',
        ],
    ]) }}>
    @component('mxui.image', [
        'image' => $image,
        'size' => 'medium',
        'classList' => 'group-hover:scale-105 transition-transform duration-500 text-transparent',
    ])
    @endcomponent
  </div>
</section>
