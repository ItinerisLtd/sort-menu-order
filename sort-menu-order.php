<?php
declare(strict_types=1);

/**
 * Plugin Name:     Sort Menu Order
 * Plugin URI:      https://github.com/ItinerisLtd/sort-menu-order
 * Description:     Menu Order
 * Version:         0.1.1
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

add_action( 'admin_menu', function (): void {
    remove_menu_page( 'edit-comments.php' );
});

add_filter('custom_menu_order', '__return_true');

add_filter('menu_order', function (array $items): array {
    $edits = array_sort(array_filter($items, function (string $item): bool {
        return Str::startsWith($item, 'edit.php');
    }));

    $nonedits = array_diff($items, $edits, ['upload.php', 'gf_edit_forms']);

    $noneditsBeforeSperator = $nonedits;
    $noneditsAfterSperator = [];

    $speratorKey = array_search('separator1', $items);
    if ($speratorKey !== false) {
        $noneditsBeforeSperator = array_slice($nonedits, 0, $speratorKey);
        $noneditsAfterSperator = array_slice($nonedits, $speratorKey, -1);
    }

    return Arr::collapse([$noneditsBeforeSperator, ['gf_edit_forms', 'upload.php'], $edits, $noneditsAfterSperator]);
}, -10);

add_filter('menu_order', function (array $items): array {
    $key = array_search('kinsta-tools', $items);
    if ($key !== false) {
        unset($items[$key]);
        $items[] = 'kinsta-tools';
    }

    return $items;
}, 20);
