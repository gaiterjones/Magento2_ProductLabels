<?php
declare(strict_types=1);

namespace Gaiterjones\ProductLabels\ViewModel;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Gaiterjones\ProductLabels\Model\Catalog\Product as GetProduct;
use Gaiterjones\ProductLabels\Helper\ProductLabelsHelper as ProductLabelsHelper;

class ProductLabels implements ArgumentInterface
{
    /**
     * @var getProduct
     */
    protected $getProduct;
    /**
     * @var getProductLabelsHelper
     */
    protected $getProductLabelsHelper;

    public function __construct(
        GetProduct $getProduct,
        ProductLabelsHelper $getProductLabelsHelper
    ) {
        $this->getProduct = $getProduct;
        $this->getProductLabelsHelper = $getProductLabelsHelper;
    }

    public function getCurrentProduct()
    {
        return $this->getProduct->getCurrentProduct();
    }

    public function isNewProduct($_product,$_createdAt=false)
    {
        return $this->getProductLabelsHelper->isNewProduct($_product, $_createdAt=false);
    }

    public function isSaleProduct($_product)
    {
        return $this->getProductLabelsHelper->isSaleProduct($_product);
    }

    public function getProductInfoIconData()
    {
        // init data object
        //
        $_data = new \StdClass();
        $_data->productlabel = false;
        $_data->infoicon = false;
        $_data->infoicontext='';
        $_data->childSwatchElements=array();
        $_data->childSaleSkus=array();
        $_data->childCount=0;
        $_data->childNewCount=0;
        $_data->childNewDescription='NEW PRODUCTS';
        $_data->childSaleCount=0;

        $_product=$this->getCurrentProduct();

        if ($_product instanceof \Magento\Catalog\Model\Product) {

            // A.N. OTHER icon
            //
            if ($_product->hasData('product_info_icon')) {
                $_data->infoicon=$this->getProductIcon($_product->getAttributeText('product_info_icon'));
                $_data->infoicontext=$_product->getAttributeText('product_info_icon');
            }

            // Product LABELS
            //

            // GET LABELS
            //
            // 1. SALE LABELS for PARENT / CHILDREN
            //

            // NEW PRODUCT LABEL FOR CONFIGURABLE CHILDREN
            //
            if ($_product->getTypeId() == 'configurable') {

                $_children =$_product->getTypeInstance()->getUsedProducts($_product);

                foreach ($_children as $_child){
                    // detect newly born infants
                    //
                    if ($this->isNewProduct($_child)) {
                        // get child options
                        //
                        $_options=$_product->getTypeInstance()->getConfigurableOptions($_product);

                        foreach($_options as $_key => $_attributes){

                            // get attributes
                            //
                            foreach($_attributes as $_attribute){

                                // find product attribute
                                //
                                if ($_attribute['sku']==$_child->getSku()) {
                                    // this creates the element id of the swatch for the product....
                                    // id added to array for jquery stuff later
                                    //
                                    // TODO what happens when child has multiple options - colour and size etc, etc. ?
                                    //
                                    // ignore the qty option ???
                                    //
                                    if (strpos($_attribute['attribute_code'], 'product_quantity') !== false) {continue;
                                    }
                                    if (strpos($_attribute['attribute_code'], 'product_colour') !== false) {$_data->childNewDescription='NEW COLOURS';
                                    }


                                    $_data->childSwatchElements[]=array(
                                      'id' => 'option-label-'.$_attribute['attribute_code'].'-'.$_key.'-item-'.$_attribute['value_index'],
                                      'class' => 'newproduct'
                                    );
                                    $_data->childNewCount++;
                                }
                            }

                        }

                    }

                }

                if ($_data->childNewCount>0) {
                    $_data->productlabel .= '<div style="width: 25%; text-align: center;" class="product-label new-label">'. $_data->childNewCount . ' '. __($_data->childNewDescription).'</div>';
                }

            }

            // NEW PRODUCT LABEL FOR PARENT / SIMPLE
            //
            if ($this->isNewProduct($_product)) {
                $_data->productlabel .= '<div style="width: 25%; text-align: center;" class="product-label new-label">'. __('NEW').'</div>';
            }

        } // not a product... WTF!

        // return data object
        //
        return $_data;
    }

}
?>
