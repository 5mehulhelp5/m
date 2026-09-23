<?php
declare(strict_types=1);

namespace Crimson\Newsletter\Setup\Patch\Schema;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;

class IncreaseSmtpLogSize implements SchemaPatchInterface
{
    /** @var ModuleDataSetupInterface  */
    private $moduleDataSetup;

    public function __construct(ModuleDataSetupInterface $moduleDataSetup)
    {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->modifyColumn(
            $this->moduleDataSetup->getTable('mageplaza_smtp_log'),
            'email_content', [
                'type' => Table::TYPE_TEXT,
                'length' => 16777216,
                'nullable' => true,
                'comment' => 'Email Content',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }
}
