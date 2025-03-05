<?php
namespace Mageplaza\BannerSlider\Setup\Patch\Schema;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Mageplaza\BannerSlider\Model\Config\Source\Template;
use Psr\Log\LoggerInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpdateBannerSliderSchema implements SchemaPatchInterface
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
     * @return $this|UpdateBannerSliderSchema
     */
    public function apply()
    {
        $setup = $this->schemaSetup;
        $setup->startSetup();

        $connection = $setup->getConnection();
        $tableName = $setup->getTable('mageplaza_bannerslider_banner_slider');

        $indexList = $connection->getIndexList($tableName);
        $indexesToRemove = [
            'MAGEPLAZA_BANNERSLIDER_BANNER_SLIDER_SLIDER_ID',
            'MAGEPLAZA_BANNERSLIDER_BANNER_SLIDER_BANNER_ID',
            'MAGEPLAZA_BANNERSLIDER_BANNER_SLIDER_UNIQUE'
        ];

        foreach ($indexesToRemove as $indexName) {
            if (isset($indexList[$indexName])) {
                $connection->dropIndex($tableName, $indexName);
            }
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
                $oriPath = dirname(__DIR__, 4) . $DS . 'view' . $DS . 'adminhtml' . $DS . 'web' . $DS . 'images' . $DS . $demo['value'];
                $mediaDirectory->getDriver()->copy($oriPath, $targetPath);
            }
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    /**
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return [];
    }
}
