<?php
declare(strict_types=1);

namespace Yireo\ExampleDeclarativeSchema\Test\Integration;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Component\ComponentRegistrar;
use PHPUnit\Framework\TestCase;
use Magento\Framework\App\ResourceConnection;
use Yireo\ExampleDeclarativeSchema\Test\Util\SchemaExecutor;

/**
 * Class SetupDeclarativeSchemaTest
 * @package Yireo\ExampleDeclarativeSchema\Test\Integration
 */
class SetupDeclarativeSchemaTest extends TestCase
{
    const MODULE = 'Yireo_ExampleDeclarativeSchema';

    /**
     * Preliminary test to see if the table exists at all
     */
    public function testIfTableExists()
    {
        $connection = $this->getResourceConnection()->getConnection();
        $this->assertTrue($connection->isTableExists('yireo_example'));
    }

    /**
     * @throws \Magento\Framework\Setup\Exception
     */
    public function testDropColumnEnabled()
    {
        $connection = $this->getResourceConnection()->getConnection();
        $table = 'yireo_example';
        $column = 'enabled';

        $msg = sprintf('Table "%s" should have a column "%s"', $table, $column);
        $this->assertTrue($connection->tableColumnExists($table, $column), $msg);

        $schemaPatch = $this->getCustomSchemaPatch('db_schema_test_drop_column_enabled.xml');
        $this->getSchemaExecutor()->executePatchFile($schemaPatch);

        $msg = sprintf('Table "%s" should still have a column "%s"', $table, $column);
        $this->assertTrue($connection->tableColumnExists($table, $column), $msg);

        $this->getSchemaExecutor()->whitelistModule(self::MODULE);

        $msg = sprintf('Table "%s" should not have a column "%s"', $table, $column);
        $this->assertFalse($connection->tableColumnExists($table, $column), $msg);
    }

    /**
     * @return SchemaExecutor
     */
    private function getSchemaExecutor(): SchemaExecutor
    {
        return $this->getObjectManager()->get(SchemaExecutor::class);
    }

    /**
     * @param string $fileName
     * @return string
     */
    private function getCustomSchemaPatch(string $fileName): string
    {
        $moduleFolder = $this->getComponentRegistrar()->getPath('module', self::MODULE);
        return $moduleFolder . '/etc/' . $fileName;
    }

    /**
     * @return ComponentRegistrar
     */
    private function getComponentRegistrar(): ComponentRegistrar
    {
        return $this->getObjectManager()->get(ComponentRegistrar::class);
    }

    /**
     * @return ResourceConnection
     */
    private function getResourceConnection(): ResourceConnection
    {
        return $this->getObjectManager()->get(ResourceConnection::class);
    }

    /**
     * @return ObjectManager
     */
    private function getObjectManager(): ObjectManager
    {
        return ObjectManager::getInstance();
    }
}
