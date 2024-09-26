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

describe('isFilled', function (): void {
    it('checks if a value is filled or not equal to zero', function (): void {
        expect(ArrayUtils::isFilled(' abc  '))->toBeTrue();  // Non-empty string
        expect(ArrayUtils::isFilled(null))->toBeFalse();     // Null value
        expect(ArrayUtils::isFilled(0))->toBeFalse();        // Numeric zero
        expect(ArrayUtils::isFilled('0'))->toBeFalse();      // String zero
        expect(ArrayUtils::isFilled(1))->toBeTrue();         // Positive number
        expect(ArrayUtils::isFilled(-1))->toBeTrue();        // Negative number
    });

    it('returns true for non-empty strings', function (): void {
        expect(ArrayUtils::isFilled(' abc  '))->toBeTrue(); // Non-empty string with spaces
        expect(ArrayUtils::isFilled('Hello'))->toBeTrue();   // Regular non-empty string
    });

    it('returns false for empty strings', function (): void {
        expect(ArrayUtils::isFilled(''))->toBeFalse();       // Empty string
        expect(ArrayUtils::isFilled('    '))->toBeFalse();   // String with spaces only
    });

    it('returns true for positive and negative integers', function (): void {
        expect(ArrayUtils::isFilled(1))->toBeTrue();         // Positive integer
        expect(ArrayUtils::isFilled(-1))->toBeTrue();        // Negative integer
    });

    it('returns false for zero or string zero', function (): void {
        expect(ArrayUtils::isFilled(0))->toBeFalse();        // Numeric zero
        expect(ArrayUtils::isFilled('0'))->toBeFalse();      // String zero
    });

    it('returns true for non-zero floats', function (): void {
        expect(ArrayUtils::isFilled(0.1))->toBeTrue();       // Positive float
        expect(ArrayUtils::isFilled(-0.1))->toBeTrue();      // Negative float
    });

    it('returns false for boolean false and true for boolean true', function (): void {
        expect(ArrayUtils::isFilled(false))->toBeFalse();    // Boolean false
        expect(ArrayUtils::isFilled(true))->toBeTrue();      // Boolean true
    });

    it('returns false for null', function (): void {
        expect(ArrayUtils::isFilled(null))->toBeFalse();     // Null value
    });

    it('returns true for non-empty arrays', function (): void {
        expect(ArrayUtils::isFilled([1, 2, 3]))->toBeTrue(); // Non-empty array
    });

    it('returns false for empty arrays', function (): void {
        expect(ArrayUtils::isFilled([]))->toBeFalse();       // Empty array
    });
});

describe('isBlank', function (): void {
    it('returns true for blank or zero values', function (): void {
        expect(ArrayUtils::isBlank('   '))->toBeTrue();   // Blank string with spaces
        expect(ArrayUtils::isBlank(null))->toBeTrue();    // Null value
        expect(ArrayUtils::isBlank(0))->toBeTrue();       // Numeric zero
        expect(ArrayUtils::isBlank('0'))->toBeTrue();     // String zero
        expect(ArrayUtils::isBlank('0.000000'))->toBeTrue(); // String representing zero with decimals
    });

    it('returns false for non-blank, non-zero values', function (): void {
        expect(ArrayUtils::isBlank(1))->toBeFalse();      // Positive number
        expect(ArrayUtils::isBlank(-1))->toBeFalse();     // Negative number
    });
});
