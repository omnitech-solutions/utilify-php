<?php

namespace Omnitech\Utilify;

use Illuminate\Support\Arr;

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
}
