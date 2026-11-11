<div class="tailwind">
  <div class="o-container">
    <ul class="grid grid-cols-[repeat(auto-fit,minmax(min(100%,7rem),10rem))] gap-4 space-y-0 mx-auto justify-center">
      @foreach ($items as $item)
        <li class="contents">
          <a href="{{ $item['href'] }}" target="{{ $item['target'] ?? '_self' }}"
            class="border border-primary hover:border-black text-link hover:text-black grid grid-rows-subgrid row-span-2 gap-2 items-center visited:hover:text-black text-center p-3 hover:bg-[#0001] rounded-lg transition-colors">
            <span class="text-[40px] leading-none">
              {{ mx_get_icon($item['icon'] ?: 'arrow_forward', [
                  'classList' => ['block'],
              ]) }}
            </span>
            <div class="text-sm font-medium text-inherit group-hover:text-inherit">
              {{ $item['title'] }}
            </div>
          </a>
        </li>
      @endforeach
    </ul>
  </div>
</div>
