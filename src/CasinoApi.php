<?php

namespace SdkGis;

use PDO;

/**
 * Class CasinoApi
 * @package SdkGis
 */
class CasinoApi
{

    /**
     * @var PDO
     */
    private $db;

    /**
     * GisApi constructor.
     */
    public function __construct()
    {
        $dbConfig = include(__DIR__ . '/config/db.php');
        $this->db = new PDO($dbConfig['dsn'], $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
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
        echo json_encode($errorData);
    }


    /**
     * Actual player's balance.
     * JSON response.
     * @param $request
     */
    private function balance($request)
    {
        header('Content-type: application/json; charset=UTF-8');
        $data = [
            'balance' => '55.55',
        ];
        echo json_encode($data);
    }

    /**
     * Player makes a bet.
     * JSON response.
     * @param $request
     */
    private function bet($request)
    {
        header('Content-type: application/json; charset=UTF-8');
        $data = [
            'balance' => '54.55',
            'transaction_id' => '1',
        ];
        echo json_encode($data);
    }

    /**
     * Player wins.
     * JSON response.
     * @param $request
     */
    private function win($request)
    {
        header('Content-type: application/json; charset=UTF-8');
        $data = [
            'balance' => '56.55',
            'transaction_id' => '2',
        ];
        echo json_encode($data);
    }

    /**
     * Refund is a cash back in case bet problems.
     * JSON response.
     * @param $request
     */
    private function refund($request)
    {
        header('Content-type: application/json; charset=UTF-8');
        $data = [
            'balance' => '55.55',
            'transaction_id' => '3',
        ];
        echo json_encode($data);
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
