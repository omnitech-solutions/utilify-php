<?php

use Omnitech\Utilify\ArrayUtils;

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

        expect(ArrayUtils::undot($dottedArray))->toEqual($expectedArray);
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
        $result = ArrayUtils::filterByKeyPattern($array, $regex);

        expect($result)->toEqual([]);
    });

    it('returns all entries when all keys match the regex', function (): void {
        $array = [
            '100000' => 123,
            '200000' => 456,
            '300000' => 789,
        ];

        $regex = '/^\d+$/'; // Matches all keys as they are numeric
        $result = ArrayUtils::filterByKeyPattern($array, $regex);

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

        $result = ArrayUtils::filterByKeyPattern($array, $regex);

        expect($result)->toEqual($expectedArray); // Expect only matching keys to be returned
    });

    it('returns an empty array when given an empty array', function (): void {
        $array = [];

        $regex = '/^\d+/'; // Any regex
        $result = ArrayUtils::filterByKeyPattern($array, $regex);

        expect($result)->toEqual([]); // Expect an empty array
    });

    it('throws a TypeError with an invalid regex', function (): void {
        $array = [
            '100000' => 123,
            '200000' => 456,
        ];

        $regex = '/[unclosed_bracket'; // Invalid regex

        expect(fn (): array => ArrayUtils::filterByKeyPattern($array, $regex))
            ->toThrow(TypeError::class); // Expect a TypeError due to the invalid regex
    });
});
