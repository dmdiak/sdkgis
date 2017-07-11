<?php

namespace SdkGis;

/**
 * Class GisApiClient
 * @package SdkGis
 */
class GisApiClient
{

    /**
     * @var array
     */
    private $config;

    /**
     * GisApiClient constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
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
     *
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
        curl_setopt($curl, CURLOPT_HTTPHEADER, $authHeaders);

        $json = curl_exec($curl);
        $result = json_decode($json, true);

        print_r($result);
    }

}
