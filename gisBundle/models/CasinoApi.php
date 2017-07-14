<?php

namespace GisBundle\Models;

use GisBundle\Exceptions\GisException;
use GisBundle\Exceptions\InternalErrorException;
use GisBundle\Interfaces\IPlayer;
use GisBundle\Responses\Response;

/**
 * Class CasinoApi
 * @package GisBundle\Models
 */
class CasinoApi
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var IPlayer;
     */
    protected $player;

    /**
     * @var array
     */
    protected $requiredPostFields = [
        'balance' => ['action', 'player_id', 'currency'],
        'bet' => ['action', 'amount', 'currency', 'game_uuid', 'player_id', 'transaction_id', 'session_id', 'type'],
        'win' => ['action', 'amount', 'currency', 'game_uuid', 'player_id', 'transaction_id', 'session_id', 'type'],
        'refund' => ['action', 'amount', 'currency', 'game_uuid', 'player_id', 'transaction_id', 'session_id', 'bet_transaction_id'],
    ];

    /**
     * CasinoApi constructor.
     */
    public function __construct()
    {
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
     * Check POST fields.
     * @throws GisException
     */
    protected function checkPostFields()
    {
        $required = $this->requiredPostFields;
        if (count(array_intersect_key(array_flip($required), $_REQUEST)) !== count($required)) {
            throw new InternalErrorException('Required POST fields are missing');
        }
    }

    /**
     * Check request headers.
     * @throws GisException
     */
    protected function checkHeaders()
    {
        if ($_SERVER['CONTENT_TYPE'] !== 'application/x-www-form-urlencoded') {
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
     * @throws GisException
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
     * Check request from GIS.
     * @throws GisException
     */
    public function checkRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new InternalErrorException('All calls from GIS to integrator will be done via POST');
        }
        $this->checkHeaders();
        $this->checkXSign();
        $this->checkPostFields();
    }

    /**
     * Process request from GIS.
     * @param IPlayer $player
     * @throws GisException
     */
    public function processRequest(IPlayer $player)
    {
        switch ($_REQUEST['action']) {
            case 'balance':
                $response = $player->getBalanceResponse();
                break;
            case 'bet':
                $response = $player->getBetResponse();
                break;
            case 'win':
                $response = $player->getWinResponse();
                break;
            case 'refund':
                $response = $player->getRefundResponse();
                break;
            default:
                throw new InternalErrorException('Action ' . $_REQUEST['action'] . ' not found');
        }
        $this->successResponse($response);
    }

}
