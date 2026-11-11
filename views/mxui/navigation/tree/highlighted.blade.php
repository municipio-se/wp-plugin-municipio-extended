<div class="tailwind">
  <ul class="space-y-12  {{ '@container' }}">
    @foreach ($items as $item)
      <li class="grid grid-cols-[min(200px,33%)_minmax(0,1fr)] grid-rows-[auto_auto_auto] gap-7">
        <!-- Wrapper div for Image and Text section -->
        <div
          class="relative group grid grid-cols-subgrid grid-rows-subgrid row-span-2 col-span-2 bg-complementary-light hover:bg-primary gap-x-7 gap-y-4 p-4">
          <!-- Image Section -->
          <div class="@[40rem]:row-span-2 col-span-1 @[40rem]:-ml-4 @[40rem]:-my-4 @[40rem]:w-[calc(100%+1rem)]">
            @if ($item['image'] ?? null)
              @component('mxui.image', [
                  'image' => $item['image'],
                  'size' => 'medium',
                  'sizes' => '200px',
                  'classList' => 'aspect-video h-full',
              ])
              @endcomponent
            @endif
          </div>

          <!-- Text Section (Coloured Section) -->
          <div class="self-center">
            <a href="{{ $item['href'] }}" target="{{ $item['target'] ?? '_self' }}"
              class="text-base inline-flex items-center after:absolute after:inset-0 after:z-[1] after:inert:hidden">
              <span class="typography-h3 underline group-hover:text-primary-contrasting">{{ $item['title'] }}</span>
            </a>
          </div>
          @if (!empty($item['description']))
            <div class="text-xs sm:text-base col-span-2 @[40rem]:col-span-1 group-hover:text-primary-contrasting">
              {{ $item['description'] }}
            </div>
          @endif
        </div>

        <!-- Links Section aligned with the text section  -->
        @if (!empty($item['children']))
          <ul class="flex flex-wrap gap-7 space-y-0 col-span-2 @[40rem]:col-start-2 @[40rem]:col-span-1">
            @foreach ($item['children'] as $child)
              <li class="p-0">
                <a href="{{ $child['href'] }}" target="{{ $child['target'] ?? '_self' }}"
                  class="block leading-none text-link visited:text-link underline transition-colors hover:text-link-hover hover:visited:text-link-hover">
                  {{ $child['title'] }}
                </a>
              </li>
            @endforeach
          </ul>
        @endif
      </li>
    @endforeach
  </ul>
</div>
