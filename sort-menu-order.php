<?php
declare(strict_types=1);

/**
 * Plugin Name:     Sort Menu Order
 * Plugin URI:      https://github.com/ItinerisLtd/sort-menu-order
 * Description:     Menu Order
 * Version:         0.1.0
 * Author:          Itineris Limited
 * Author URI:      https://www.itineris.co.uk/
 * Text Domain:     sort-menu-order
 */

namespace Itineris\MenuOrder;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

add_filter('custom_menu_order', '__return_true');

add_filter('menu_order', function ($items) {
    $edits = array_sort(array_filter($items, function (string $item): bool {
        return Str::startsWith($item, 'edit.php');
    }));

    $nonedits = array_filter($items, function (string $item): bool {
        return ! Str::startsWith($item, 'edit.php') && 'upload.php' !== $item && 'gf_edit_forms' !== $item;
    });

    $noneditsBeforeSperator = [];
    $noneditsAfterSperator = [];

    $pointer = false;
    foreach ($nonedits as $item) {
        if ('separator1' === $item) {
            $noneditsBeforeSperator[] = $item;
            $pointer = true;
            continue;
        }

        if ($pointer) {
            $noneditsAfterSperator[] = $item;
        } else {
            $noneditsBeforeSperator[] = $item;
        }
    }

    return Arr::collapse([$noneditsBeforeSperator, ['gf_edit_forms', 'upload.php'], $edits, $noneditsAfterSperator]);
}, 799999);

add_filter('menu_order', function ($items) {
    $items = array_filter($items, function (string $item): bool {
        return 'kinsta-tools' !== $item;
    });

    $items[] = 'kinsta-tools';

    return $items;
}, 999999);

add_filter('menu_order', function ($items) {
    return array_filter($items, function (string $item): bool {
        return 'edit-comments.php' !== $item;
    });
}, 1099999);
