<?php
/**
 * Copyright © NuTech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Nulogic\Base\Model;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Unserialize\Unserialize;

/**
 * Wrapper for Serialize
 */
class Serializer
{
    /**
     * @var null|SerializerInterface
     */
    private $serializer;

    /**
     * @var Unserialize
     */
    private $unserialize;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param Unserialize $unserialize
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Unserialize $unserialize
    ) {
        if (interface_exists(SerializerInterface::class)) {
            // For Magento >= 2.2
            $this->serializer = $objectManager->get(SerializerInterface::class);
        }
        $this->unserialize = $unserialize;
    }

    /**
     * @inheritDoc
     */
    public function serialize($value)
    {
        try {
            if ($this->serializer !== null) {
                return $this->serializer->serialize($value);
            }

            return '{}';
        } catch (\Exception $e) {
            return '{}';
        }
    }

    /**
     * @inheritDoc
     */
    public function unserialize($value)
    {
        if (false === $value || null === $value || '' === $value) {
            return false;
        }

        if ($this->serializer === null) {
            return $this->unserialize->unserialize($value);
        }

        try {
            return $this->serializer->unserialize($value);
        } catch (\InvalidArgumentException $exception) {
            return false;
        }
    }
}
