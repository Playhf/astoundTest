<?php

declare(strict_types=1);
namespace AstoundDRudenko\Badge\Block\Adminhtml\Form\Field\Badge\Options;

use Magento\CatalogInventory\Block\Adminhtml\Form\Field\Customergroup;
use \Magento\Framework\View\Element\Html\Select;
use \AstoundDRudenko\Badge\Model\Attribute\Badge\Config;

/**
 * Class Renderer
 * @package AstoundDRudenko\Badge\Block\Adminhtml\Form\Field\Badge\Options
 */
class Renderer extends Select
{
    /**
     * {@inheritDoc}
     */
    protected function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach (Config::DEFAULT_OPTIONS as $value => $label) {
                $this->addOption($value, addslashes($label));
            }
        }

        return parent::_toHtml();
    }

    /**
     * Set input name
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}