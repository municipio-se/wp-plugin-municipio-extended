@if ($posts)
  @php
    $taxonomies = array_reduce(
        $posts,
        function ($carry, $post) {
            return array_merge(
                $carry,
                array_map(function ($term) {
                    return $term['taxonomy'];
                }, $post->termsUnlinked),
            );
        },
        [],
    );
    $taxonomies = array_unique($taxonomies, SORT_REGULAR);
    $taxonomies = array_map(function ($taxonomy) {
        return [
            'taxonomy' => $taxonomy,
            'label' => get_taxonomy($taxonomy)->labels->name,
        ];
    }, $taxonomies);
    $meta_fields = array_reduce(
        $posts,
        function ($carry, $post) {
            return array_merge(
                $carry,
                array_map(function ($meta_field) {
                    return $meta_field['field'];
                }, $post->metaValues ?? []),
            );
        },
        [],
    );
    $meta_fields = array_unique($meta_fields, SORT_REGULAR);
    $meta_fields = array_map(function ($meta_field) {
        return [
            'field' => $meta_field,
            /**
             * Filters the display label for archive meta fields.
             *
             * @param string $label      Generated meta field label.
             * @param string $meta_field Meta field key.
             * @return string Filtered meta field label.
             */
            'label' => apply_filters(
                'mx/meta_field/label',
                ucfirst(preg_replace('/_/', ' ', $meta_field)),
                $meta_field,
            ),
        ];
    }, $meta_fields);
    $show_date = !empty($posts[0]->archiveDate);
  @endphp
  <div class="tailwind contents">
    <div class=" {{ '@container/table' }}">
      <div class="{{ clsx('@[43rem]/table:grid', mx_grid_cols_class(2)) }}">
        <div class="hidden @[43rem]/table:grid col-start-1 -col-end-1 grid-cols-subgrid p-3 gap-3 mt-0 font-medium"
          aria-hidden="true">
          <div>{{ get_post_type_object($posts[0]->postType)->labels->singular_name }}</div>
          <div
            class="{{ clsx('col-start-2 -col-end-1 grid gap-2', mx_grid_cols_class(((int) $show_date) + count($taxonomies) + count($meta_fields))) }}">
            @if ($show_date)
              <div class="">Datum</div>
            @endif
            @foreach ($meta_fields as $meta_field)
              <div>{{ $meta_field['label'] }}</div>
            @endforeach
            @foreach ($taxonomies as $taxonomy)
              <div>{{ $taxonomy['label'] }}</div>
            @endforeach
          </div>
        </div>
        <ul class="grid col-start-1 -col-end-1 grid-cols-subgrid mt-0">
          @foreach ($posts as $post)
            <li class="odd:bg-layer grid col-start-1 -col-end-1 grid-cols-subgrid p-3 gap-3 mt-0">
              <div class="font-medium">
                @component('mxui.clickable', [
                    'href' => $post->permalink,
                    'classList' => ['typography-link'],
                ])
                  {{ $post->postTitle }}
                @endcomponent
              </div>
              <dl
                class="{{ clsx('col-start-2 -col-end-1 grid gap-2', mx_grid_cols_class(((int) $show_date) + count($taxonomies) + count($meta_fields))) }}">
                @if ($show_date)
                  <div>
                    <dt class="@[43rem]/table:hidden font-medium">
                      Datum:
                    </dt>
                    <dd>{{ $post->archiveDate }}</dd>
                  </div>
                @endif
                @foreach ($meta_fields as $meta_field)
                  <div>
                    <dt class="@[43rem]/table:hidden font-medium">
                      {{ $meta_field['label'] }}:
                    </dt>
                    <dd>
                      {{ implode(
                          ', ',
                          array_map(
                              function ($field) {
                                  /**
                                   * Filters the display value for an archive meta field.
                                   *
                                   * @param mixed  $value Meta field value.
                                   * @param string $field Meta field key.
                                   * @return mixed Filtered meta field display value.
                                   */
                                  return apply_filters('mx/meta_field/display_value', $field['value'], $field['field']);
                              },
                              array_filter($post->metaValues, function ($field) use ($meta_field) {
                                  return $field['field'] === $meta_field['field'];
                              }),
                          ),
                      ) }}
                    </dd>
                  </div>
                @endforeach
                @if ($post->termsUnlinked)
                  @foreach ($taxonomies as $taxonomy)
                    <div>
                      <dt class="@[43rem]/table:hidden font-medium">
                        {{ $taxonomy['label'] }}:
                      </dt>
                      <dd>
                        {{ implode(
                            ', ',
                            array_map(
                                function ($term) use ($taxonomy) {
                                    return $term['label'];
                                },
                                array_filter($post->termsUnlinked, function ($term) use ($taxonomy) {
                                    return $term['taxonomy'] === $taxonomy['taxonomy'];
                                }),
                            ),
                        ) }}
                      </dd>
                    </div>
                  @endforeach
                @endif
              </dl>
            </li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>
@endif
