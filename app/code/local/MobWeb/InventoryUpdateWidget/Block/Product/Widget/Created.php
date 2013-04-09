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
class MobWeb_InventoryUpdateWidget_Block_Product_Widget_Created
    extends Mage_Catalog_Block_Product_New
    implements Mage_Widget_Block_Interface
{
    protected function _beforeToHtml()
    {
        // Get the time period for yesterday
        $yesterdayStartOfDayDate = Mage::app()
            ->getLocale()
            ->date()
            ->sub( '3', Zend_Date::DAY_SHORT )
            ->setTime( '00:00:00' )
            ->toString( Varien_Date::DATETIME_INTERNAL_FORMAT );

        $yesterdayEndOfDayDate = Mage::app()
            ->getLocale()
            ->date()
            ->sub( '3', Zend_Date::DAY_SHORT )
            ->setTime( '23:59:59' )
            ->toString( Varien_Date::DATETIME_INTERNAL_FORMAT );

        // Get yesterday's date
        $yesterday = Mage::helper( 'InventoryUpdateWidget/Data' )->getYesterday();

        // Create a new product collection
        $collection = Mage::getResourceModel( 'catalog/product_collection' );

        // Get only the visible products, the ones that are currently in stock
        $collection->setVisibility( Mage::getSingleton( 'catalog/product_visibility' )->getVisibleInCatalogIds() );

        // Load the product attributes & prices
        $collection = $this->_addProductAttributesAndPrices( $collection );

        // Add the relevant filters
        $collection->addStoreFilter()
            ->addAttributeToFilter( 'created_at', array( 'or'=> array(
                0 => array( 'date' => true, 'to' => $yesterdayEndOfDayDate ),
                1 => array( 'is' => new Zend_Db_Expr( 'null' ) ) )
            ), 'left')
            ->addAttributeToFilter( 'created_at', array( 'or'=> array(
                0 => array( 'date' => true, 'from' => $yesterdayStartOfDayDate),
                1 => array( 'is' => new Zend_Db_Expr( 'null' ) ) )
            ), 'left' )
            // Don't show any products in both the "created_at"
            // and "restocked_at" blocks
            ->addFieldToFilter( 'restocked_at', array( 'neq'=>$yesterday ) )
            ->addAttributeToSort( 'created_at', 'desc' )
            ->setPageSize( $this->getProductsCount() )
            ->setCurPage( 1 );

        // Save the product collection
        $this->setProductCollection( $collection );

        // Set the title for the block
        $this->title = 'Recently Created Products';

        // Set debugging mode
        $debug = false;
        if( $debug ) {
            $debug = array();
            $debug[ 'from' ] = $yesterdayStartOfDayDate;
            $debug[ 'to' ] = $yesterdayEndOfDayDate;
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
