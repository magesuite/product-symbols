<?php

declare(strict_types=1);

namespace MageSuite\ProductSymbols\Test\Integration\Controller\Adminhtml\Symbol;

/**
 * @magentoAppArea adminhtml
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class UploadTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    protected ?\Magento\Framework\Filesystem $filesystem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filesystem = $this->_objectManager->get(\Magento\Framework\Filesystem::class);
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoDataFixture MageSuite_ProductSymbols::Test/Integration/_files/symbol_image.php
     */
    public function testUploadActionWithCorrectData(): void
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
     * @magentoDataFixture MageSuite_ProductSymbols::Test/Integration/_files/symbol_image.php
     */
    public function testUploadActionWithWrongData(): void
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
}
