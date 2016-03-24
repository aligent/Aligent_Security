Aligent Security Extension
=====================
Security patches for Magento.
Facts
-----
- version: 0.1.0
- extension key: Aligent_APPSEC212
- [extension on GitHub](https://github.com/aligent/Aligent_APPSEC212

Description
-----------
Applies an equivalent to the APPSEC-212 patch (SUPEE-2518) (http://www.magentocommerce.com/index.php/getmagento/ce_patches/PATCH_SUPEE-2518_CE_1.5.1.0-1.7.0.2_v1.sh)

Issues
-----------
- You can encountered issues if you are symlinking the media folder.
  - Thumbnails in admin can break, because this module uses `realpath` function
- To resolve the issue apply SUPEE-2518 directly instead of using this module.

Requirements
------------
- PHP >= 5.2.0
- Mage_Core

Compatibility
-------------
- Magento >= 1.4


