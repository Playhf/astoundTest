<?php

declare(strict_types=1);
namespace AstoundDRudenko\Badge\Model\Attribute\Badge;

use \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;

/**
 * Backend model for badge attribute
 *
 * Class Backend
 * @package AstoundDRudenko\Badge\Model\Attribute\Badge
 */
class Backend extends AbstractBackend
{
    /**
     * Before Attribute Save Process
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $attributeCode = $this->getAttribute()->getName();
        if ($attributeCode === Config::BADGE_ATTRIBUTE_CODE) {
            $data = $object->getData($attributeCode);
            if (!is_array($data)) {
                $data = [];
            }
            $object->setData($attributeCode, implode(',', $data) ?: null);
        }
        if (!$object->hasData($attributeCode)) {
            $object->setData($attributeCode, null);
        }
        return $this;
    }

    /**
     * After Load Attribute Process
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     */
    public function afterLoad($object)
    {
        $attributeCode = $this->getAttribute()->getName();
        if ($attributeCode === Config::BADGE_ATTRIBUTE_CODE) {
            $data = $object->getData($attributeCode);
            if ($data) {
                if (!is_array($data)) {
                    $object->setData($attributeCode, explode(',', $data));
                } else {
                    $object->setData($attributeCode, $data);
                }
            }
        }
        return $this;
    }
}
