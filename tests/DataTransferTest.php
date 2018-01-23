<?php

use PHPUnit\Framework\TestCase;

final class DataTransferTest extends TestCase
{
    public function testGetAndSet()
    {
        $dataTransfer = new \Payone\Payone\Api\DataTransfer();

        $dataTransfer->set('test', '1234');
        $dataTransfer->set('cardpan', '4111111111111111');
        $dataTransfer->set('iban', 'DE85123456782599100003');
        $dataTransfer->set('street', 'Hauptstrasse 1');

        $this->assertEquals('1234', $dataTransfer->get('test'));
        $this->assertEquals('4111111111111111', $dataTransfer->get('cardpan'));
        $this->assertEquals('DE85123456782599100003', $dataTransfer->get('iban'));
        $this->assertEquals('Hauptstrasse 1', $dataTransfer->get('street'));
    }

    public function testAnonymization()
    {
        $dataTransfer = new \Payone\Payone\Api\DataTransfer();

        $dataTransfer->set('test', '1234');
        $dataTransfer->set('cardpan', '4111111111111111');
        $dataTransfer->set('iban', 'DE85123456782599100003');
        $dataTransfer->set('street', 'Hauptstrasse 1');

        $dataTransfer->anonymizeParameters();

        $this->assertEquals('1234', $dataTransfer->get('test'));
        $this->assertEquals('4111xxxxxxxx1111', $dataTransfer->get('cardpan'));
        $this->assertEquals('DE85xxxxxxxxxxxxxxx003', $dataTransfer->get('iban'));
        $this->assertEquals('Hxxxxxxxxxxxx1', $dataTransfer->get('street'));
    }
}
