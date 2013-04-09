<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * New products widget
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class MobWeb_InventoryUpdateWidget_Block_Product_Widget_Restocked
    extends Mage_Catalog_Block_Product_New
    implements Mage_Widget_Block_Interface
{
    protected function _beforeToHtml()
    {
        // Create a new product collection
        $collection = Mage::getResourceModel( 'catalog/product_collection' );

        // Get only the visible products, the ones that are currently in stock
        $collection->setVisibility( Mage::getSingleton('catalog/product_visibility' )->getVisibleInCatalogIds() );

        // Load the product attributes & prices
        $collection = $this->_addProductAttributesAndPrices( $collection );

        // Get yesterday's date
        $yesterday = Mage::helper( 'InventoryUpdateWidget/Data' )->getYesterday();

        // Add the relevant filters
        $collection->addStoreFilter()
            //TODO: If the "restocked_at" attribute is setup and filled the
            //exact same way as the "created_at" field, we could use the same
            //filtering method here as we do when filtering the "created_at"
            //collection, which is the "Magento Way" of doing this...
            // Notice: Using addAttributeToFilter doesn't work here.
            // About 50% of the time it would result all the products,
            // and the other 50% the correct ones...
            ->addFieldToFilter( 'restocked_at', array( 'eq'=>$yesterday ) )
            ->addAttributeToSort( 'updated_at', 'desc' )
            ->setPageSize( $this->getProductsCount() )
            ->setCurPage( 1 );

        // Save the product collection
        $this->setProductCollection( $collection );

        // Set the title for the block
        $this->title = 'Recently Restocked Products';

        // Set debugging mode
        $debug = false;
        if( $debug ) {
            $debug = array();
            $debug[ 'date' ] = $yesterday;
            $this->setData( 'debug', $debug );
            $collection->printLogQuery( true );
        }

        // Call the grandparent directly. Calling the parent would overwrite
        // everything we just did...
        return Mage_Catalog_Block_Product_Abstract::_beforeToHtml();
    }

    // Copied from Mage_Catalog_Block_Product_Widget_New
    protected function _construct()
    {
        parent::_construct();

        $this->addPriceBlockType('bundle', 'bundle/catalog_product_price', 'bundle/catalog/product/price.phtml');
    }

    // Copied from Mage_Catalog_Block_Product_Widget_New
    public function getProductsCount()
    {
        if (!$this->hasData('products_count')) {
            return parent::getProductsCount();
        }
        return $this->_getData('products_count');
    }
}
