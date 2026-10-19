@php
  $headingLevel ??= 3;
  $workTitle = trim($contact['work_title'] ?? '');
  $department = trim($contact['administration_unit'] ?? '');
  $email = trim($contact['email'] ?? '');
  $phones = array_filter(
      ($contact['phone'] ?? []) ?: [],
      fn($phone) => trim($phone['number'] ?? '') !== '',
  );
  $openingHours = array_filter(
      array_map('trim', explode("\n", $contact['opening_hours'] ?? '')),
      fn($hours) => $hours !== '',
  );
@endphp

<div {{ mx_attrs([
    'class' => ['bg-[--color-background-card]'],
]) }} itemscope itemtype="https://schema.org/Person">
  @if ($workTitle !== '' || $department !== '' || $phones || $email !== '')
    <ul class="text-lg mb-4">
      @if ($workTitle !== '')
        {{-- Work title --}}
        <li
          {{ mx_attrs([
              'class' => [
                  'mb-4' => $department === '',
              ],
          ]) }}>
          {{ $workTitle }}
        </li>
      @endif

      @if ($department !== '')
        {{-- Department --}}
        <li class="mb-4" itemprop="department">
          {{ $department }}
        </li>
      @endif

      {{-- Phone number --}}
      @foreach ($phones as $phone)
        <li class="text-primary">
          <a itemprop="telephone" href="tel:{{ $phone['number'] }}">
            <span aria-hidden="true">{{ mx_get_icon('call') }}</span>
            <span class="underline">{{ $phone['number'] }}</span>
          </a>
        </li>
      @endforeach

      {{-- Email --}}
      @if ($email !== '')
        <li class="text-primary">
          <a itemprop="email" translate="no" class="hover:bg-white flex  break-all gap-1"
            href="mailto:{{ $contact['email'] }}">
            <span aria-hidden="true">{{ mx_get_icon('mail') }}</span>
            <span class="underline">{{ $contact['email'] }}</span>
          </a>
        </li>
      @endif
    </ul>
  @endif

  {{-- Social Media --}}
  @if (!empty($contact['social_media']))
    <h{!! $headingLevel !!} class="font-semibold"><?php _e(
      "Social media",
      "modularity",
    ); ?>
      </h{!! $headingLevel !!}>
      <ul class="mb-4">
        @foreach ($contact['social_media'] as $media)
          @php
            $icon = '';
            switch ($media['media']) {
                case 'facebook':
                    $icon =
                        '<svg class="inline-block w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="currentColor" d="M279.1 288l14.2-92.7h-88.9v-60.1c0-25.4 12.4-50.1 52.2-50.1h40.4V6.3S260.4 0 225.4 0c-73.2 0-121.1 44.4-121.1 124.7v70.6H22.9V288h81.4v224h100.2V288z"/></svg>';
                    break;
                case 'instagram':
                    $icon =
                        '<svg class="inline-block w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="currentColor" d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg>';
                    break;
                case 'linkedin':
                    $icon =
                        '<svg class="inline-block w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="currentColor" d="M100.3 448H7.4V148.9h92.9zM53.8 108.1C24.1 108.1 0 83.5 0 53.8S24.1 0 53.8 0s53.8 24.1 53.8 53.8-24.1 54.3-53.8 54.3zM447.9 448h-92.7V302.4c0-34.7-.7-79.2-48.3-79.2-48.3 0-55.7 37.7-55.7 76.7V448h-92.8V148.9h89.1v40.8h1.3c12.4-23.5 42.7-48.3 87.9-48.3 94 0 111.3 61.9 111.3 142.3V448z"/></svg>';
                    break;
                case 'x':
                    $icon =
                        '<svg class="inline-block w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="currentColor" d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/></svg>';
                    break;
            }
          @endphp
          <li class="text-primary">
            <a href="{{ $media['url'] }}" aria-label="{{ $media['media'] }}" target="_blank"
              rel="noopener noreferrer">
              <span>{!! $icon !!}</span>
              <span class="underline">{{ ucfirst($media['media']) }}</span>
            </a>
          </li>
        @endforeach
      </ul>
  @endif

  {{-- Address --}}
  @if (!empty($contact['address']))
    <h{!! $headingLevel !!} class="font-semibold mt-2"><?php _e(
      "Postal address",
      "modularity",
    ); ?>
      </h{!! $headingLevel !!}>
      <p class="mb-4">{!! $contact['address'] !!}</p>
  @endif


  {{-- Visiting Address --}}
  @if (!empty($contact['visiting_address']))
    <h{!! $headingLevel !!} class="font-semibold mt-2"><?php _e(
      "Visiting address",
      "modularity",
    ); ?>
      </h{!! $headingLevel !!}>
      <p class="mb-4">{!! $contact['visiting_address'] !!}</p>
  @endif

  {{-- Opening Hours --}}
  @if ($openingHours)
    <h{!! $headingLevel !!} class="font-semibold mt-2"><?php _e(
      "Opening hours",
      "modularity",
    ); ?>
      </h{!! $headingLevel !!}>
      <ul class="mb-4">
        @foreach ($openingHours as $hours)
          <li>{!! $hours !!}</li>
        @endforeach
      </ul>
  @endif

  {{-- Other content data --}}
  @if (!empty($contact['other']))
    @if ($contact['other'])
      <div class="mt-4">
        {!! $contact['other'] !!}
      </div>
    @endif
  @endif
</div>
