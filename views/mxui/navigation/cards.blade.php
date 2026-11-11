<div class="tailwind">
  <ul
    class="grid grid-cols-[repeat(auto-fill,minmax(min(100%,16rem),1fr))] gap-[--grid-gap] space-y-0 [--color-background-card:var(--color-complementary-light)] [--color-background-card-hover:var(--color-primary)] [--color-text-card-hover:var(--color-primary-contrasting)] peer">
    @foreach ($items as $item)
      @php
        $content = wp_trim_words($item['post']->post_content ?? '', 8, '...');
      @endphp
      <li>
        @card([
            'link' => $item['href'],
            'imageFirst' => true,
            'heading' => $item['title'],
            'content' => $content,
            'tags' => $item['termsUnlinked'] ?? '',
            'meta' => $item['readingTime'] ?? '',
            'date' => $item['postDateFormatted'] ?? '',
            'dateBadge' => $item['dateBadge'] ?? '',
            'classList' => ['u-height--100'],
            'containerAware' => true,
            'hasAction' => true,
            'hasPlaceholder' => $item['hasPlaceholderImage'] ?? false,
            'image' => $item['image'] ?? '',
            'postId' => $item['id'] ?? '',
            'postType' => $item['postType'] ?? '',
            'attributeList' => [...($item['attributeList'] ?? []), 'target' => $item['target'] ?? '_self'],
            'useHbg' => false
        ])
          @includeWhen(!empty($post->callToActionItems['floating']), 'partials.floating')
        @endcard
      </li>
    @endforeach
  </ul>
  @if (!empty($empty_message))
    <div class="hidden peer-contentless:block prose">
      {{ mx_safe_html($empty_message) }}
    </div>
  @endif
</div>
