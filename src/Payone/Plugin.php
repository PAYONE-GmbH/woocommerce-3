<?php

namespace Payone;

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

class Plugin
{
    /**
     * @var \Payone\Admin\Settings
     */
    private $settings;

    /**
     * @var \Payone\Payone\Api\Request
     */
    private $request;

    public function __construct()
    {
        $this->settings = new \Payone\Admin\Settings();
        $this->settings->init();

        $this->request = new \Payone\Payone\Api\Request();
        $this->request
            ->setMode('test')
            ->setAccountId('39578')
            ->setMerchantId('37834')
            ->setPortalId('2027202')
            ->setPortalKey('k2OBcwb2J0YayrU3')
            ->set('amount', 10000)
            ->set('cardexpiredate', 2001)
            ->set('cardpan', '4111111111111111')
            ->set('cardtype', 'V')
            ->set('clearingtype', 'cc')
            ->set('country', 'DE')
            ->set('currency', 'EUR')
            ->set('customer_is_present', 'yes')
            ->set('ecommercemode', 'internet')
            ->set('firstname', 'Timo')
            ->set('lastname', 'Tester')
            ->set('reference', substr(md5(uniqid('ref', true)), 0, 20))
            ->set('request', 'preauthorization')
        ;

        #$this->request->execute();
    }
}