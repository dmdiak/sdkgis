<?php

namespace SdkGis\Interfaces;

interface IClient
{

    public function balance($request);

    public function bet($request);

    public function win($request);

    public function refund($request);

}
