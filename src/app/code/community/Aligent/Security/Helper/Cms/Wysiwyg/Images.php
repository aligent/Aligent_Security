<?php
/**
 * Applies an equivalent to the APPSEC-212 patch (http://www.magentocommerce.com/index.php/getmagento/ce_patches/PATCH_SUPEE-2518_CE_1.5.1.0-1.7.0.2_v1.sh)
 * via a helper rewrite for easy installation.
 *
 */
class Aligent_Security_Helper_Cms_Wysiwyg_Images extends Mage_Cms_Helper_Wysiwyg_Images {

    /**
     * Images Storage root directory
     *
     * @return string
     */
    public function getStorageRoot() {
        return realpath(parent::getStorageRoot());
    }

    /**
     * Decode HTML element id
     *
     * @param string $id
     * @return string
     */
    public function convertIdToPath($id) {
        $currentPath = $this->getStorageRoot();
        $path = realpath(parent::convertIdToPath($id));
        if (is_dir($path) && false !== stripos($path, $currentPath)) {
            $currentPath = $path;
        }
        return $currentPath;
    }

}