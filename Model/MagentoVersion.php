<?php
/**
 * Copyright © NuTech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Nulogic\Base\Model;

/**
 * Class MagentoVersion is used for faster retrieving magento version
 */
class MagentoVersion
{
    /**
     * @var MAGENTO_VERSION
     */
    public const MAGENTO_VERSION = 'amasty_magento_version';

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var \Magento\Framework\App\Cache\Type\Config
     */
    private $cache;

    /**
     * @var string
     */
    private $magentoVersion;

    /**
     * @param \Magento\Framework\App\Cache\Type\Config $cache
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     */
    public function __construct(
        \Magento\Framework\App\Cache\Type\Config $cache,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {
        $this->productMetadata = $productMetadata;
        $this->cache = $cache;
    }

    /**
     * Get
     *
     * @return string
     */
    public function get()
    {
        if (!$this->magentoVersion
            && !($this->magentoVersion = $this->cache->load(self::MAGENTO_VERSION))
        ) {
            $this->magentoVersion = $this->productMetadata->getVersion();
            $this->cache->save($this->magentoVersion, self::MAGENTO_VERSION);
        }

        return $this->magentoVersion;
    }
}
