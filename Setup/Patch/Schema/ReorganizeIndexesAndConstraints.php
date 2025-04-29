<?php
declare(strict_types=1);

namespace Mageplaza\BannerSlider\Setup\Patch\Schema;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Mageplaza\BannerSlider\Model\Config\Source\Template;
use Psr\Log\LoggerInterface;

class ReorganizeIndexesAndConstraints implements SchemaPatchInterface
{
    /**
     * @var SchemaSetupInterface
     */
    private SchemaSetupInterface $schemaSetup;
    /**
     * @var Filesystem
     */
    protected Filesystem $fileSystem;
    /**
     * @var Template
     */
    protected Template $template;
    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param Filesystem $filesystem
     * @param Template $template
     * @param LoggerInterface $logger
     * @param SchemaSetupInterface $schemaSetup
     */
    public function __construct(
        Filesystem $filesystem,
        Template $template,
        LoggerInterface $logger,
        SchemaSetupInterface $schemaSetup
    ) {
        $this->fileSystem = $filesystem;
        $this->template = $template;
        $this->logger = $logger;
        $this->schemaSetup = $schemaSetup;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $setup = $this->schemaSetup;
        $connection = $setup->getConnection();
        $setup->startSetup();

        $tableName = $setup->getTable('mageplaza_bannerslider_banner_slider');

        if ($connection->isTableExists($tableName)) {
            $foreignKeys = $connection->getForeignKeys($tableName);
            foreach ($foreignKeys as $foreignKey) {
                $connection->dropForeignKey($tableName, $foreignKey['FK_NAME']);
            }
            $indexes = $connection->getIndexList($tableName);
            foreach ($indexes as $indexName => $indexData) {
                if ($indexName !== 'PRIMARY') {
                    $connection->dropIndex($tableName, $indexName);
                }
            }

            $connection->addForeignKey(
                $setup->getFkName(
                    'mageplaza_bannerslider_banner_slider',
                    'slider_id',
                    'mageplaza_bannerslider_slider',
                    'slider_id'
                ),
                $tableName,
                'slider_id',
                $setup->getTable('mageplaza_bannerslider_slider'),
                'slider_id',
                Table::ACTION_CASCADE
            );
            $connection->addForeignKey(
                $setup->getFkName(
                    'mageplaza_bannerslider_banner_slider',
                    'banner_id',
                    'mageplaza_bannerslider_banner',
                    'banner_id'
                ),
                $tableName,
                'banner_id',
                $setup->getTable('mageplaza_bannerslider_banner'),
                'banner_id',
                Table::ACTION_CASCADE
            );

            $connection->addIndex(
                $tableName,
                $setup->getIdxName($tableName, ['slider_id']),
                ['slider_id']
            );
            $connection->addIndex(
                $tableName,
                $setup->getIdxName($tableName, ['banner_id']),
                ['banner_id']
            );
            $connection->addIndex(
                $tableName,
                $setup->getIdxName(
                    $tableName,
                    ['slider_id', 'banner_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['slider_id', 'banner_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            );
        }

        $this->copyDemoImage();
        $setup->endSetup();
        return $this;
    }

    /**
     * @return void
     */
    private function copyDemoImage()
    {
        try {
            $mediaDirectory = $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA);
            $url = 'mageplaza/bannerslider/banner/demo/';
            $mediaDirectory->create($url);
            $demos = $this->template->toOptionArray();
            foreach ($demos as $demo) {
                $targetPath = $mediaDirectory->getAbsolutePath($url . $demo['value']);
                $DS = DIRECTORY_SEPARATOR;
                $oriPath = dirname(__DIR__, 3) . $DS . 'view' . $DS . 'adminhtml' . $DS . 'web' . $DS . 'images' . $DS . $demo['value'];
                $mediaDirectory->getDriver()->copy($oriPath, $targetPath);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
