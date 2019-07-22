<?php

namespace AstoundDRudenko\Badge\Model\System\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use \Magento\Framework\App\Config\Value;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Model\Context;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Math\Random as MathRandom;

/**
 * Backend system config model
 *
 * Class Badge
 * @package AstoundDRudenko\Badge\Model\System\Config\Backend
 */
class Badge extends Value
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var MathRandom
     */
    private $mathRandom;

    /**
     * Badge constructor.
     * @param MathRandom $mathRandom
     * @param SerializerInterface $serializer
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        MathRandom $mathRandom,
        SerializerInterface $serializer,
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->serializer = $serializer;
        $this->mathRandom = $mathRandom;
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Unset array element with '__empty' key
     * Save multiple config values to database if exists
     *
     * @return $this
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            unset($value['__empty']);
        }

        $result = [];
        foreach ($value as $row) {
            if (!is_array($row)
                || !array_key_exists('badge', $row)
                || !array_key_exists('priority', $row)
            ) {
                continue;
            }
            $badge = $row['badge'];
            $priority = (int)$row['priority'];
            $result[$badge] = $priority;
        }

        $result = $this->serializer->serialize($result);
        $this->setValue($result);

        return parent::beforeSave();
    }

    /**
     * Decode values from db and reproducing in valid format
     *
     * @return Value
     */
    protected function _afterLoad()
    {
        $value = $this->getValue();
        if (is_string($value) && !empty($value)) {
            $value = $this->serializer->unserialize($value);
        } else {
            $value = [];
        }

        unset($value['__empty']);

        $result = [];
        foreach ($value as $badge => $priority) {
            $resultId = $this->mathRandom->getUniqueHash('_');
            $result[$resultId] = ['badge' => $badge, 'priority' => (int)$priority];
        }
        $this->setValue($result);

        return parent::_afterLoad();
    }
}
