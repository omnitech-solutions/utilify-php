<?php

namespace Omnitech\Utilify;

use Illuminate\Support\Arr;
use RuntimeException;
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
}
