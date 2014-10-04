<?php

/**
 * Applies the SUPEE-1868 patch
 *
 * @author Jonathan Day <jonathan@aligent.com.au>
 *
 */
require_once 'Mage/Adminhtml/controllers/DashboardController.php';

class Aligent_Security_Adminhtml_DashboardController extends Mage_Adminhtml_DashboardController {

    public function tunnelAction()
    {
        $httpClient = new Varien_Http_Client();
        $gaData = $this->getRequest()->getParam('ga');
        $gaHash = $this->getRequest()->getParam('h');
        if ($gaData && $gaHash) {
            $newHash = Mage::helper('adminhtml/dashboard_data')->getChartDataHash($gaData);
            if ($newHash == $gaHash) {
                $params = json_decode(base64_decode(urldecode($gaData)), true);
                if ($params) {
                    $response = $httpClient->setUri(Mage_Adminhtml_Block_Dashboard_Graph::API_URL)
                        ->setParameterGet($params)
                        ->setConfig(array('timeout' => 5))
                        ->request('GET');

                    $headers = $response->getHeaders();

                    $this->getResponse()
                        ->setHeader('Content-type', $headers['Content-type'])
                        ->setBody($response->getBody());
                }
            }
        }
    }

}