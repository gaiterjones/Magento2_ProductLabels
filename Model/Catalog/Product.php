<?php
declare(strict_types=1);

namespace Gaiterjones\ProductLabels\Model\Catalog;

class Product
{

    private $_debug;

    protected $_product = null;
    protected $_registry;
    protected $_productRepository;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        $this->_registry = $registry;
        $this->_productRepository = $productRepository;

        $this->_debug=false;
    }

    public function getCurrentCategory()
    {
        return $this->_registry->registry('current_category');
    }

    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

    public function getProductById($id)
    {
        return $this->_productRepository->getById($id);
    }

    public function getProductBySku($sku)
    {
        return $this->_productRepository->get($sku);
    }
}
