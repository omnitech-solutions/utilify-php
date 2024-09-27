<?php

use Symfony\Component\Yaml\Yaml;

describe('undot', function (): void {
    it('can convert a dotted array to a multi-dimensional array', function (): void {
        $dottedArray = [
            'entity' => 'Entity A',
            'entries.0.values.key_one' => 'value1',
            'entries.0.values.key_two' => 10.5,
            'entries.1.values.key_one' => 'value2',
            'entries.1.values.key_two' => '80',
        ];

        $expectedArray = [
            'entity' => 'Entity A',
            'entries' => [
                [
                    'values' => [
                        'key_one' => 'value1',
                        'key_two' => 10.5,
                    ],
                ],
                [
                    'values' => [
                        'key_one' => 'value2',
                        'key_two' => '80',
                    ],
                ],
            ],
        ];

        // Use the macro to call undot on a collection
        expect(collect($dottedArray)->undot()->all())->toEqual($expectedArray);
    });
});

describe('filterByKeyPattern', function (): void {
    it('returns an empty array when no keys match the regex', function (): void {
        $array = [
            'abc' => 1,
            'def' => 2,
            'ghi' => 3,
        ];

        $regex = '/\d+/'; // Looking for numeric keys, but none exist
        $result = collect($array)->filterByKeyPattern($regex)->all();

        expect($result)->toEqual([]);
    });

    it('returns all entries when all keys match the regex', function (): void {
        $array = [
            '100000' => 123,
            '200000' => 456,
            '300000' => 789,
        ];

        $regex = '/^\d+$/'; // Matches all keys as they are numeric
        $result = collect($array)->filterByKeyPattern($regex)->all();

        expect($result)->toEqual($array); // Expect the original array to be returned
    });

    it('returns only entries with keys that match the regex', function (): void {
        $array = [
            '100000' => 123,
            '200000' => 456,
            'abc000' => 789,
        ];

        $regex = '/^\d+/'; // Matches only numeric keys at the start
        $expectedArray = [
            '100000' => 123,
            '200000' => 456,
        ];

        $result = collect($array)->filterByKeyPattern($regex)->all();

        expect($result)->toEqual($expectedArray); // Expect only matching keys to be returned
    });

    it('returns an empty array when given an empty array', function (): void {
        $array = [];

        $regex = '/^\d+/'; // Any regex
        $result = collect($array)->filterByKeyPattern($regex)->all();

        expect($result)->toEqual([]); // Expect an empty array
    });
});

describe('filterAttributesByConditions', function (): void {
    it('returns all attributes when filter is true', function (): void {
        $attributes = [
            'name' => 'John',
            'age' => '30',
            'email' => 'john@example.com',
        ];

        $filters = [
            'name' => true,
            'age' => true,
            'email' => true,
        ];

        $result = collect($attributes)->filterAttributesByConditions($filters)->all();

        expect($result)->toEqual($attributes); // All attributes should be included
    });

    it('filters attributes using regex', function (): void {
        $attributes = [
            'username' => 'user123',
            'password' => 'pass123',
            'email' => 'john@example.com',
        ];

        $filters = [
            'username' => '/^[a-zA-Z0-9]+$/', // Regex to match alphanumeric username
            'password' => '/^pass\d+$/',  // Regex to match password pattern
        ];

        $result = collect($attributes)->filterAttributesByConditions($filters)->all();

        expect($result)->toEqual([
            'username' => 'user123',
            'password' => 'pass123',
        ]); // Only username and password should match the filters
    });

    it('filters attributes using callable', function (): void {
        $attributes = [
            'role' => 'admin',
            'status' => 'active',
            'age' => 25,
        ];

        $filters = [
            'role' => fn ($value): bool => $value === 'admin', // Include if role is 'admin'
            'status' => fn ($value): bool => $value === 'active', // Include if status is 'active'
            'age' => fn ($value): bool => $value >= 18, // Include if age is 18 or above
        ];

        $result = collect($attributes)->filterAttributesByConditions($filters)->all();

        expect($result)->toEqual([
            'role' => 'admin',
            'status' => 'active',
            'age' => 25,
        ]); // All attributes match their conditions
    });

    it('ignores filters that don’t match existing attributes', function (): void {
        $attributes = [
            'first_name' => 'John',
            'last_name' => 'Doe',
        ];

        $filters = [
            'first_name' => true,
            'age' => fn ($value): bool => $value >= 18, // Attribute 'age' does not exist
        ];

        $result = collect($attributes)->filterAttributesByConditions($filters)->all();

        expect($result)->toEqual([
            'first_name' => 'John',
        ]); // Only 'first_name' should be included, as 'age' does not exist in attributes
    });

    it('handles mixed filters with regex, boolean, and callable', function (): void {
        $attributes = [
            'username' => 'user456',
            'email' => 'user@example.com',
            'age' => 22,
        ];

        $filters = [
            'username' => '/^[a-zA-Z0-9]+$/', // Regex for alphanumeric usernames
            'email' => true, // Always include email
            'age' => fn ($value): bool => $value >= 18, // Include if age is 18 or above
        ];

        $result = collect($attributes)->filterAttributesByConditions($filters)->all();

        expect($result)->toEqual([
            'username' => 'user456',
            'email' => 'user@example.com',
            'age' => 22,
        ]); // All attributes match their respective filters
    });
});

