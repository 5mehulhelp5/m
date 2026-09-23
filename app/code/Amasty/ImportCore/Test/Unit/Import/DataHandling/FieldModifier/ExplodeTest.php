<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\DataHandling\FieldModifier;

use Amasty\ImportCore\Import\DataHandling\FieldModifier\Explode;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\DataHandling\FieldModifier\Explode
 */
class ExplodeTest extends TestCase
{
    /**
     * @var Explode
     */
    private $modifier;

    protected function setUp(): void
    {
        $this->modifier = new Explode([]);
    }

    /**
     * @param $value
     * @param string|null $separator
     * @param $expectedResult
     * @dataProvider transformDataProvider
     */
    public function testTransform($value, ?string $separator, $expectedResult)
    {
        if ($separator !== null) {
            $reflection = new \ReflectionClass(Explode::class);

            $separatorProperty = $reflection->getProperty('separator');
            $separatorProperty->setAccessible(true);
            $separatorProperty->setValue($this->modifier, $separator);
        }

        $this->assertSame($expectedResult, $this->modifier->transform($value));
    }

    /**
     * Data provider for transform
     * @return array
     */
    public function transformDataProvider(): array
    {
        return [
            'basic' => [
                'test,test2,test3,test4',
                ',',
                [
                    'test',
                    'test2',
                    'test3',
                    'test4'
                ]
            ],
            'array_as_value' => [
                [
                    'test',
                    'test2',
                    'test3',
                    'test4'
                ],
                ',',
                [
                    'test',
                    'test2',
                    'test3',
                    'test4'
                ]
            ],
            'separator_test' => [
                'test|test2|test3|test4',
                '|',
                [
                    'test',
                    'test2',
                    'test3',
                    'test4'
                ]
            ],
            'string_without_separator_test' => [
                'test|test2',
                ',',
                [
                    'test|test2'
                ]
            ],
            'empty_config' => [
                'test,test2',
                null,
                [
                    'test',
                    'test2'
                ]
            ],
            'first_last_separator' => [
                ',test,test2,',
                null,
                [
                    'test',
                    'test2'
                ]
            ],
            'empty_separator_value' => [
                'test,,test2',
                null,
                [
                    'test',
                    '',
                    'test2'
                ]
            ]
        ];
    }
}
