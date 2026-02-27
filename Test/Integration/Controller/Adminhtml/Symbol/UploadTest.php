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
    protected ?string $filesDirectory;
    protected $uri = 'backend/symbol/symbol/upload'; // phpcs:ignore
    protected $resource = \MageSuite\ProductSymbols\Controller\Adminhtml\Symbol\Upload::ADMIN_RESOURCE; // phpcs:ignore

    protected function setUp(): void
    {
        parent::setUp();

        $this->filesystem = $this->_objectManager->get(\Magento\Framework\Filesystem::class);
        $this->filesDirectory = join(DIRECTORY_SEPARATOR, [
            $this->_objectManager->get(\Magento\Framework\Module\Dir::class)->getDir('MageSuite_ProductSymbols'),
            'Test',
            'Integration',
            '_files',
            'tmp'
        ]);
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
                'tmp_name' => join(DIRECTORY_SEPARATOR, [$this->filesDirectory, 'magento_image.jpg']),
                'error' => 0,
                'size' => 13864
            ]
        ];

        $this->dispatch('backend/symbol/symbol/upload');

        $response = json_decode($this->getResponse()->getBody(), true);

        $this->assertArrayHasKey('name', $response);
        $path = join(DIRECTORY_SEPARATOR, [$this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath(), 'symbol', $response['name']]);
        $this->assertFileExists($path);
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
                'tmp_name' => join(DIRECTORY_SEPARATOR, [$this->filesDirectory, 'missing_magento_image.jpg']),
                'error' => 0,
                'size' => 13864
            ]
        ];

        $this->dispatch('backend/symbol/symbol/upload');

        $response = json_decode($this->getResponse()->getBody(), true);

        $this->assertFalse($response);
    }
}
