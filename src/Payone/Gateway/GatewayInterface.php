<?php

namespace Payone\Gateway;

defined('ABSPATH') or die('Direct access not allowed');

interface GatewayInterface
{
    public function add($methods);
}