<?php

/**
 * Applies an equivalent to the APPSEC-212 patch (http://www.magentocommerce.com/index.php/getmagento/ce_patches/PATCH_SUPEE-2677_EE_1.13.0.2_v2.sh)
 * via a helper rewrite for easy installation.
 *
 */
class Aligent_Security_Helper_Cms_Wysiwyg_Images extends Mage_Cms_Helper_Wysiwyg_Images
{

    /**
     * Images Storage root directory
     * @var string
     */
    protected $_storageRoot;

    /**
     * Images Storage root directory
     *
     * @return string
     */
    public function getStorageRoot()
    {
        if (!$this->_storageRoot) {
            $this->_storageRoot = realpath(
                    Mage::getConfig()->getOptions()->getMediaDir()
                    . DS . Mage_Cms_Model_Wysiwyg_Config::IMAGE_DIRECTORY
                ) . DS;
        }
        return $this->_storageRoot;
    }

    /**
     * Return path of the current selected directory or root directory for startup
     * Try to create target directory if it doesn't exist
     *
     * @throws Mage_Core_Exception
     * @return string
     */
    public function getCurrentPath()
    {
        if (!$this->_currentPath) {
            $currentPath = $this->getStorageRoot();
            $node        = $this->_getRequest()->getParam($this->getTreeNodeName());
            if ($node) {
                $path = realpath($this->convertIdToPath($node));
                if (is_dir($path) && FALSE !== stripos($path, $currentPath)) {
                    $currentPath = $path;
                }
            }
            $io = new Varien_Io_File();
            if (!$io->isWriteable($currentPath) && !$io->mkdir($currentPath)) {
                $message = Mage::helper('cms')->__('The directory %s is not writable by server.', $currentPath);
                Mage::throwException($message);
            }
            $this->_currentPath = $currentPath;
        }
        return $this->_currentPath;
    }

    /**
     * Return URL based on current selected directory or root directory for startup
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        if (!$this->_currentUrl) {
            $path              = str_replace(realpath(Mage::getConfig()->getOptions()->getMediaDir()), '', $this->getCurrentPath());
            $path              = trim($path, DS);
            $this->_currentUrl = Mage::app()->getStore($this->_storeId)->getBaseUrl('media') .
                $this->convertPathToUrl($path) . '/';
        }
        return $this->_currentUrl;
    }
}