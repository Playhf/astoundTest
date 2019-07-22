<?php

declare(strict_types=1);
namespace AstoundDRudenko\Badge\Model\Attribute\Badge;

use \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Class Source
 * @package AstoundDRudenko\Badge\Model\Attribute\Badge
 */
class Source extends AbstractSource
{
    /**
     * {@inheritDoc}
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [];
            foreach (Config::DEFAULT_OPTIONS as $value => $label) {
                $this->_options[] = [
                    'value' => $value,
                    'label' => $label
                ];
            }
        }

        return $this->_options;
    }
}
