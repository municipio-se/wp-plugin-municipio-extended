<div class="tailwind">
  <ul class="divide-y space-y-0 *:border-border-divider my-8">
    @foreach ($items as $item)
      <li class="p-0 flex gap-[--grid-gap] py-8 first:pt-0 last:pb-0">
        <div class="w-[180px] flex-none">
          <div class="@[40rem]:row-span-2 col-span-1 @[40rem]:-ml-4 @[40rem]:-my-4 @[40rem]:w-[calc(100%+1rem)]">
            @if ($item['image'] ?? null)
              @component('mxui.image', [
                  'image' => $item['image'],
                  'size' => 'medium',
                  'sizes' => '180px',
                  'classList' => 'block w-full',
              ])
              @endcomponent
            @endif
          </div>
        </div>
        <div class="space-y-4">
          <div class="">
            <a href="{{ $item['href'] }}" target="{{ $item['target'] ?? '_self' }}"
              class="text-h2 leading-none font-medium underline text-link hover:text-link-hover">
              {{ $item['title'] }}
            </a>
          </div>
          @if (!empty($item['description']))
            <p class="">
              {{ $item['description'] }}
            </p>
          @endif
          @if (!empty($item['children']))
            <ul class="flex flex-wrap gap-2 space-y-0">
              @foreach ($item['children'] as $child)
                <li class="p-0">
                  <a href="{{ $child['href'] }}" target="{{ $child['target'] ?? '_self' }}"
                    class="text-h3 block p-3 leading-none rounded bg-secondary text-secondary-contrasting hover:bg-secondary-dark visited:hover:text-white transition-colors">
                    {{ $child['title'] }}
                  </a>
                </li>
              @endforeach
            </ul>
          @endif
        </div>
      </li>
    @endforeach
  </ul>
</div>
