<div class="tailwind">
  <div class="bg-primary">
    <div class="o-container">
      <ul class="grid grid-cols-[repeat(auto-fit,minmax(min(100%,7rem),1fr))] space-y-0 mx-auto justify-center">
        @foreach ($items as $item)
          <li class="contents">
            <a href="{{ $item['href'] }}" target="{{ $item['target'] ?? '_self' }}"
              class="text-primary-contrasting hover:text-primary-contrasting grid grid-rows-subgrid row-span-2 gap-2 items-center visited:hover:text-primary-contrasting text-center py-5 px-2 hover:bg-primary-dark transition-colors">
              <span class="text-[54px] leading-none">
                {{ mx_get_icon($item['icon'] ?: 'arrow_forward', [
                    'classList' => ['block'],
                ]) }}
              </span>
              <div class="text-sm font-medium underline">
                {{ $item['title'] }}
              </div>
            </a>
          </li>
        @endforeach
      </ul>
    </div>
  </div>
</div>
