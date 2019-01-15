<?php
declare(strict_types=1);

namespace Yireo\ExampleDeclarativeSchema\Test\Util;

use DOMDocument;
use Magento\Developer\Model\Setup\Declaration\Schema\WhitelistGenerator;
use Magento\Framework\Setup\Declaration\Schema\Config\Converter;
use Magento\Framework\Setup\Declaration\Schema\Declaration\SchemaBuilder as DeclarativeSchemaBuilder;
use Magento\Framework\Setup\Declaration\Schema\Dto\SchemaFactory;
use Magento\Framework\Setup\Declaration\Schema\Diff\SchemaDiff;
use Magento\Framework\Setup\Declaration\Schema\OperationsExecutor;
use Magento\Framework\Setup\Declaration\Schema\SchemaConfigInterface;

/**
 * Class SchemaExecutor
 * @package Yireo\ExampleDeclarativeSchema\Test\Util
 */
class SchemaExecutor
{
    /**
     * @var DeclarativeSchemaBuilder
     */
    private $declarativeSchemaBuilder;

    /**
     * @var SchemaFactory
     */
    private $schemaFactory;

    /**
     * @var SchemaConfigInterface
     */
    private $schemaConfig;

    /**
     * @var SchemaDiff
     */
    private $schemaDiff;

    /**
     * @var OperationsExecutor
     */
    private $operationsExecutor;

    /**
     * @var \Magento\Framework\Config\ConverterInterface
     */
    private $converter;
    /**
     * @var WhitelistGenerator
     */
    private $whitelistGenerator;

    /**
     * SchemaExecutor constructor.
     *
     * @param DeclarativeSchemaBuilder $declarativeSchemaBuilder
     * @param SchemaFactory $schemaFactory
     * @param SchemaConfigInterface $schemaConfig
     * @param SchemaDiff $schemaDiff
     * @param OperationsExecutor $operationsExecutor
     * @param Converter $converter
     * @param WhitelistGenerator $whitelistGenerator
     */
    public function __construct(
        DeclarativeSchemaBuilder $declarativeSchemaBuilder,
        SchemaFactory $schemaFactory,
        SchemaConfigInterface $schemaConfig,
        SchemaDiff $schemaDiff,
        OperationsExecutor $operationsExecutor,
        Converter $converter,
        WhitelistGenerator $whitelistGenerator
    ) {
        $this->declarativeSchemaBuilder = $declarativeSchemaBuilder;
        $this->schemaFactory = $schemaFactory;
        $this->schemaConfig = $schemaConfig;
        $this->schemaDiff = $schemaDiff;
        $this->operationsExecutor = $operationsExecutor;
        $this->converter = $converter;
        $this->whitelistGenerator = $whitelistGenerator;
    }

    /**
     * @param string $schemaPatchFile
     * @throws \Magento\Framework\Setup\Exception
     * @comment Copied from \Magento\Setup\Model\DeclarationInstaller
     */
    public function executePatchFile(string $schemaPatchFile)
    {
        $data = $this->getDataFromConfigFile($schemaPatchFile);
        $schema = $this->schemaFactory->create();
        $this->declarativeSchemaBuilder->addTablesData($data['table']);
        $declarativeSchema = $this->declarativeSchemaBuilder->build($schema);
        $dbSchema = $this->schemaConfig->getDbConfig();
        $diff = $this->schemaDiff->diff($declarativeSchema, $dbSchema);
        $this->operationsExecutor->execute($diff, []);
    }

    /**
     * @param string $moduleName
     * @throws \Magento\Framework\Exception\ConfigurationMismatchException
     */
    public function whitelistModule(string $moduleName)
    {
        $this->whitelistGenerator->generate($moduleName);
    }

    /**
     * @param $file
     * @return DOMDocument
     */
    private function getDomDocument(string $file): DOMDocument
    {
        $domDocument = new DOMDocument();
        $domDocument->loadXML(file_get_contents($file));
        return $domDocument;
    }

    /**
     * @param string $file
     * @return array
     */
    private function getDataFromConfigFile(string $file): array
    {
        $domDocument = $this->getDomDocument($file);
        return $this->converter->convert($domDocument);
    }
}
