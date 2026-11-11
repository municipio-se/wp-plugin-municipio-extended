@segment([
    'layout' => 'card',
    'title' => $input['title'],
    'context' => $context,
    'image' => $input['image']['src'],
    'content' => $input['content'],
    'buttons' => [['text' => $input['linkText'], 'href' => $input['link'], 'target' => $input['linkTarget'] ?: '_self', 'color' => 'primary']],
    'containerAware' => true,
    'reverseColumns' => $imagePosition,
    'classList' => [$input['columnSize']],
    'imageAspectRatio' => $input['imageAspectRatio'] ?? null
])
@endsegment
