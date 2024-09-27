<?php

namespace Omnitech\Utilify;

use Illuminate\Support\Arr;
use RuntimeException;
use Symfony\Component\Yaml\Yaml;
use TypeError;

class ArrayUtils
{
    /**
     * Converts a dot notation array into a multi-dimensional array.
     *
     * @param  array<string, mixed>  $dotNotationArray  An associative array where keys are in dot notation and values are of any type.
     * @return array<string, mixed> A multi-dimensional array.
     */
    public static function undot(array $dotNotationArray): array
    {
        $array = [];
        foreach ($dotNotationArray as $key => $value) {
            Arr::set($array, $key, $value);
        }

        return $array;
    }

    /**
     * Filters an array to include only entries where the keys match a given regular expression.
     *
     * This method searches the keys of the provided array and retains only those
     * that match the specified regular expression. The values associated with those keys
     * are included in the returned array.
     *
     * @param  array<string, mixed>  $array  The associative array to filter, with string keys and mixed values.
     * @param  string  $regex  The regular expression to use for matching the array keys.
     * @return array<string, mixed> An array containing only the key-value pairs where the key matches the regex.
     *
     * @throws TypeError If the provided regular expression is invalid.
     * @throws RuntimeException If an error occurs while processing the regular expression.
     */
    public static function filterByKeyPattern(array $array, string $regex): array
    {
        // Validate the regex before using it
        if (@preg_match($regex, '') === false) {
            throw new TypeError('Invalid regular expression: '.$regex);
        }

        $matches = preg_grep($regex, array_keys($array));

        // Handle the case where preg_grep() returns false
        if ($matches === false) {
            throw new RuntimeException('An error occurred while processing the regular expression.');
        }

        return array_intersect_key($array, array_flip($matches));
    }

    /**
     * Checks if the given value is filled (non-empty) or not equal to `0`.
     *
     * This function determines whether the provided value is either a non-numeric
     * filled value (not empty or null) or a numeric value that is not equal to `0`.
     *
     * - If the value is non-numeric, the function checks if it is filled (not empty).
     * - If the value is numeric, it returns `true` if the value is not exactly zero.
     *
     * @param  mixed  $value  The value to check, which can be of any type (string, number, etc.).
     * @return bool Returns `true` if the value is filled or not equal to `0`, otherwise `false`.
     */
    public static function isFilled(mixed $value): bool
    {
        // Handle booleans directly
        if (is_bool($value)) {
            return $value;
        }

        $isFilledNonNumeric = ! is_numeric($value) && filled($value);
        $isZeroNumeric = is_numeric($value) && (float) $value !== 0.0;

        return $isFilledNonNumeric || $isZeroNumeric;
    }

    /**
     * Checks if the given value is blank (non-empty) or not equal to `0`.
     *
     * This function determines whether the provided value is either a non-numeric
     * blank value (empty or null) or a numeric value that is equal to `0`.
     *
     * - If the value is non-numeric, the function checks if it is blank (empty).
     * - If the value is numeric, it returns `true` if the value is exactly zero.
     *
     * @param  mixed  $value  The value to check, which can be of any type (string, number, etc.).
     * @return bool Returns `true` if the value is filled or not equal to `0`, otherwise `false`.
     */
    public static function isBlank(mixed $value): bool
    {
        return ! self::isFilled($value);
    }

    /**
     * Filters the attributes based on the given filters.
     *
     * Each filter must have a key and a value of either a string, a regular expression, or a callable that returns a boolean.
     *
     * @param  array<string, mixed>  $attributes  The array of attributes to be filtered (string keys, mixed values).
     * @param  array<string, bool|string|callable>  $filters  An array of filters to apply. Filters can be a boolean, string, regex pattern, or a callable.
     * @return array<string, mixed> The filtered attributes that match the provided filters.
     *
     * @example
     *
     * ['field_1' => true] // Always includes 'field_1'
     * ['field_2' => /^[0-9]*$/] // Includes 'field_2' if it contains only numbers
     * ['field_3' => function ($value) { // Includes 'field_3' if the callback evaluates to true
     *     return in_array($value, ['allowed_value1', 'allowed_value2'], true);
     * }]
     */
    public static function filterAttributesByConditions(array $attributes, array $filters): array
    {
        // Keep only the filters that match existing attributes
        $filters = Arr::only($filters, array_keys($attributes));

        $filteredAttributes = [];
        foreach ($filters as $key => $filter) {
            $value = $attributes[$key];

            // If the filter is a callable function
            if (is_callable($filter)) {
                if ($filter($value)) {
                    $filteredAttributes[$key] = $value;
                }

                continue;
            }

            // If the filter is `true` or a regex pattern, check that value is a string before using preg_match
            if ($filter === true || (is_string($value) && preg_match($filter, $value))) {
                $filteredAttributes[$key] = $value;
            }
        }

        return $filteredAttributes;
    }

    /**
     * Rejects items that are blank.
     *
     * This method removes blank items from the provided array. An item is considered blank
     * if it is null, an empty string, an empty array, or any value that the Laravel `filled` helper
     * considers as "blank."
     *
     * @param  array<string, mixed>  $items  The array of items to filter. The keys are strings, and the values can be of mixed types.
     * @return array<string, mixed> The filtered array with blank items removed.
     */
    public static function rejectBlanks(array $items): array
    {
        return array_filter($items, 'filled');
    }

    /**
     * Converts the given array to a YAML-formatted string.
     *
     * If YAML conversion fails, it falls back to returning a JSON-formatted string.
     * If JSON encoding fails, it returns an empty string.
     *
     * @param  array<string, mixed>  $items  The array of items to format. The keys are strings, and the values can be of mixed types.
     * @return string The formatted YAML or JSON string, or an empty string if encoding fails.
     */
    public static function toYamlStr(array $items): string
    {
        try {
            // Attempt to convert the array to YAML format
            return Yaml::dump($items, 10);
        } catch (\Exception) {
            // If YAML conversion fails, fall back to JSON
            return self::toJsonStr($items);
        }
    }

    /**
     * Converts the given array to a JSON-formatted string.
     *
     * If JSON encoding fails, it returns an empty string.
     *
     * @param  array<string, mixed>  $items  The array of items to format. The keys are strings, and the values can be of mixed types.
     * @return string The JSON string or an empty string if encoding fails.
     */
    public static function toJsonStr(array $items): string
    {
        $jsonContent = json_encode($items, JSON_PRETTY_PRINT);

        // Ensure that a string is always returned, even if json_encode fails
        return $jsonContent !== false ? $jsonContent : '';
    }
}
