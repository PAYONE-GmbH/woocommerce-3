<?php

namespace Payone\Payone\Api;

class Request extends DataTransfer
{
    const API_URL = 'https://api.pay1.de/post-gateway/';
    const SOLUTION_NAME = 'payone-woocommerce-3';
    const INTEGRATOR_NAME = 'woocommerce';

    private $apiLogEnabled = false;

    public function __construct()
    {
        parent::__construct();

        $options = get_option('payone_account', [
            'account_id' => '',
            'merchant_id' => '',
            'portal_id' => '',
            'key' => '',
            'mode' => 'test',
            'api_log' => 0,
            'transaction_log' => 0,
        ]);

        $this->apiLogEnabled = $options['api_log'] ? true : false;

        $this
            ->set('api_version', '3.10')
            ->setMode($options['mode'])
            ->setAccountId($options['account_id'])
            ->setMerchantId($options['merchant_id'])
            ->setPortalId($options['portal_id'])
            ->setPortalKey($options['key'])
            ->set('encoding', 'UTF-8')
            ->set('solution_name', self::SOLUTION_NAME)
            ->set('solution_version', PAYONE_PLUGIN_VERSION)
            ->set('integrator_name', self::INTEGRATOR_NAME)
            ->set('integrator_version', $this->getWooCommerceVersionNumber())
        ;
    }

    public function setParameters($keyValues)
    {
        foreach ($keyValues as $key => $value)
        {
            $this->set($key, $value);
        }
    }

    public function execute()
    {
        $ch = curl_init(self::API_URL);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POST => 1,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POSTFIELDS => $this->getPostfieldsFromParameters(),
        ]);

        $result = curl_exec($ch);

        curl_close($ch);

        $response = $this->createResponse($result);
        if ($this->apiLogEnabled) {
            $logEntry = $this->createLogEntry($this, $response);
            $logEntry->save();
        }
    }

    /**
     * @return string
     */
    public function getAccountId()
    {
        return $this->get('aid');
    }

    /**
     * @param string $accountId
     *
     * @return Request
     */
    public function setAccountId($accountId)
    {
        $this->set('aid', $accountId);

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->get('mid');
    }

    /**
     * @param string $merchantId
     *
     * @return Request
     */
    public function setMerchantId($merchantId)
    {
        $this->set('mid', $merchantId);

        return $this;
    }

    /**
     * @return string
     */
    public function getPortalId()
    {
        return $this->get('portalid');
    }

    /**
     * @param string $portalId
     *
     * @return Request
     */
    public function setPortalId($portalId)
    {
        $this->set('portalid', $portalId);

        return $this;
    }

    /**
     * @return string
     */
    public function getPortalKey()
    {
        return $this->get('key');
    }

    /**
     * @param string $portalKey
     *
     * @return Request
     */
    public function setPortalKey($portalKey)
    {
        $this->set('key', hash('md5', $portalKey));

        return $this;
    }

    /**
     * @return string
     */
    public function getApiVersion()
    {
        return $this->get('api_version');
    }

    /**
     * @param string $apiVersion
     *
     * @return Request
     */
    public function setApiVersion($apiVersion)
    {
        $this->set('api_version', $apiVersion);

        return $this;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->get('mode');
    }

    /**
     * @param string $mode
     *
     * @return Request
     */
    public function setMode($mode)
    {
        $this->set('mode', $mode);

        return $this;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->get('encoding');
    }

    /**
     * @param string $encoding
     *
     * @return Request
     */
    public function setEncoding($encoding)
    {
        $this->set('encoding', $encoding);

        return $this;
    }
    /**
     * @param string $result
     *
     * @return Response
     */
    private function createResponse($result)
    {
        /**
         * status=ERROR
        errorcode=1202
        errormessage=Parameter {request} faulty or missing
        customermessage=An error occured while processing this transaction (wrong parameters).
         */

        $response = new Response();
        $lines = explode("\n", $result);
        foreach ($lines as $line) {
            $keyValue = explode('=', $line);
            $key = isset($keyValue[0]) ? trim($keyValue[0]) : null;
            $value = isset($keyValue[1]) ? trim($keyValue[1]) : null;

            if ($key) {
                $response->set($key, $value);
            }
        }

        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Log
     */
    private function createLogEntry($request, $response)
    {
        $logEntry = new Log();
        $logEntry
            ->setRequest($request)
            ->setResponse($response);

        return $logEntry;
    }

    /**
     * From: https://wpbackoffice.com/get-current-woocommerce-version-number/
     *
     * @return string|null
     */
    private function getWooCommerceVersionNumber()
    {
        // If get_plugins() isn't available, require it
        if ( ! function_exists( 'get_plugins' ) )
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        // Create the plugins folder and file variables
        $plugin_folder = get_plugins( '/' . 'woocommerce' );
        $plugin_file = 'woocommerce.php';

        // If the plugin version number is set, return it
        if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
            return $plugin_folder[$plugin_file]['Version'];
        }

        return null;
    }
}