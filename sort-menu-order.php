<?php
declare(strict_types=1);

/**
 * Plugin Name:     Sort Menu Order
 * Plugin URI:      https://github.com/ItinerisLtd/sort-menu-order
 * Description:     Menu Order
 * Version:         0.4.0
 * Author:          Itineris Limited
 * Author URI:      https://www.itineris.co.uk/
 * Text Domain:     sort-menu-order
 */

namespace Itineris\MenuOrder;

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

class Helper {
    /**
     * Determine if a given string starts with a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    public static function stringStartsWith(string $haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && substr($haystack, 0, strlen($needle)) === (string) $needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * Collapse an array of arrays into a single array.
     *
     * @param  array  $array
     * @return array
     */
    public static function arrayCollapse(array $array): array
    {
        $results = [];

        foreach ($array as $values) {
            if (! is_array($values)) {
                continue;
            }

            $results[] = $values;
        }

        return array_merge([], ...$results);
    }
}

add_action( 'admin_menu', function (): void {
    remove_menu_page( 'edit-comments.php' );
});

add_filter('custom_menu_order', '__return_true');

add_filter('menu_order', function (array $items): array {
    $edits = array_filter($items, function (string $item): bool {
        return Helper::stringStartsWith($item, 'edit.php');
    });
    sort($edits);

    $nonEdits = array_diff($items, $edits, ['upload.php', 'gf_edit_forms']);

    $nonEditsBeforeSeparator = $nonEdits;
    $nonEditsAfterSeparator = [];

    $separatorKey = array_search('separator1', $items);
    if ($separatorKey !== false) {
        $nonEditsBeforeSeparator = array_slice($nonEdits, 0, $separatorKey);
        $nonEditsAfterSeparator = array_slice($nonEdits, $separatorKey, -1);
    }

    return Helper::arrayCollapse([$nonEditsBeforeSeparator, ['gf_edit_forms', 'upload.php'], $edits, $nonEditsAfterSeparator]);
}, -10);

add_filter('menu_order', function (array $items): array {
    $key = array_search('kinsta-tools', $items);
    if ($key !== false) {
        unset($items[$key]);
        $items[] = 'kinsta-tools';
    }

    return $items;
}, 20);
