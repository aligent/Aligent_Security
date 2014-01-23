<?php

/**
 * Applies an equivalent to the APPSEC-212 patch (http://www.magentocommerce.com/index.php/getmagento/ce_patches/PATCH_SUPEE-2677_EE_1.13.0.2_v2.sh)
 * via a helper rewrite for easy installation.
 *
 */
class Aligent_Security_Model_Cms_Model_Wysiwyg_Images_Storage extends Mage_Cms_Model_Wysiwyg_Images_Storage
{

    /**
     * Return one-level child directories for specified path
     *
     * @param string $path Parent directory path
     *
     * @return Varien_Data_Collection_Filesystem
     */
    public function getDirsCollection($path)
    {
        if (Mage::helper('core/file_storage_database')->checkDbUsage()) {
            $subDirectories = Mage::getModel('core/file_storage_directory_database')->getSubdirectories($path);
            foreach ($subDirectories as $directory) {
                $fullPath = rtrim($path, DS) . DS . $directory['name'];
                if (!file_exists($fullPath)) {
                    mkdir($fullPath, 0777, TRUE);
                }
            }
        }

        $conditions = array('reg_exp' => array(), 'plain' => array());

        foreach ($this->getConfig()->dirs->exclude->children() as $dir) {
            $conditions[$dir->getAttribute('regexp') ? 'reg_exp' : 'plain'][(string)$dir] = TRUE;
        }
        // "include" section takes precedence and can revoke directory exclusion
        foreach ($this->getConfig()->dirs->include->children() as $dir) {
            unset($conditions['regexp'][(string)$dir], $conditions['plain'][(string)$dir]);
        }

        $regExp            = $conditions['reg_exp'] ? ('~' . implode('|', array_keys($conditions['reg_exp'])) . '~i') : NULL;
        $collection        = $this->getCollection($path)
            ->setCollectDirs(TRUE)
            ->setCollectFiles(FALSE)
            ->setCollectRecursively(FALSE);
        $storageRootLength = strlen($this->getHelper()->getStorageRoot());

        foreach ($collection as $key => $value) {
            $rootChildParts = explode(DIRECTORY_SEPARATOR, substr($value->getFilename(), $storageRootLength));

            if (array_key_exists(end($rootChildParts), $conditions['plain'])
                || ($regExp && preg_match($regExp, $value->getFilename()))
            ) {
                $collection->removeItemByKey($key);
            }
        }

        return $collection;
    }
}
