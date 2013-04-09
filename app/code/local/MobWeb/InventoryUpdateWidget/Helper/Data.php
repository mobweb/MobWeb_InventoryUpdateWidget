<?php
class MobWeb_InventoryUpdateWidget_Helper_Data extends Mage_Core_Helper_Abstract
{
	// Use the same string to format the date across the whole extension
	public $date_format = 'Y-m-d';

	// Returns yesterday's date in the correct format
	public function getYesterday()
	{
		// Get a timestamp for "today" from Magento
		$today = Mage::getModel( 'core/date' )->timestamp();

		// Subtract 24 hours (in seconds) from $today to get $yesterday
		return date( $this->date_format, $today-(60*60*24) );
	}
}