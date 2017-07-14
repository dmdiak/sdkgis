<?php

namespace GisBundle\Models;

use GisBundle\Exceptions\InternalErrorException;
use GisBundle\Responses\Response;
use GisBundle\Interfaces\IClient;

/**
 * Class CasinoApi
 * @package GisBundle\Models
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
        $this->config = include(__DIR__ . '/../config/config.php');
    }

    /**
     * Error JSON response.
     * @param string $errorCode
     * @param string $errorDescription
     */
    public function errorResponse($errorCode, $errorDescription)
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
    protected function successResponse($response)
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
    protected function balance($request)
    {
        $requiredFields = ['player_id', 'currency'];
        $this->checkPostFields($request, $requiredFields);
        $response = $this->client->balance($request);
        $this->successResponse($response);
    }

    /**
     * Player makes a bet.
     * JSON response.
     * @param array $request
     */
    protected function bet($request)
    {
        $requiredFields = ['amount', 'currency', 'game_uuid', 'player_id', 'transaction_id', 'session_id', 'type'];
        $this->checkPostFields($request, $requiredFields);
        $response = $this->client->bet($request);
        $this->successResponse($response);
    }

    /**
     * Player wins.
     * JSON response.
     * @param array $request
     */
    protected function win($request)
    {
        $requiredFields = ['amount', 'currency', 'game_uuid', 'player_id', 'transaction_id', 'session_id', 'type'];
        $this->checkPostFields($request, $requiredFields);
        $response = $this->client->win($request);
        $this->successResponse($response);
    }

    /**
     * Refund is a cash back in case bet problems.
     * JSON response.
     * @param array $request
     */
    protected function refund($request)
    {
        $requiredFields = ['amount', 'currency', 'game_uuid', 'player_id', 'transaction_id', 'session_id', 'bet_transaction_id'];
        $this->checkPostFields($request, $requiredFields);
        $response = $this->client->refund($request);
        $this->successResponse($response);
    }

    /**
     * Check POST fields.
     * @param array $request
     * @param array $requiredFields
     * @throws InternalErrorException
     */
    protected function checkPostFields($request, $requiredFields)
    {
        if (count(array_intersect_key(array_flip($requiredFields), $request)) !== count($requiredFields)) {
            throw new InternalErrorException('Required POST fields are missing');
        }
    }

    /**
     * Check request headers and method.
     */
    protected function checkRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new InternalErrorException('All calls from GIS to integrator will be done via POST');
        } elseif ($_SERVER['CONTENT_TYPE'] !== 'application/x-www-form-urlencoded') {
            throw new InternalErrorException('All calls from GIS to integrator will be passed with application/x-www-form-urlencoded content type');
        }

        $requiredAuthHeaders = ['HTTP_X_MERCHANT_ID', 'HTTP_X_TIMESTAMP', 'HTTP_X_NONCE', 'HTTP_X_SIGN'];

        foreach ($requiredAuthHeaders as $headerName) {
            if (!isset($_SERVER[$headerName])) {
                $errMessage = preg_replace(['/HTTP\_/', '\_'], ['', '\-'], $headerName) . ' header is missing';
                throw new InternalErrorException($errMessage);
            }
        }

        if (preg_match('/\D+/', $_SERVER['HTTP_X_TIMESTAMP'])) {
            throw new InternalErrorException('X-Timestamp header isn\'t correct');
        }

        $gisTime = $_SERVER['HTTP_X_TIMESTAMP'];
        $time = time();

        if ($gisTime > $time) {
            throw new InternalErrorException('X-Timestamp header isn\'t correct');
        } elseif ($gisTime <= ($time - 30)) {
            throw new InternalErrorException('Request is expired');
        }
    }

    /**
     * X-Sign validation.
     */
    protected function checkXSign()
    {
        $merchantKey = $this->config['integrationData']['merchantKey'];

        $headers = [
            'X-Merchant-Id' => $_SERVER['HTTP_X_MERCHANT_ID'],
            'X-Timestamp'   => $_SERVER['HTTP_X_TIMESTAMP'],
            'X-Nonce'       => $_SERVER['HTTP_X_NONCE'],
        ];

        $xSign = $_SERVER['HTTP_X_SIGN'];

        $mergedParams = array_merge($_POST, $headers);
        ksort($mergedParams);
        $hashString = http_build_query($mergedParams);

        $expectedSign = hash_hmac('sha1', $hashString, $merchantKey);

        if ($xSign !== $expectedSign) {
            throw new InternalErrorException('X-Sign header is wrong');
        }
    }

    /**
     * Process request from GIS.
     */
    public function processRequest()
    {
        $this->checkRequest();
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
                throw new InternalErrorException('Action ' . $action . ' not found');
        }
    }

}
