@php
  try {
      $image = mx_get_image($src ?? null, $size ?? null);
      if (!$image) {
          throw new \Exception('A valid WpImage object must be passed to mxui.image component');
      }
  } catch (\Exception $e) {
      $image = null;
  }
@endphp

@if ($image)
  @modal([
      'heading' => $heading,
      'isPanel' => $isPanel,
      'id' => $modalId,
      'overlay' => 'dark',
      'animation' => 'scale-up',
      'transparent' => $isTransparent
  ])
    @image([
        'src' => $image['guid'],
        'imgAttributeList' => [
            'srcset' => $image['guid']
        ]
    ])
    @endimage
  @endmodal
@endif
