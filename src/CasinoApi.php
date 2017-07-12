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
     * CasinoApi constructor.
     * @param IClient $client
     */
    public function __construct(IClient $client)
    {
        $this->client = $client;
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
     * Success JSON response.
     * @param Response $response
     */
    private function successResponse($response)
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
            $this->successResponse($response);

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

            $data = $this->client->bet($request);
            $this->successResponse($data);

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

            $data = $this->client->win($request);
            $this->successResponse($data);

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

            $data = $this->client->refund($request);
            $this->successResponse($data);

        } else {

            $errorCode = 'INTERNAL_ERROR';
            $errorDescription = 'Required fields missing';
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
