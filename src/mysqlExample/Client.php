<?php

namespace SdkGis\MysqlExample;

use PDO;
use SdkGis\Interfaces\IClient;
use SdkGis\Responses\BalanceResponse;
use SdkGis\Responses\BetResponse;
use SdkGis\Responses\WinResponse;
use SdkGis\Responses\RefundResponse;
use SdkGis\Responses\ErrorResponse;

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

    /**
     * Client constructor.
     */
    public function __construct()
    {
        try {

            $this->db = new PDO('mysql:host=127.0.0.1;dbname=casino', 'root', '');

        } catch (\Exception $e) {

            header('Content-type: application/json; charset=UTF-8');
            $errorData = [
                'error_code' => 'INTERNAL_ERROR',
                'error_description' => 'Client Side DB Error',
            ];
            echo json_encode($errorData);exit;

        }
    }

    /**
     * @param string $code
     * @param string $description
     * @return ErrorResponse
     */
    private function getDBError($code = 'INTERNAL_ERROR', $description = 'Client Side DB Error')
    {
        $error = new ErrorResponse();
        $error->setErrorCode($code)->setErrorDescription($description);

        return $error;
    }

    /**
     * @param array $request
     * @return BalanceResponse|ErrorResponse
     */
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
                $response = $this->getDBError();
            }

        } catch (\Exception $e) {

            $response = $this->getDBError();

        } finally {

            return $response;

        }

    }

    /**
     * @param array $request
     * @return BetResponse|ErrorResponse
     */
    public function bet($request)
    {
        try {

            $query = 'SELECT id, COUNT(*) AS counter FROM casino.transactions WHERE transaction_id = :transaction_id';
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

                $transactionId = $result['id'];

                $query = 'SELECT amount FROM casino.balances WHERE player_id = :player_id AND currency = :currency';
                $stmt = $this->db->prepare($query);
                $stmt->execute([
                    'player_id' => $request['player_id'],
                    'currency' => $request['currency'],
                ]);

                $balanceAmount = $stmt->fetch()['amount'];

                $response = new BetResponse();
                $response->setBalance($balanceAmount)->setTransactionId($transactionId);

            }

        } catch (\Exception $e) {

            $response = $this->getDBError();

        } finally {

            return $response;

        }
    }

    /**
     * @param array $request
     * @return ErrorResponse|WinResponse
     */
    public function win($request)
    {
        try {

            $query = 'SELECT id, COUNT(*) AS counter FROM casino.transactions WHERE transaction_id = :transaction_id';
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

                $transactionId = $result['id'];

                $query = 'SELECT amount FROM casino.balances WHERE player_id = :player_id AND currency = :currency';
                $stmt = $this->db->prepare($query);
                $stmt->execute([
                    'player_id' => $request['player_id'],
                    'currency' => $request['currency'],
                ]);

                $balanceAmount = $stmt->fetch()['amount'];

                $response = new WinResponse();
                $response->setBalance($balanceAmount)->setTransactionId($transactionId);

            }

        } catch (\Exception $e) {

            $response = $this->getDBError();

        } finally {

            return $response;

        }
    }

    /**
     * @param array $request
     * @return ErrorResponse|RefundResponse
     */
    public function refund($request)
    {
        try {

            $query = 'SELECT id, COUNT(*) AS counter FROM casino.transactions
                      WHERE transaction_id = :transaction_id OR bet_transaction_id = :bet_transaction_id';
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'transaction_id' => $request['transaction_id'],
                'bet_transaction_id' => $request['bet_transaction_id'],
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
                    'action' => 'refund',
                    'amount' => $request['amount'],
                    'currency' => $request['currency'],
                    'bet_transaction_id' => $request['bet_transaction_id'],
                ]);

                $transactionId = $this->db->lastInsertId();

                $response = new RefundResponse();
                $response->setBalance($balanceAmount)->setTransactionId($transactionId);

            } else {

                $transactionId = $result['id'];

                $query = 'SELECT amount FROM casino.balances WHERE player_id = :player_id AND currency = :currency';
                $stmt = $this->db->prepare($query);
                $stmt->execute([
                    'player_id' => $request['player_id'],
                    'currency' => $request['currency'],
                ]);

                $balanceAmount = $stmt->fetch()['amount'];

                $response = new RefundResponse();
                $response->setBalance($balanceAmount)->setTransactionId($transactionId);

            }

        } catch (\Exception $e) {

            $response = $this->getDBError();

        } finally {

            return $response;

        }
    }

}
