<?php

namespace Omnitech\Utilify;

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
            self::arraySet($array, $key, $value);
        }

        return $array;
    }

    /**
     * Set an array value using "dot" notation for the key.
     *
     * @param  array<string, mixed>  $array  The reference array to set values in.
     * @param  string  $key  The dot notation key.
     * @param  mixed  $value  The value to set in the array.
     */
    protected static function arraySet(array &$array, string $key, mixed $value): void
    {
        $keys = explode('.', $key);
        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;
    }
}
