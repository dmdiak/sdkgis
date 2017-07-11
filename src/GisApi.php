<?php

namespace SdkGis;

/**
 * Class GisApi
 * @package SdkGis
 */
class GisApi
{

    /**
     * @var array
     */
    private $config;

    /**
     * GisApi constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Authorization headers calculation.
     * @param array $requestParams
     * @return array
     */
    private function getAuthHeaders($requestParams = [])
    {
        $integrationData = $this->config['integration_data'];

        $authHeaders = [
            'X-Merchant-Id' => $integrationData['merchant_id'],
            'X-Timestamp'   => time(),
            'X-Nonce'       => md5(uniqid(mt_rand(), true)),
        ];

        $mergedParams = array_merge($requestParams, $authHeaders);

        ksort($mergedParams);
        $hashString = http_build_query($mergedParams);

        $authHeaders['X-Sign'] = hash_hmac('sha1', $hashString, $integrationData['merchant_key']);

        return $authHeaders;
    }

    /**
     * GIS API
     * method: /games
     * @return array
     */
    public function getGames()
    {
        $authHeaders = $this->getAuthHeaders();

        $integrationData = $this->config['integration_data'];
        $gisApiOpt = $this->config['gis_api_opt'];

        $curl = curl_init($integrationData['base_api_url'] . '/games');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $gisApiOpt['connect_timeout']);
        curl_setopt($curl, CURLOPT_TIMEOUT, $gisApiOpt['timeout']);

        $headers = [];
        foreach ($authHeaders as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $json = curl_exec($curl);
        $result = json_decode($json, true);

        return $result;
    }

    public function initGame()
    {

    }

}
