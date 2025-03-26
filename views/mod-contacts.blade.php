<div class="tailwind contents">
  @if (!$hideTitle && !empty($postTitle))
    @typography([
        'element' => 'h2',
        'classList' => ['module-title']
    ])
      {!! apply_filters('the_title', $postTitle) !!}
    @endtypography
  @endif
  <div class="o-grid">
    @foreach ($contacts as $contact)
      <div class="o-grid-12 {{ $columns }}">
        @component('mxui.card', [
            'content' => mx_safe_html(
                mx_render_view('mxui.contacts.contact-details', [
                    'contact' => $contact,
                    'headingLevel' => !$hideTitle && !empty($postTitle) ? 4 : 3,
                ])),
            'headingLevel' => !$hideTitle && !empty($postTitle) ? 3 : 2,
            'heading' =>
                $contact['first_name'] .
                ' ' .
                (isset($contact['last_name']) && !empty($contact['last_name']) ? $contact['last_name'] : ''),
            'wrapContent' => true,
            'image' =>
                !empty($contact['image']) && is_array($contact['image'])
                    ? array_merge($contact['image'], [
                        'src' => $contact['image']['url'],
                        'alt' => $contact['image']['alt'],
                        'classList' => ['min-h-96'],
                        'size' => ['large'],
                    ])
                    : [],
            'imageAspectRatio' => 'square',
            'overflowVisible' => true,
        ])
        @endcomponent
      </div>
    @endforeach
  </div>
</div>
