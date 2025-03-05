<?php
namespace Mageplaza\BannerSlider\Setup\Patch\Schema;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Mageplaza\BannerSlider\Model\Config\Source\Template;
use Psr\Log\LoggerInterface;

class CopyDemoImages implements SchemaPatchInterface
{
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
     * CopyDemoImages constructor.
     *
     * @param Filesystem $filesystem
     * @param Template $template
     * @param LoggerInterface $logger
     */
    public function __construct(
        Filesystem $filesystem,
        Template $template,
        LoggerInterface $logger
    ) {
        $this->fileSystem = $filesystem;
        $this->template = $template;
        $this->logger = $logger;
    }

    /**
     * @return $this
     */
    public function apply()
    {
        $this->copyDemoImage();
        return $this;
    }

    /**
     * Copy demo images
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
