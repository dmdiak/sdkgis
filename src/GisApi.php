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
        $integrationData = $this->config['integrationData'];

        $authHeaders = [
            'X-Merchant-Id' => $integrationData['merchantId'],
            'X-Timestamp'   => time(),
            'X-Nonce'       => md5(uniqid(mt_rand(), true)),
        ];

        $mergedParams = array_merge($requestParams, $authHeaders);

        ksort($mergedParams);
        $hashString = http_build_query($mergedParams);

        $authHeaders['X-Sign'] = hash_hmac('sha1', $hashString, $integrationData['merchantKey']);

        return $authHeaders;
    }

    /**
     * GIS API
     * Retrieving games list.
     * Method: /games
     * @return array
     */
    public function getGames()
    {
        $authHeaders = $this->getAuthHeaders();

        $integrationData = $this->config['integrationData'];
        $gisApiOpt = $this->config['gisApiOpt'];

        $curl = curl_init($integrationData['baseApiUrl'] . '/games');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $gisApiOpt['connectTimeout']);
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

    /**
     * GIS API
     * Returns list of tables for the selected game.
     * Method: /games/lobby
     * @param string $gameUuid
     * @param string $currency
     * @return array
     */
    public function getLobbies($gameUuid, $currency)
    {
        $requestParams = [
            'game_uuid' => $gameUuid,
            'currency' => $currency,
        ];
        $requestParamsStr = http_build_query($requestParams);

        $authHeaders = $this->getAuthHeaders($requestParams);

        $integrationData = $this->config['integrationData'];
        $gisApiOpt = $this->config['gisApiOpt'];

        $curl = curl_init($integrationData['baseApiUrl'] . '/games/lobby?' . $requestParamsStr);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $gisApiOpt['connectTimeout']);
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

    /**
     * GIS API
     * Initializing game session.
     * Method: games/init
     * @param string $gameUuid
     * @param string $playerId
     * @param string $playerName
     * @param string $currency
     * @param string $sessionId
     * @param null|string $returnUrl [optional]
     * @param null|string $language [optional]
     * @param null|string $email [optional]
     * @param null|string $lobbyData [optional]
     * @return array
     */
    public function initGame(
        $gameUuid,
        $playerId,
        $playerName,
        $currency,
        $sessionId,
        $returnUrl = null,
        $language = null,
        $email = null,
        $lobbyData = null
    ) {
        $requestParams = [
            'game_uuid' => $gameUuid,
            'player_id' => $playerId,
            'player_name' => $playerName,
            'currency' => $currency,
            'session_id' => $sessionId,
            'return_url' => $returnUrl,
            'language' => $language,
            'email' => $email,
            'lobby_data' => $lobbyData,
        ];

        $authHeaders = $this->getAuthHeaders($requestParams);

        $integrationData = $this->config['integrationData'];
        $gisApiOpt = $this->config['gisApiOpt'];

        $curl = curl_init($integrationData['baseApiUrl'] . '/games/init');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($requestParams));
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $gisApiOpt['connectTimeout']);
        curl_setopt($curl, CURLOPT_TIMEOUT, $gisApiOpt['timeout']);

        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
        ];
        foreach ($authHeaders as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $json = curl_exec($curl);
        $result = json_decode($json, true);

        return $result;
    }

    /**
     * GIS API
     * Initializing demo game session (only if provider has demo mode).
     * Method: games/init-demo
     * @param string $gameUuid
     * @param null|string $returnUrl [optional]
     * @param null|string $language [optional]
     * @return array
     */
    public function initDemoGame($gameUuid, $returnUrl = null, $language = null)
    {
        $requestParams = [
            'game_uuid' => $gameUuid,
            'return_url' => $returnUrl,
            'language' => $language,
        ];

        $authHeaders = $this->getAuthHeaders($requestParams);

        $integrationData = $this->config['integrationData'];
        $gisApiOpt = $this->config['gisApiOpt'];

        $curl = curl_init($integrationData['baseApiUrl'] . '/games/init-demo');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($requestParams));
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $gisApiOpt['connectTimeout']);
        curl_setopt($curl, CURLOPT_TIMEOUT, $gisApiOpt['timeout']);

        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
        ];
        foreach ($authHeaders as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $json = curl_exec($curl);
        $result = json_decode($json, true);

        return $result;
    }

    /**
     * GIS API
     * Returns list of limits for merchant.
     * Method: /limits
     * @return array
     */
    public function getLimits()
    {
        $authHeaders = $this->getAuthHeaders();

        $integrationData = $this->config['integrationData'];
        $gisApiOpt = $this->config['gisApiOpt'];

        $curl = curl_init($integrationData['baseApiUrl'] . '/limits');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $gisApiOpt['connectTimeout']);
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

    /**
     * GIS API
     * Returns list of jackpots assigned to merchant key.
     * Method: /jackpots
     * @return array
     */
    public function getJackpots()
    {
        $authHeaders = $this->getAuthHeaders();

        $integrationData = $this->config['integrationData'];
        $gisApiOpt = $this->config['gisApiOpt'];

        $curl = curl_init($integrationData['baseApiUrl'] . '/jackpots');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $gisApiOpt['connectTimeout']);
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

}
