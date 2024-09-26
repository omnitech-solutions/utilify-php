<?php

use Omnitech\Utilify\ArrayUtils;

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

    // Assert that the arrayUndot method correctly converts the dotted array
    expect(ArrayUtils::undot($dottedArray))->toEqual($expectedArray);
});
