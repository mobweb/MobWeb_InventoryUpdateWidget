# MobWeb_InventoryUpdateWidget extension for Magento

This extension creates two new widgets.

### Recently Created Products

This widget shows all the products that where created the previous day. It uses Magento's built in "created_at" product attribute to filter the products.

### Recently Restocked Products

This widget shows all the products whose inventory was increased from 0 the previous day. During the installation of this extension, a new product attribute named "restocked_at" is created, which is updated on every product save to determine such a restock. This attribute is then used in the widget to filter the products.

## Customization

To change the timespan (for example show products that were modified today, not yesterday), have a look at Refilled.php and Created.php.

The templates have been copied directly from Magento's default theme, they are called products_grid.phtml and products_list.phtml and can be modified freely. 

## Installation

Install using [colinmollenhour/modman](https://github.com/colinmollenhour/modman/).