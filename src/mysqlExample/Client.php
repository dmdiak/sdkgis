<?php

namespace SdkGis;

class Client implements IClient
{

    public function balance($request)
    {
        $data = [
            'balance' => '55.55',
        ];

        return $data;
    }

    public function bet($request)
    {
        $data = [
            'balance' => '54.55',
            'transaction_id' => '1',
        ];

        return $data;
    }

    public function win($request)
    {
        $data = [
            'balance' => '56.55',
            'transaction_id' => '2',
        ];

        return $data;
    }

    public function refund($request)
    {
        $data = [
            'balance' => '55.55',
            'transaction_id' => '3',
        ];

        return $data;
    }

}