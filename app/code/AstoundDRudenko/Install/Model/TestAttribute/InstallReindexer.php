<?php

declare(strict_types=1);
namespace AstoundDRudenko\Install\Model\TestAttribute;

use Magento\Framework\Indexer\AbstractProcessor;

/**
 * Reindex needed processor in install scripts
 * Class InstallReindexer
 * @package AstoundDRudenko\Install\Model\TestAttribute
 */
class InstallReindexer
{
    /**
     * @var AbstractProcessor[]
     */
    private $processors;

    /**
     * InstallReindexer constructor.
     * @param array $processors
     */
    public function __construct(array $processors)
    {
        $this->processors = $processors;
    }

    /**
     * Reindex needed processors
     * @param array $productIds
     */
    public function reindexProductAttributes(array $productIds)
    {
        foreach ($this->processors as $processor) {
            $processor->reindexList($productIds);
        }
    }
}