describe('rejectBlanks', function (): void {
    it('removes blank values', function (): void {
        $items = [
            'name' => 'John',
            'email' => null,
            'age' => 0,
            'description' => '',
            'status' => 'active',
            'tags' => [],
        ];

        $result = collect($items)->rejectBlanks()->all();

        expect($result)->toEqual([
            'name' => 'John',
            'age' => 0,
            'status' => 'active',
        ]);
    });

    it('handles all blank values', function (): void {
        $items = [
            'name' => '',
            'email' => null,
            'tags' => [],
        ];

        $result = collect($items)->rejectBlanks()->all();

        expect($result)->toBeEmpty(); // All values are blank, so the result should be an empty array
    });

    it('keeps non-blank values', function (): void {
        $items = [
            'username' => 'user123',
            'age' => 25,
            'is_active' => true,
        ];

        $result = collect($items)->rejectBlanks()->all();

        expect($result)->toEqual($items); // None of the values are blank, so the result should be the same as the input
    });

    it('handles empty array', function (): void {
        $items = [];

        $result = collect($items)->rejectBlanks()->all();

        expect($result)->toEqual([]); // Empty input should return an empty array
    });

    it('removes only blank values and keeps non-blank', function (): void {
        $items = [
            'price' => '0',
            'quantity' => 10,
            'notes' => null,
            'comments' => 'Great product',
            'discount' => '',
        ];

        $result = collect($items)->rejectBlanks()->all();

        expect($result)->toEqual([
            'price' => '0',
            'quantity' => 10,
            'comments' => 'Great product',
        ]);
    });
});

describe('toYamlStr', function (): void {
    it('returns YAML content', function (): void {
        $items = ['name' => 'John', 'email' => 'john@example.com'];

        $yamlContent = collect($items)->toYamlStr();

        expect($yamlContent)->toEqual(Yaml::dump($items, 10));
    });

    it('returns YAML content for an empty array', function (): void {
        $items = [];

        $yamlContent = collect($items)->toYamlStr();

        expect($yamlContent)->toEqual(Yaml::dump($items, 10));
    });
});

describe('toJsonStr', function (): void {
    it('returns JSON content for a valid array', function (): void {
        $items = ['name' => 'John Doe', 'age' => 30, 'email' => 'john@example.com'];

        $expectedJson = json_encode($items, JSON_PRETTY_PRINT);

        $result = collect($items)->toJsonStr();

        expect($result)->toEqual($expectedJson);
    });

    it('returns an empty string if JSON encoding fails', function (): void {
        // Invalid UTF-8 sequence for JSON encoding
        $items = ['invalid' => "\xB1\x31"];

        $result = collect($items)->toJsonStr();

        expect($result)->toEqual(''); // Expect an empty string if json_encode fails
    });

    it('returns JSON content for an empty array', function (): void {
        $items = [];

        $expectedJson = json_encode($items, JSON_PRETTY_PRINT);

        $result = collect($items)->toJsonStr();

        expect($result)->toEqual($expectedJson); // Expect empty JSON array format "[]"
    });

    it('returns JSON content with mixed data types', function (): void {
        $items = [
            'name' => 'Jane',
            'age' => 27,
            'is_verified' => true,
            'preferences' => ['newsletters' => false, 'notifications' => true],
        ];

        $expectedJson = json_encode($items, JSON_PRETTY_PRINT);

        $result = collect($items)->toJsonStr();

        expect($result)->toEqual($expectedJson); // Expect correct JSON format for mixed types
    });
});
