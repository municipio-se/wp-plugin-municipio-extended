<!-- mxui.navigation.inline -->
<div class="tailwind">
  <div class="bg-primary [&:not(:has(ul>li))]:hidden">
    <ul class="p-2 space-y-0 flex flex-wrap gap-2 justify-start md:justify-center">
      @foreach ($items as $item)
        <li class="contents">
          <a href="{{ $item['href'] }}" target="{{ $item['target'] ?? '_self' }}"
            class="items-center text-primary-contrasting hover:text-primary-contrasting inline-grid group grid-cols-[max-content_1fr] gap-2 visited:hover:text-primary-contrasting p-3 hover:bg-primary-dark transition-colors leading-snug rounded">
            <span class="text-[1.25em] -m-1">
              {{ mx_get_icon($item['icon'] ?: 'arrow_forward') }}
            </span>
            <span class="text-md underline translate-y-[-.0625em]">
              {{ $item['title'] }}
            </span>
          </a>
        </li>
      @endforeach
      <template>
        <li class="">
          <a href="#"
            class="items-center text-primary-contrasting hover:text-primary-contrasting inline-grid group grid-cols-[max-content_1fr] gap-2 visited:hover:text-primary-contrasting p-3 hover:bg-primary-dark transition-colors leading-snug rounded"
            slot="link">
            <span class="text-[1.25em] -m-1">
              {{ mx_get_icon('arrow_forward') }}
            </span>
            <span class="text-md underline translate-y-[-.0625em]" slot="title"></span>
          </a>
        </li>
      </template>
    </ul>
  </div>
</div>
