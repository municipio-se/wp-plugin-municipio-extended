<div class="tailwind">
  <ul class="px-0 space-y-1 peer">
    @foreach ($items as $item)
      <li class="">
        <a href="{{ $item['href'] }}" target="{{ $item['target'] ?? '_self' }}"
          class="flex gap-1 text-link visited:text-link hover:text-link-hover transition-none">
          <span class="min-h-[1lh] flex-none self-start flex items-center">
            {{ mx_get_icon($item['icon']['name'] ?? $item['icon'] ?: 'arrow_forward', [
                'classList' => ['block text-[1.5rem] leading-none flex-none'],
            ]) }}
          </span>
          <span class="flex-grow flex-shrink self-center underline">
            {{ $item['title'] }}
          </span>
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
