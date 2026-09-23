<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\Config;

use Amasty\ImportCore\Import\Config\IsModuleDisabled;
use Magento\Framework\Module\Manager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\Config\IsModuleDisabled
 */
class IsModuleDisabledTest extends TestCase
{
    /**
     * @var IsModuleDisabled
     */
    private $isModuleDisabled;

    /**
     * @var Manager|MockObject
     */
    private $moduleManagerMock;

    public function setUp(): void
    {
        $this->moduleManagerMock = $this->createMock(Manager::class);
        $this->isModuleDisabled = new IsModuleDisabled(
            $this->moduleManagerMock,
            []
        );
    }

    /**
     * Data provider for isEnabled
     * @return array
     */
    public function isEnabledDataProvider(): array
    {
        return [
            'disabledModule' => [
                'TestModule',
                false,
                1,
                true
            ],
            'enabledModule' => [
                'TestModule',
                true,
                1,
                false
            ],
            'emptyConfig' => [
                '',
                false,
                0,
                false
            ]
        ];
    }

    /**
     * @param string $moduleName
     * @param bool $moduleState
     * @param int $expectsAmount
     * @param bool $expectedResult
     * @dataProvider isEnabledDataProvider
     */
    public function testIsEnabled(
        string $moduleName,
        bool $moduleState,
        int $expectsAmount,
        bool $expectedResult
    ) {
        $reflection = new \ReflectionClass(IsModuleDisabled::class);

        $moduleNameProp = $reflection->getProperty('moduleName');
        $moduleNameProp->setAccessible(true);
        $moduleNameProp->setValue($this->isModuleDisabled, $moduleName);

        $this->moduleManagerMock->expects($this->exactly($expectsAmount))
            ->method('isEnabled')
            ->willReturnMap([
                [$moduleName, $moduleState]
            ]);
        $this->assertSame($expectedResult, $this->isModuleDisabled->isEnabled());
    }
}
