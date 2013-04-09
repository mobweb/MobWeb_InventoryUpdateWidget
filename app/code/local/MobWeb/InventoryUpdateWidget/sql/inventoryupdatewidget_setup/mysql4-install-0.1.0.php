<?php
/*
 *
 * This file installs the custom attributes into the DB. To see if it has been
 * executed properly, see the "core_resource" table in the DB. To run the
 * script again, simply delete the corresponding row from "core_resources"
 *
 */
$installer = $this;
$installer->startsetup();

// Create the custom attribute
$installer->addAttribute(
	'catalog_product',
	'restocked_at',
	array(
		'group' => 'General', // This adds the attribute to the attribute set,
		// adding it in the backend
		'type' => 'varchar',
		'label' => 'Restocked At',
		'input' => 'text',
		'required' => 0,
		'visible' => 1 // TODO: Change to 0 after debugging?
	)
);

$installer->endSetup();