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
     * Process request from GIS
     */
    public function processRequest()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        if ($requestMethod === 'POST') {

            $contentType = $_SERVER['CONTENT_TYPE'];
            if ($contentType === 'application/x-www-form-urlencoded') {

                $request = $_REQUEST;
                switch ($request['action']) {

                    case 'balance':
                        //$this->balance($request);
                        break;

                    case 'bet':
                        //$this->bet($request);
                        break;

                    case 'win':
                        //$this->win($request);
                        break;

                    case 'refund':
                        //$this->refund($request);
                        break;

                    default:
                        $errorCode = 'INTERNAL_ERROR';
                        $errorDescription = '';
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
