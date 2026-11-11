{{--
Left to handle:

'meta' => $post->readingTime,
'metaFirst' => true,
'containerAware' => true,
'hasAction' => true,
'postId' => $post->id,
'postType' => $post->postType,
'icon' => $post->termIcon,
--}}

@php
  // Navigation attributes belong on the heading link, not the card's div wrapper.
  $linkAttributes = array_intersect_key($attributeList ?? [], array_flip(['target', 'rel', 'download', 'hreflang', 'referrerpolicy']));
  $attributeList = array_diff_key($attributeList ?? [], $linkAttributes);
  $asSubgrid ??= false;
  $classList ??= null;
  $content ??= $slot ?? null;
  $date ??= null;
  $dateBadge ??= false;
  $heading ??= null;
  $headingLevel ??= 3;
  $headingVariant ??= 'typography-h3';
  $image ??= null;
  $imageAspectRatio ??= 'video';
  $link ??= null;
  $asTemplate ??= false;
  $wrapContent ??= !empty($content) && (is_string($content) || $asTemplate);
  $proseWrap ??= false;
  $expandLinkCover ??= false;
  $buttons ??= null;
  $overflowVisible ??= false;
  $modifier ??= null;
@endphp
<!-- mxui.card -->

<div
  {{ mx_attrs(
      [
          'class' => [
              'grid content-start rounded-[var(--c-card-border-radius,var(--radius-lg,calc(var(--base,8px)*1.5)))] bg-[--color-background-card] hover:bg-[var(--color-background-card-hover,var(--color-background-card))] hover:text-[var(--color-text-card-hover,var(--color-black))] grid-cols-[100%]',
              'border border-[length:var(--border-width-card,0)] border-[color:var(--color-border-card)]',
              'relative group' => !$expandLinkCover,
              'grid-rows-subgrid' => $asSubgrid,
              'grid-rows-[auto,auto,1fr,auto]' => !$asSubgrid,
              'w-full',
              'overflow-hidden' => !$overflowVisible,
  
              // In some places (e.g. contact module in list mode), headers are added via $content, so we cannot style them in-place. Hence the weird selectors here.
              '[&_.c-card\_\_header]:bg-primary [&_.c-card\_\_header]:text-primary-contrasting' => $modifier == 'panel',
              '[&_.c-card\_\_header]:border-b-[length:calc(var(--base,8px)/2)] [&_.c-card\_\_header]:border-b-[color:var(--color-primary)]' =>
                  $modifier == 'accented',
  
              // Replacement for `.c-card .c-collection { border: none; }`
              '[&_.c-collection]:border-none',
              '@container',
  
              $classList,
          ],
      ],
      $attributeList ?? [],
  ) }}>
  @if (mxui_debug_enabled())
    <div class="absolute top-0 right-0 w-full m-1 opacity-50 group-hover:opacity-100 flex justify-end flex-wrap gap-1">
      @foreach (['wrapContent', 'proseWrap', 'context'] as $var)
        <span class="bg-black rounded p-1 leading-none text-[.625rem] text-white font-mono">{{ $var }}:
          {{ json_encode($$var) }}</span>
      @endforeach
    </div>
  @endif
  @if (!empty($date) || !empty($heading) || !empty($meta))
    {{-- CardHeader --}}
    <div
      {{ mx_attrs([
          'class' => [
              'row-start-2',
              'row-span-1',
              'space-y-2',
              'px-[var(--card-px,1rem)] py-[var(--card-py,1rem)]',
              'border-l-[length:var(--base,8px)] border-l-[color:var(--color-primary)]' => $modifier == 'highlight',
              'border-b-[length:calc(var(--base,8px)/2)] border-b-[color:var(--color-primary)]' => $modifier == 'accented',
              'bg-primary text-primary-contrasting' => $modifier == 'panel',
          ],
      ]) }}>
      @if (!empty($heading))
        {{-- CardTitle --}}
        <h{!! $headingLevel !!} class={{ clsx($headingVariant) }}>
          {{-- CardClickable --}}
          @include('mxui.clickable', [
              'link' => $asTemplate ? ($link === true ? '/___' : $link) : $link ?? null,
              'content' => $asTemplate ? ($heading === true ? '' : $heading) : $heading ?? null,
              'classList' =>
                  'no-prose no-underline interactive:underline after:absolute after:inset-0 after:z-[1] after:inert:hidden hover:visited:text-inherit transition-none break-words hyphens-auto',
              'attributes' => [
                  ...$linkAttributes,
                  'data-mxui-card-link' => '',
                  'slot' => $asTemplate ? 'link title' : null,
              ],
          ])
          </h{!! $headingLevel !!}>
      @endif
      @if (!empty($date))
        <div
          {{ mx_attrs([
              'class' => ['flex items-center gap-1 text-sm text-gray-500'],
              'slot' => $asTemplate ? 'date' : null,
          ]) }}>
          {{-- <Icon class="text-deep-blue" name="calendar" /> --}}
          {{ $asTemplate ? ($date === true ? '' : $date) : $date }}
        </div>
      @endif
      @if (!empty($meta))
        <div class="flex items-center gap-1 text-sm text-gray-500">
          {{-- <Icon class="text-deep-blue" name="calendar" /> --}}
          {!! $meta !!}
        </div>
      @endif
    </div>
  @endif
  @if (!empty($image))
    {{-- CardMedia --}}
    <div
      {{ mx_attrs([
          'class' => [
              'w-full',
              'row-start-1',
              'row-span-1',
              'bg-secondary first:last:mb-0',
              'overflow-hidden border-none',
              [
                  'aspect-video' => $imageAspectRatio == 'video',
                  'aspect-square' => $imageAspectRatio == 'square',
              ],
          ],
      ]) }}>
      @component('mxui.image', [
          'image' => $image,
          'size' => 'medium',
          'classList' =>
              'group-hover:group-has-[[data-mxui-card-link][data-mxui-interactive]]:scale-105 transition-transform duration-500 text-transparent',
          'attributeList' => [
              'slot' => $asTemplate ? 'image' : null,
          ],
      ])
      @endcomponent
    </div>
    @if ($dateBadge)
      <div aria-hidden="true" class="absolute left-0 top-0 m-4 w-auto">
        @component('mxui.datebadge', [
            'date' => $date,
            'classList' => [],
        ])
        @endcomponent
      </div>
    @endif
  @endif
  @if (!empty($content))
    {{-- CardContent --}}
    <div
      {{ mx_attrs([
          'class' => [
              'row-start-3',
              'row-span-1',
              'px-[var(--card-px,1rem)] pb-[var(--card-py,1rem)]' => $wrapContent,
              'border-l-[length:var(--base,8px)] border-l-[color:var(--color-primary)]' => $modifier == 'highlight',
          ],
          'slot' => $asTemplate ? 'content' : null,
      ]) }}>
      @if ($proseWrap)
        <div class="prose">
      @endif
      @if (is_string($content))
        <p>{{ $content }}</p>
      @else
        {!! $asTemplate ? ($content === true ? '' : $content) : $content !!}
      @endif
      @if ($proseWrap)
    </div>
  @endif
  @if ($buttons)
    <ul class="px-0 space-y-0 flex flex-wrap gap-4 mt-4">
      @foreach ($buttons as $button)
        <li class="">
          @component('mxui.button', [
              'href' => $button['href'],
              'target' => $button['target'] ?? $button['attributeList']['target'] ?? '_self',
              'attributes' => $button['attributeList'] ?? [],
              'variant' => $button['color'] ?? ($button['buttonVariant'] ?? 'default'),
              'classList' => 'h-auto',
          ])
            @if ($button['icon'])
              <span class="min-h-[1lh] flex-none flex items-center">
                @icon([
                    'icon' => $button['icon']['name'] ?? $button['icon'],
                    'classList' => ['block text-[1.5rem] leading-none flex-none']
                ])
                @endicon
              </span>
            @endif
            <span class="text-base flex-grow flex-shrink">
              {{ $button['text'] ?? $button['title'] }}
            </span>
          @endcomponent
        </li>
      @endforeach
    </ul>
  @endif
</div>
@endif
@if (!empty($tags))
  {{-- CardFooter --}}
  <div
    {{ mx_attrs([
        'class' => [
            'row-start-4',
            'row-span-1',
            'px-[var(--card-px,1rem)] py-[var(--card-py,1rem)]',
            'border-l-[length:var(--base,8px)] border-l-[color:var(--color-primary)]' => $modifier == 'highlight',
        ],
    ]) }}>
    @component('mxui.taglist', [
        'tags' => $tags,
        'classList' => [],
    ])
    @endcomponent
  </div>
@endif
</div>
