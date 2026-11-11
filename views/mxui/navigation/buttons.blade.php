<div class="tailwind">
  <ul class="px-0 space-y-0 flex flex-wrap gap-4 contentless:hidden peer">
    @foreach ($items as $item)
      <li class="">
        @component('mxui.button', [
            'href' => $item['href'],
            'target' => $item['target'] ?? '_self',
            'variant' => $item['buttonVariant'] ?? 'default',
            'classList' => ['font-normal px-4'],
        ])
          @if ($item['icon'])
            <span class="min-h-[1lh] flex-none flex items-center">
              {{ mx_get_icon($item['icon']['name'] ?? $item['icon'], [
                  'classList' => ['block text-[1.5rem] leading-none flex-none'],
              ]) }}
            </span>
          @endif
          <span class="text-base flex-grow flex-shrink">
            {{ $item['title'] }}
          </span>
        @endcomponent
      </li>
    @endforeach
    <template>
      <li class="">
        @component('mxui.button', [
            'href' => '#',
            // 'variant' => $item['buttonVariant'] ?? 'default',
            'attributes' => [
                'slot' => 'link',
            ],
        ])
          <span class="text-base flex-grow flex-shrink" slot="title"></span>
        @endcomponent
      </li>
    </template>
  </ul>
  @if (!empty($empty_message))
    <div class="hidden peer-contentless:block prose">
      {{ mx_safe_html($empty_message) }}
    </div>
  @endif
</div>
