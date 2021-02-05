<?php
declare(strict_types=1);

namespace Gaiterjones\ProductLabels\Helper;
use Magento\Framework\App\Helper\AbstractHelper;
use Gaiterjones\ProductLabels\Model\Product\ProductLabels as GetProductLabels;

class ProductLabelsHelper extends AbstractHelper
{

    protected $_getProductLabels;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        GetProductLabels $getProductLabels
    ) {
        $this->_getProductLabels=$getProductLabels;
        parent::__construct($context);
    }


    public function getProductLabelHtml($product)
    {
        return $this->_getProductLabels->getProductLabelHtml($product);
    }

    public function isNewProduct($_product,$_createdAt=false)
    {
        return $this->_getProductLabels->isNewProduct($_product, $_createdAt=false);
    }

    public function isSaleProduct($product)
    {
        return $this->_getProductLabels->isSaleProduct($product);
    }
}
