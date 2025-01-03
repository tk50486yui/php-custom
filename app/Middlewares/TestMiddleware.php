<?php

class TestMiddleware
{
    protected $response;

    public function __construct($response)
    {
        $this->response = $response;
    }

    public function handle($request)
    {
        $token = $request->bearerToken();
        $token2 = $request->query('token');

        /*if (empty($token) || $token != '123456') {
        $this->response->no('Failed');
        }*/

        if (empty($token2) || $token2 != '123456') {
            $this->response->no('Failed');
        }

        return $request;
    }
}
