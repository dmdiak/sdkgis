<?php

namespace SdkGis;

use SdkGis\Responses\Response;
use SdkGis\Interfaces\IClient;

/**
 * Class CasinoApi
 * @package SdkGis
 */
class CasinoApi
{

    /**
     * @var IClient
     */
    private $client;

    /**
     * @var array
     */
    private $config;

    /**
     * CasinoApi constructor.
     * @param IClient $client
     */
    public function __construct(IClient $client)
    {
        $this->client = $client;
        $this->config = include(__DIR__ . '/config/config.php');
    }

    /**
     * Error JSON response.
     * @param string $errorCode
     * @param string $errorDescription
     */
    private function errorResponse($errorCode, $errorDescription)
    {
        header('Content-type: application/json; charset=UTF-8');
        $errorData = [
            'error_code' => $errorCode,
            'error_description' => $errorDescription,
        ];
        echo json_encode($errorData);exit;
    }

    /**
     * JSON response.
     * @param Response $response
     */
    private function response($response)
    {
        header('Content-type: application/json; charset=UTF-8');
        $data = get_object_vars($response);
        echo json_encode($data);exit;
    }

    /**
     * Actual player's balance.
     * JSON response.
     * @param array $request
     */
    private function balance($request)
    {
        $requiredFields = [
            'player_id',
            'currency',
        ];

        if (count(array_intersect_key(array_flip($requiredFields), $request)) === count($requiredFields)) {

            $response = $this->client->balance($request);
            $this->response($response);

        } else {

            $errorCode = 'INTERNAL_ERROR';
            $errorDescription = 'Required fields missing';
            $this->errorResponse($errorCode, $errorDescription);

        }

    }

    /**
     * Player makes a bet.
     * JSON response.
     * @param array $request
     */
    private function bet($request)
    {
        $requiredFields = [
            'amount',
            'currency',
            'game_uuid',
            'player_id',
            'transaction_id',
            'session_id',
            'type',
        ];

        if (count(array_intersect_key(array_flip($requiredFields), $request)) === count($requiredFields)) {

            $response = $this->client->bet($request);
            $this->response($response);

        } else {

            $errorCode = 'INTERNAL_ERROR';
            $errorDescription = 'Required fields missing';
            $this->errorResponse($errorCode, $errorDescription);

        }
    }

    /**
     * Player wins.
     * JSON response.
     * @param array $request
     */
    private function win($request)
    {
        $requiredFields = [
            'amount',
            'currency',
            'game_uuid',
            'player_id',
            'transaction_id',
            'session_id',
            'type',
        ];

        if (count(array_intersect_key(array_flip($requiredFields), $request)) === count($requiredFields)) {

            $response = $this->client->win($request);
            $this->response($response);

        } else {

            $errorCode = 'INTERNAL_ERROR';
            $errorDescription = 'Required fields missing';
            $this->errorResponse($errorCode, $errorDescription);

        }
    }

    /**
     * Refund is a cash back in case bet problems.
     * JSON response.
     * @param array $request
     */
    private function refund($request)
    {
        $requiredFields = [
            'amount',
            'currency',
            'game_uuid',
            'player_id',
            'transaction_id',
            'session_id',
            'bet_transaction_id',
        ];

        if (count(array_intersect_key(array_flip($requiredFields), $request)) === count($requiredFields)) {

            $response = $this->client->refund($request);
            $this->response($response);

        } else {

            $errorCode = 'INTERNAL_ERROR';
            $errorDescription = 'Required fields missing';
            $this->errorResponse($errorCode, $errorDescription);

        }
    }

    /**
     * Check Authorization headers.
     */
    private function checkAuthHeaders()
    {
        if (!isset($_SERVER['X-Merchant-Id'])) {
            $errorCode = 'INTERNAL_ERROR';
            $errorDescription = 'X-Merchant-Id header is missing';
            $this->errorResponse($errorCode, $errorDescription);
        } elseif (!isset($_SERVER['X-Timestamp'])) {
            $errorCode = 'INTERNAL_ERROR';
            $errorDescription = 'X-Timestamp header is missing';
            $this->errorResponse($errorCode, $errorDescription);
        } elseif (!isset($_SERVER['X-Nonce'])) {
            $errorCode = 'INTERNAL_ERROR';
            $errorDescription = 'X-Nonce header is missing';
            $this->errorResponse($errorCode, $errorDescription);
        } elseif (!isset($_SERVER['X-Sign'])) {
            $errorCode = 'INTERNAL_ERROR';
            $errorDescription = 'X-Sign header is missing';
            $this->errorResponse($errorCode, $errorDescription);
        } elseif (preg_match('/\D+/', $_SERVER['X-Timestamp'])) {
            $errorCode = 'INTERNAL_ERROR';
            $errorDescription = 'X-Timestamp header isn\'t correct';
            $this->errorResponse($errorCode, $errorDescription);
        }

        $gisTime = strtotime($_SERVER['X-Timestamp']);
        $time = time();

        if (($gisTime > $time) || ($gisTime <= ($time - 30))) {
            $errorCode = 'INTERNAL_ERROR';
            $errorDescription = 'Request is expired';
            $this->errorResponse($errorCode, $errorDescription);
        }
    }

    /**
     * X-Sign validation.
     */
    private function checkXSign()
    {
        $merchantKey = $this->config['integrationData']['merchantKey'];

        $headers = [
            'X-Merchant-Id' => $_SERVER['X-Merchant-Id'],
            'X-Timestamp'   => $_SERVER['X-Timestamp'],
            'X-Nonce'       => $_SERVER['X-Nonce'],
        ];

        $xSign = $_SERVER['X-Sign'];

        $mergedParams = array_merge($_POST, $headers);
        ksort($mergedParams);
        $hashString = http_build_query($mergedParams);

        $expectedSign = hash_hmac('sha1', $hashString, $merchantKey);

        if ($xSign !== $expectedSign) {
            $errorCode = 'INTERNAL_ERROR';
            $errorDescription = 'X-Sign header is wrong';
            $this->errorResponse($errorCode, $errorDescription);
        }
    }

    /**
     * Process request from GIS
     */
    public function processRequest()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        if ($requestMethod === 'POST') {

            $contentType = $_SERVER['CONTENT_TYPE'];
            if ($contentType === 'application/x-www-form-urlencoded') {

                $this->checkAuthHeaders();
                $this->checkXSign();

                $request = $_REQUEST;
                $action = $request['action'];
                unset($request['action']);

                switch ($action) {

                    case 'balance':
                        $this->balance($request);
                        break;

                    case 'bet':
                        $this->bet($request);
                        break;

                    case 'win':
                        $this->win($request);
                        break;

                    case 'refund':
                        $this->refund($request);
                        break;

                    default:
                        $errorCode = 'INTERNAL_ERROR';
                        $errorDescription = 'Action ' . $action . ' not found';
                        $this->errorResponse($errorCode, $errorDescription);
                        break;

                }

            } else {

                $errorCode = 'INTERNAL_ERROR';
                $errorDescription = 'All calls from GIS to integrator will be passed with application/x-www-form-urlencoded content type';
                $this->errorResponse($errorCode, $errorDescription);

            }

        } else {

            $errorCode = 'INTERNAL_ERROR';
            $errorDescription = 'All calls from GIS to integrator will be done via POST';
            $this->errorResponse($errorCode, $errorDescription);

        }
    }

}
