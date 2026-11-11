@php
$tab_menu_button_size = get_theme_mod("tab_menu_button_size");
@endphp

@button([
    'id' => $id . " - " . $item['id'] ."-" . $loop->index . "__label",
    'icon' => isset($item['icon']['icon']) ? $item['icon']['icon'] : false,
    'reversePositions' => true,
    'text' => $item['label'],
    'style' => $item['buttonStyle'] ?? $buttonStyle,
    'color' => $item['buttonColor'] ?? $buttonColor,
    'href' => $item['href'],
    'target' => $item['target'] ?? '_top',
    'classList' => [
        $baseClass . '__button',
    ],
    'attributeList' => [
        'aria-label' => $item['label']
    ],
    'context' => [
        'component.nav.button'
    ],
    'size' => $tab_menu_button_size
])
@endbutton
