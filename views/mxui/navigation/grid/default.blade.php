<div class="tailwind">
  <ul class="grid grid-cols-[repeat(auto-fill,minmax(min(100%,18rem),1fr))] gap-[--grid-gap] space-y-0 peer">
    @foreach ($items as $item)
      <li class="p-0" style="--color-custom: {!! $item['color'] ?: 'var(--color-primary)' !!}">
        <a href="{{ $item['href'] }}" target="{{ $item['target'] ?? '_self' }}"
          class="grid group grid-cols-[max-content_1fr] gap-4 items-center hover:text-inherit visited:hover:text-inherit">
          <div class="self-start">
            <span
              class="bg-custom text-white rounded-full size-[1.5em] grid items-center justify-center text-[40px] group-hover:bg-custom-tint-100 group-active:bg-custom-shade-100 transition-colors">
              {{ mx_get_icon($item['icon']['name'] ?? $item['icon'] ?: 'arrow_forward', [
                  'classList' => ['block'],
              ]) }}
            </span>
          </div>
          <div class="space-y-2">
            <div class="text-h2 leading-none text-link group-hover:text-link-hover underline transition-colors">
              {{ $item['title'] }}
            </div>
            @if (!empty($item['description']))
              <div class="">
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
