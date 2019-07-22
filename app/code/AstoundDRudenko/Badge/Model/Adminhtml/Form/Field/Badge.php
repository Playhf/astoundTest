<?php

declare(strict_types=1);
namespace AstoundDRudenko\Badge\Model\Adminhtml\Form\Field;

use \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use \AstoundDRudenko\Badge\Block\Adminhtml\Form\Field\Badge\Options\Renderer;

/**
 * System config product badge priority form field
 *
 * Class Badge
 * @package AstoundDRudenko\Badge\Block\Adminhtml\Form\Field
 */
class Badge extends AbstractFieldArray
{
    /**
     * @var Renderer
     */
    private $optionsRenderer;

    /**
     * Retrieves renderer
     *
     * @return Renderer|\Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getBadgesOptionsRenderer()
    {
        if (!$this->optionsRenderer) {
            $this->optionsRenderer = $this->getLayout()->createBlock(
                Renderer::class,
                'badge.options.renderer',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->optionsRenderer->setClass('badge_select');
        }

        return $this->optionsRenderer;
    }

    /**
     * Prepare config row ro render and add renderer
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'badge',
            ['label' => __('Badge'), 'renderer' => $this->getBadgesOptionsRenderer()]
        );
        $this->addColumn('priority', ['label' => __('Priority')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Badge Priority');
    }

    /**
     * Prepare existing row data object
     *
     * @param \Magento\Framework\DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $optionExtraAttr = [];
        $optionExtraAttr['option_' . $this->getBadgesOptionsRenderer()->calcOptionHash($row->getData('badge'))] =
            'selected="selected"';
        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );
    }
}
