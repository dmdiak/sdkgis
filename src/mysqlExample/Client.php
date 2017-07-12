<?php

namespace SdkGis\MysqlExample;

use PDO;
use SdkGis\Interfaces\IClient;
use SdkGis\Responses\BalanceResponse;
use SdkGis\Responses\BetResponse;
use SdkGis\Responses\WinResponse;
use SdkGis\Responses\RefundResponse;
use SdkGis\Responses\ExceptionResponse;

/**
 * Class Client
 * @package SdkGis\MysqlExample
 */
class Client implements IClient
{
    /**
     * @var PDO
     */
    private $db;

    public function __construct()
    {
        $this->db = new PDO('mysql:host=127.0.0.1;dbname=casino', 'root', '');
    }

    private function getDBException($code = 'INTERNAL_ERROR', $description = 'Client Side DB Exception')
    {
        $exception = new ExceptionResponse();
        $exception->setErrorCode($code)->setErrorDescription($description);

        return $exception;
    }

    public function balance($request)
    {

        try {

            $query = 'SELECT amount FROM casino.balances WHERE player_id = :player_id AND currency = :currency';
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'player_id' => $request['player_id'],
                'currency' => $request['currency'],
            ]);

            $result = $stmt->fetch();

            if (is_array($result)) {

                $response = new BalanceResponse();
                $response->setBalance($result['amount']);

            } else {
                $response = $this->getDBException();
            }

        } catch (\Exception $e) {

            $response = $this->getDBException();

        } finally {

            return $response;

        }

    }

    public function bet($request)
    {
        try {

            $query = 'SELECT COUNT(*) AS counter FROM casino.transactions WHERE transaction_id = :transaction_id';
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'transaction_id' => $request['transaction_id'],
            ]);

            $result = $stmt->fetch();

            if ($result['counter'] === '0') {

                $query = 'SELECT id, amount FROM casino.balances WHERE player_id = :player_id AND currency = :currency';
                $stmt = $this->db->prepare($query);
                $stmt->execute([
                    'player_id' => $request['player_id'],
                    'currency' => $request['currency'],
                ]);

                $balanceData = $stmt->fetch();
                $balanceId = $balanceData['id'];
                $balanceAmount = $balanceData['amount'] - $request['amount'];

                $query = 'UPDATE casino.balances SET amount = :amount WHERE id = :id';
                $stmt = $this->db->prepare($query);
                $stmt->execute([
                    'amount' => $balanceAmount,
                    'id' => $balanceId,
                ]);

                $query = 'INSERT INTO casino.transactions
                          (player_id, balance_id, game_uuid, session_id, transaction_id, action, amount, currency, type)
                          VALUES (:player_id, :balance_id, :game_uuid, :session_id, :transaction_id, :action, :amount, :currency, :type)';
                $stmt = $this->db->prepare($query);
                $stmt->execute([
                    'player_id' => $request['player_id'],
                    'balance_id' => $balanceId,
                    'game_uuid' => $request['game_uuid'],
                    'session_id' => $request['session_id'],
                    'transaction_id' => $request['transaction_id'],
                    'action' => 'bet',
                    'amount' => $request['amount'],
                    'currency' => $request['currency'],
                    'type' => $request['type'],
                ]);

                $transactionId = $this->db->lastInsertId();

                $response = new BetResponse();
                $response->setBalance($balanceAmount)->setTransactionId($transactionId);

            } else {
                $response = $this->getDBException('INTERNAL_ERROR', 'transaction_id exists');
            }

        } catch (\Exception $e) {

            $response = $this->getDBException();

        } finally {

            return $response;

        }
    }

    public function win($request)
    {
        try {

            $query = 'SELECT COUNT(*) AS counter FROM casino.transactions WHERE transaction_id = :transaction_id';
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'transaction_id' => $request['transaction_id'],
            ]);

            $result = $stmt->fetch();

            if ($result['counter'] === '0') {

                $query = 'SELECT id, amount FROM casino.balances WHERE player_id = :player_id AND currency = :currency';
                $stmt = $this->db->prepare($query);
                $stmt->execute([
                    'player_id' => $request['player_id'],
                    'currency' => $request['currency'],
                ]);

                $balanceData = $stmt->fetch();
                $balanceId = $balanceData['id'];
                $balanceAmount = $balanceData['amount'] + $request['amount'];

                $query = 'UPDATE casino.balances SET amount = :amount WHERE id = :id';
                $stmt = $this->db->prepare($query);
                $stmt->execute([
                    'amount' => $balanceAmount,
                    'id' => $balanceId,
                ]);

                $query = 'INSERT INTO casino.transactions
                          (player_id, balance_id, game_uuid, session_id, transaction_id, action, amount, currency, type)
                          VALUES (:player_id, :balance_id, :game_uuid, :session_id, :transaction_id, :action, :amount, :currency, :type)';
                $stmt = $this->db->prepare($query);
                $stmt->execute([
                    'player_id' => $request['player_id'],
                    'balance_id' => $balanceId,
                    'game_uuid' => $request['game_uuid'],
                    'session_id' => $request['session_id'],
                    'transaction_id' => $request['transaction_id'],
                    'action' => 'win',
                    'amount' => $request['amount'],
                    'currency' => $request['currency'],
                    'type' => $request['type'],
                ]);

                $transactionId = $this->db->lastInsertId();

                $response = new WinResponse();
                $response->setBalance($balanceAmount)->setTransactionId($transactionId);

            } else {
                $response = $this->getDBException('INTERNAL_ERROR', 'transaction_id exists');
            }

        } catch (\Exception $e) {

            $response = $this->getDBException();

        } finally {

            return $response;

        }
    }

    public function refund($request)
    {
        try {

            $response = new RefundResponse();
            $response->setBalance(55.55)->setTransactionId('3');

            return $response;

        } catch (\Exception $e) {
            $exception = $this->getDBException();
            return $exception;
        }
    }

}
