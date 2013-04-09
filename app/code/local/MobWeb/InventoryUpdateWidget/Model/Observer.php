<?php

class MobWeb_InventoryUpdateWidget_Model_Observer
{
	public function catalogProductSaveBefore( $observer )
	{
		// Get a reference to the product from the $observer
		$product = $observer->getEvent()->getProduct();

		// If it's a new product, don't do anything here
		if( $product->isObjectNew() ) {
			return;
		}

		// If it's an update, check if the product was changed
		if( $product->hasDataChanges() ) {
			// Get the product's old & new inventory qty
			$qty_old = (int) $product->stock_data[ 'original_inventory_qty' ];
			$qty_new = (int) $product->stock_data[ 'qty' ];

			// If the original inventory qty was 0, and the new one
			// is not 0, there was a restock
			if( $qty_old === 0 && $qty_new > $qty_old ) {

				// Get the current timestamp from Magento
				$timestamp = Mage::getModel( 'core/date' )
					->date( Mage::helper( 'InventoryUpdateWidget/Data' )->date_format );

				// Update the "restocked_at" attribute with today's date
				$product->setData( 'restocked_at', $timestamp );

				// Set the "in_stock" attribute to true
				$product->setData( 'is_in_stock', '1' );
			}
		}
	}
}