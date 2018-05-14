# [MageVision](https://www.magevision.com/) Cart Product Comment Extension for Magento 2

## Overview
The Cart Product Comment extension gives you the ability to your customers to leave a comment to each product in cart. Comments are visible on every order in "My Account" and on the order confirmation email for customers and in Magento admin for Admin users. Admin users can view customer's comments and take action if needed it. Improve your customer's experience and satisfy your customers the best.

## Key Features
    * Customers can leave a comment to each product in cart
	* Comments are visible on every order in "My Account" and on the order confirmation email for customers
    * Comments are visible on every order in Magento admin for Admin users
	* Admin users can view customer's comments and take action if needed it
    * Improve your customer's experience
    * Satisfy your customers the best

## Other Features
	* Developed by a Magento Certified Developer
	* Meets Magento standard development practices
    * Single License is valid for 1 live Magento installation and unlimited test Magento installations
	* Simple installation
	* 100% open source

## Compatibility
Magento Community Edition 2.0 - 2.1 - 2.2

## Installing the Extension
	* Backup your web directory and store database
	* Download the extension
		1. Sign in to your account
		2. Navigate to menu My Account → My Downloads
		3. Find the extension and click to download it
	* Extract the downloaded ZIP file in a temporary directory
	* Upload the extracted folders and files of the extension to base (root) Magento directory. Do not replace the whole folders, but merge them. If you have downloaded the extension from Magento Marketplace, then create the following folder path app/code/MageVision/CartProductComment and upload there the extracted folders and files.
        * Connect via SSH to your Magento server as, or switch to, the Magento file system owner and run the following commands from the (root) Magento directory:
            1. cd path_to_the_magento_root_directory 
            2. php -f bin/magento module:enable MageVision_CartProductComment
            3. php -f bin/magento setup:upgrade
            4. php -f bin/magento setup:di:compile
            5. php -f bin/magento setup:static-content:deploy
        * Log out from Magento admin and log in again
		
## Support
If you need support or have any questions directly related to a [MageVision](https://www.magevision.com/) extension, please contact us at [support@magevision.com](mailto:support@magevision.com)
