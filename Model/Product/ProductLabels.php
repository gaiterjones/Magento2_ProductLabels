<?php
declare(strict_types=1);

namespace Gaiterjones\ProductLabels\Model\Product;

/**
 * render labels for products
 */
class ProductLabels
{
    private $_debug;

    public function __construct(
    ) {
        $this->_debug=false;
    }


    public function getProductLabelHtml($_product)
    {
        $product_label = '';

        // SALE / SPECIAL OFFER
        //
        if ($this->isSaleProduct($_product)) {
            $product_label .= '<div class="product-label sale-label">'. __('SALE').'</div>';
        }

        // NEW PRODUCT ICON
        //
        if ($this->isNewProduct($_product)) {
            $product_label .= '<div class="product-label new-label">'. __('NEW').'</div>';
        }
        // OTHER ICONS
        //
        if ($_product->hasData('product_info_icon')) {

            // VEGAN
            if ($_product->getAttributeText('product_info_icon')==='TESTING') {
                $product_label .= '<div class="product-label new-label">TESTING</div>';
            }
        }

        return $product_label;
    }
    /**
     * isNewProduct returns boolean for new product flag based on either createdat or newsfrom product data
     *
     * @param  $_product   product
     * @param  boolean $_createdAt boolean - use created at data
     * @return boolean             true/false
     */
    public function isNewProduct($_product,$_createdAt=false)
    {

        $now = new \DateTime('NOW', new \DateTimeZone('UTC'));

        if ($_createdAt) {
            // use created at data for new product calculation
            //
            if ($_product->getTypeId() === 'configurable') {return false;
            }

            $productNewFromDate = $_product->getCreatedAt();
            $productNewToDate=$now->format('Y-m-d');

        } else {

            // use NEWSFROM data for new product calculation
            //
            $productNewFromDate = $_product->getNewsFromDate();
            $productNewToDate = $_product->getNewsToDate();

            if(empty($productNewToDate)) {
                $productNewToDate=$now->format('Y-m-d');
            }
        }

        if(!(empty($productNewFromDate))) {

            $newFromDate = new \DateTime($productNewFromDate, new \DateTimeZone('UTC'));
            $newToDate = new \DateTime($productNewToDate, new \DateTimeZone('UTC'));
            $productAgeDays=$newFromDate->diff($now);

            // show products as new if created in last XX days
            //
            if($productAgeDays->format('%a') <= 90) {

                // this shit is new!
                //
                return true;
            }
        }


            return false;
    }



    public function isSaleProduct($_product)
    {
        // Init time
        //
        $_productSpecialPrice=false;
        $_now = new \DateTime('NOW', new \DateTimeZone('UTC'));

        // check for special price time range
        //
        if(strtotime($_product->getSpecialFromDate()) <= $_now->getTimestamp() && strtotime($_product->getSpecialToDate()) >= $_now->getTimestamp()) {
            $_productSpecialPrice=true;
        }

        if ($_productSpecialPrice) {

            return true;
        }

        return false;
    }

}
