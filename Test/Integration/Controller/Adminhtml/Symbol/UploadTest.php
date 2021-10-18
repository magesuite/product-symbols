<?php

namespace MageSuite\ProductSymbols\Test\Integration\Controller\Adminhtml\Symbol;

/**
 * @magentoAppArea adminhtml
 */
class UploadTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    protected $filesystem;

    public function setUp(): void
    {
        parent::setUp();

        $this->filesystem = $this->_objectManager->create('Magento\Framework\Filesystem');
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoDataFixture moveSymbolImageToTmp
     */
    public function testUploadActionWithCorrectData()
    {
        $_FILES = [ //phpcs:ignore
            'symbol_icon' => [
                'name' => 'magento_image.jpg',
                'type' => 'image/jpg',
                'tmp_name' => __DIR__.'/../../../_files/tmp/magento_image.jpg',
                'error' => 0,
                'size' => 13864
            ]
        ];

        $this->dispatch('backend/symbol/symbol/upload');

        $response = json_decode($this->getResponse()->getBody(), true);

        $this->assertTrue(isset($response['name']));
        $path = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath() . 'symbol/' . $response['name'];
        $fileExist = file_exists($path);
        $this->assertTrue($fileExist);
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoDataFixture moveSymbolImageToTmp
     */
    public function testUploadActionWithWrongData()
    {
        $_FILES = [ //phpcs:ignore
            'symbol_icon' => [
                'name' => 'magento_image.jpg',
                'type' => 'image/jpg',
                'tmp_name' => __DIR__.'/../../../d/_files/tmp/magento_image.jpg',
                'error' => 0,
                'size' => 13864
            ]
        ];

        $this->dispatch('backend/symbol/symbol/upload');

        $response = json_decode($this->getResponse()->getBody(), true);

        $this->assertFalse($response);
    }

    public static function moveSymbolImageToTmp()
    {
        include __DIR__.'/../../../_files/symbol_image.php';
    }
}
