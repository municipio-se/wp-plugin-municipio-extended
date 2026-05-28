@php
  $url = $url ?? '';
  $height = $height ?? null;
  $title = $title ?? null;
  $iframe = wstg_parse_input($url ?? '');
  if (!($iframe['embedUrl'] ?? null)) {
      throw new Exception(
          json_encode($url, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) .
              ' could not be parsed into a valid iframe URL',
          1,
      );
  }
@endphp
<wstg-iframe
  {{ mx_attrs(
      [
          'src' => $iframe['embedUrl'],
          'service' => $iframe['serviceKey'] ?? null,
          'category' => $iframe['service']['category'] ?? null,
          'class' => [
              $iframe['thumbnailUrl'] ?? null ? 'bg-black' : 'transparent',
              'aspect-[--iframe-aspect-ratio] grid grid-cols-[100%] *:row-start-1 *:col-start-1',
          ],
          'iframe-class' => 'relative z-[1] aspect-[--iframe-aspect-ratio]',
          'iframe-title' => $title,
          'style' => [
              '--iframe-aspect-ratio' => $iframe['aspectRatio'] ?? null,
          ],
          'height' => $height,
      ],
      $iframe['attributes'] ?? [],
  ) }}>
  @if ($iframe['thumbnailUrl'] ?? null)
    <img src="{{ $iframe['thumbnailUrl'] }}" alt="" slot="thumbnail"
      class="object-cover object-center min-h-0 self-stretch justify-self-stretch opacity-50">
  @endif
  <div slot="loader">
    <div class="grid place-items-center h-full">
      <div class="w-8 h-8 border-4 border-white border-t-transparent rounded-full animate-spin"></div>
    </div>
  </div>
  <div slot="dialog"
    class="{{ clsx([
        $iframe['thumbnailUrl'] ?? null
            ? 'bg-white m-3 shadow-[0_0_2rem_rgba(0,0,0,0.1)] w-auto justify-self-center max-w-[40rem] self-center'
            : 'bg-layer',
        'relative p-6 space-y-4 prose rounded-[--radius-lg]',
    ]) }}"
    hidden>
    @if ($iframe['service']['title'] ?? null)
      <p>
        Detta innehåll kan inte visas eftersom du inte samtyckt till kakor och
        delning av uppgifter med {{ $iframe['service']['title'] }}.
      </p>
    @else
      <p>
        Detta innehåll kan inte visas eftersom du inte samtyckt till alla kakor
        och delning av uppgifter med tredje part.
      </p>
    @endif
    <p class="flex gap-2 flex-wrap">
      @component('mxui.button', [
          'type' => 'button',
          'variant' => 'secondary',
          'attributes' => [
              'slot' => 'settingsButton',
          ],
      ])
        Ändra mina inställningar
      @endcomponent
      @if ($iframe['standaloneUrl'] ?? null)
        @component('mxui.button', [
            'variant' => 'secondary',
            'href' => $iframe['standaloneUrl'],
            'attributes' => [
                'target' => '_blank',
                'rel' => 'noreferrer noopener',
            ],
        ])
          Öppna på {{ $iframe['service']['title'] ?? 'extern webbplats' }}
          @component('mxui.icon', [
              'icon' => 'open_in_new',
              'size' => 'sm',
              'classList' => ['translate-x-1'],
          ])
          @endcomponent
        @endcomponent
      @endif
    </p>
  </div>
</wstg-iframe>
