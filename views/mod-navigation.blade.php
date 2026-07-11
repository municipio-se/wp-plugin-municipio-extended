@if (!empty($items) || !$hideIfEmpty)
  @if (!$hideTitle && !empty($postTitle))
    @typography([
        'id' => 'mod-navigation-' . $ID . '-label',
        'element' => 'h2',
        // 'variant' => 'h2',
        'autopromote' => true,
        'classList' => ['module-title']
    ])
      {!! $postTitle !!}
    @endtypography
  @endif

  @switch($format)
    @case ('list')
      @include('mxui.navigation.list')
    @break

    @case ('bar')
      @include('mxui.navigation.bar')
    @break

    @case ('tree')
      @include('mxui.navigation.tree')
    @break

    @case ('cards')
      @include('mxui.navigation.cards')
    @break

    @case ('buttons')
      @include('mxui.navigation.buttons')
    @break

    @case ('inline')
      @include('mxui.navigation.inline')
    @break
  @endswitch
@endif
