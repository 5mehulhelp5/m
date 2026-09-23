<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\Behavior;

use Amasty\ImportCore\Import\Behavior\BehaviorResult;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\Behavior\BehaviorResult
 */
class BehaviorResultTest extends TestCase
{
    /**
     * @var BehaviorResult
     */
    private $behaviorResult;

    public function setUp(): void
    {
        $this->behaviorResult = new BehaviorResult();
    }

    /**
     * @param array $newIds
     * @param array $updatedIds
     * @param array $deletedIds
     * @param array $expectedResult
     * @dataProvider getAffectedIdsDataProvider
     */
    public function testGetAffectedIds(
        array $newIds,
        array $updatedIds,
        array $deletedIds,
        array $expectedResult
    ) {
        $this->setPropValue('newIds', $newIds);
        $this->setPropValue('updatedIds', $updatedIds);
        $this->setPropValue('deletedIds', $deletedIds);

        $this->assertEquals($expectedResult, $this->behaviorResult->getAffectedIds());
    }

    /**
     * @param array $initialData
     * @param BehaviorResult|MockObject $resultMock
     * @param array $expectedData
     * @dataProvider mergeDataProvider
     * @throws \ReflectionException
     */
    public function testMerge(array $initialData, $resultMock, array $expectedData)
    {
        foreach ($initialData as $propName => $value) {
            $this->setPropValue($propName, $value);
        }

        $this->behaviorResult->merge($resultMock);

        foreach ($expectedData as $propName => $value) {
            $this->assertEquals($value, $this->getPropValue($propName));
        }
    }

    /**
     * Sets property value
     *
     * @param string $propName
     * @param mixed $value
     * @return void
     * @throws \ReflectionException
     */
    private function setPropValue(string $propName, $value)
    {
        $reflection = new \ReflectionClass(BehaviorResult::class);

        $property = $reflection->getProperty($propName);
        $property->setAccessible(true);
        $property->setValue($this->behaviorResult, $value);
    }

    /**
     * Returns property value
     *
     * @param string $propName
     * @return mixed
     * @throws \ReflectionException
     */
    private function getPropValue(string $propName)
    {
        $reflection = new \ReflectionClass(BehaviorResult::class);

        $property = $reflection->getProperty($propName);
        $property->setAccessible(true);

        return $property->getValue($this->behaviorResult);
    }

    public function getAffectedIdsDataProvider()
    {
        return [
            [[1], [], [], [1]],
            [[], [1], [], [1]],
            [[], [], [1], [1]],
            [[1], [2], [3], [3, 1, 2]],
        ];
    }

    public function mergeDataProvider()
    {
        return [
            [
                [],
                $this->createConfiguredMock(
                    BehaviorResult::class,
                    [
                        'getNewIds' => [1],
                        'getUpdatedIds' => [2],
                        'getDeletedIds' => [3]
                    ]
                ),
                [
                    'newIds' => [1],
                    'updatedIds' => [2],
                    'deletedIds' => [3]
                ]
            ],
            [
                [
                    'newIds' => [1],
                    'updatedIds' => [2],
                    'deletedIds' => [3]
                ],
                $this->createConfiguredMock(
                    BehaviorResult::class,
                    [
                        'getNewIds' => [4],
                        'getUpdatedIds' => [5],
                        'getDeletedIds' => [6]
                    ]
                ),
                [
                    'newIds' => [1, 4],
                    'updatedIds' => [2, 5],
                    'deletedIds' => [3, 6]
                ]
            ]
        ];
    }
}
