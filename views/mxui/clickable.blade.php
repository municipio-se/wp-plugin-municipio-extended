@php
  $link = is_string($link ?? null) ? ['href' => $link] : $link ?? null;
  $href = $href ?? null ?: $link['href'] ?? null ?: $link['url'] ?? null;
  $content = $content ?? null ?: $link['content'] ?? null ?: $link['text'] ?? null ?: $label ?? null ?: $slot ?? null;
  $type = $type ?? (!empty($href) ? 'link' : (!empty($onclick) ? 'button' : null));
@endphp

@if ($type === 'link')
  <a
    {{ mx_attrs(
        [
            'href' => $href,
            'target' => $target ?? $link['target'] ?? null,
            'rel' => $rel ?? $link['rel'] ?? null,
            'class' => [$classList ?? null],
            'data-mxui-clickable' => '',
            'data-mxui-interactive' => true,
        ],
        $attributes ?? null,
    ) }}>
    {{ $content }}
  </a>
@elseif ($type === 'button' || $type === 'submit' || $type === 'reset')
  <button
    {{ mx_attrs(
        [
            'onclick' => $onclick,
            'class' => [$classList ?? null],
            'data-mxui-clickable' => '',
            'data-mxui-interactive' => true,
        ],
        $attributes ?? null,
        [
            'type' => $type,
        ],
    ) }}>
    {{ $content }}
  </button>
@else
  <span
    {{ mx_attrs(
        [
            'class' => [$classList ?? null],
            'data-mxui-clickable' => '',
        ],
        $attributes ?? null,
    ) }}>{{ $content }}</span>
@endif
