<?php

namespace Omnitech\Utilify;

use Illuminate\Support\Collection;

/**
 * @codeCoverageIgnore
 */
class CollectionMacros
{
    public static function register(): void
    {
        /**
         * Converts a dot notation array into a multi-dimensional array.
         *
         * @return Collection<string, mixed> A multi-dimensional array.
         */
        Collection::macro('undot', fn (): Collection =>
            /** @var Collection<string, mixed> $this */
            new Collection(ArrayUtils::undot($this->toArray())));

        /**
         * Return array entries with keys that match the search pattern
         *
         * @return Collection<string, mixed>
         */
        Collection::macro('filterByKeyPattern', fn (string $pattern): Collection =>
            /** @var Collection<int|string, mixed> $this */
            new Collection(ArrayUtils::filterByKeyPattern($this->toArray(), $pattern)));

        /**
         * Filters the attributes based on the given filters.
         *
         * @param  array<string, bool|string|callable>  $filters  An array of filters to apply. Filters can be a boolean, string, regex pattern, or a callable.
         * @return Collection<string, mixed> The filtered attributes that match the provided filters.
         */
        Collection::macro('filterAttributesByConditions', fn (array $filters): Collection =>
            /** @var Collection<int|string, mixed> $this */
            new Collection(ArrayUtils::filterAttributesByConditions($this->toArray(), $filters)));

        /**
         * Rejects items that are blank.
         *
         * @return Collection<string, mixed> The filtered attributes that match the provided filters.
         */
        Collection::macro('rejectBlanks', fn (): Collection =>
            /** @var Collection<int|string, mixed> $this */
            new Collection(ArrayUtils::rejectBlanks($this->toArray())));

        /**
         * Converts the given collection to a YAML-formatted string.
         *
         * @return string The formatted YAML or JSON string (if unable to convert to YAML format), or an empty string if encoding fails.
         */
        Collection::macro('toYamlStr', fn (): string =>
            /** @var Collection<int|string, mixed> $this */
            ArrayUtils::toYamlStr($this->toArray()));

        /**
         * Converts the given array to a JSON-formatted string.
         *
         * @return string The JSON string or an empty string if encoding fails.
         */
        Collection::macro('toJsonStr', fn (): string =>
            /** @var Collection<int|string, mixed> $this */
            ArrayUtils::toJsonStr($this->toArray()));
    }
}
