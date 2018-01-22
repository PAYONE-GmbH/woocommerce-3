<?php

namespace Payone\Payone\Api;

class DataTransfer
{
    private $parameterBag;

    public function __construct()
    {
        $this->clear();
    }

    public static function constructFromJson($jsonData)
    {
        $dataTransfer = new DataTransfer();
        $dataTransfer->unserializeParameters($jsonData);

        return $dataTransfer;
    }

    public function clear()
    {
        $this->parameterBag = [];
    }

    /**
     * @todo Wenn ein $key erneut gesetzt wird, kann es auch sein, dass ein Array gespeichert wird.
     *
     * @param string $key
     * @param mixed $value
     *
     * @param DataTransfer
     */
    public function set($key, $value)
    {
        $this->parameterBag[$key] = $value;

        return $this;
    }

    public function get($key)
    {
        if (array_key_exists($key, $this->parameterBag)) {
            return $this->parameterBag[$key];
        }

        return null;
    }

    /**
     * @return string
     */
    public function getPostfieldsFromParameters()
    {
        return http_build_query($this->parameterBag);
    }

    public function getSerializedParameters()
    {
        return json_encode($this->parameterBag);
    }

    public function unserializeParameters($serialized)
    {
        $this->parameterBag = json_decode($serialized, true);
    }

    private function anonymize()
    {
        // @todo Alle persönlichen Daten anonymisieren
    }
}