<div class="tailwind">
  <ul class="grid grid-cols-[repeat(auto-fill,minmax(min(100%,18rem),1fr))] gap-2 sm:gap-6 peer">
    @foreach ($items as $item)
      <li>
        <a href="{{ $item['href'] }}" target="{{ $item['target'] ?? '_self' }}"
          class="block w-full h-full p-4 bg-complementary-light hover:bg-primary hover:text-primary-contrasting transition-none"
          style="border-radius: var(--radius-lg);">
          <div class="space-y-2">
            <div class="typography-h3 leading-none underline">
              {{ $item['title'] }}
            </div>
            @if (!empty($item['description']))
              <div class="text-xs sm:text-base">
                {{ $item['description'] }}
              </div>
            @endif
          </div>
        </a>
      </li>
    @endforeach
  </ul>
  @if (!empty($empty_message))
    <div class="hidden peer-contentless:block prose">
      {{ mx_safe_html($empty_message) }}
    </div>
  @endif
</div>
