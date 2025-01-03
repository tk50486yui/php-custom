<?php

class Response
{
    protected $statusCode = 200;
    protected $headers = [
        'Content-Type' => 'application/json',
    ];

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function json($data, $statusCode = null)
    {
        if ($statusCode !== null) {
            $this->setStatusCode($statusCode);
        }
        $this->sendHeaders();
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function ok($message = '', $result = [])
    {
        $response = [
            'success' => true,
            'message' => $message,
            'result' => $result,
        ];

        $this->json($response, 200);
    }

    public function no($message = '', $statusCode = 500, $result = null)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($result != null) {
            $response['result'] = $result;
        }

        $this->json($response, $statusCode);
    }

    public function error($message = '', $statusCode = 500, $result = null)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($result != null) {
            $response['result'] = $result;
        }

        $this->json($response, $statusCode);
    }

    protected function sendHeaders()
    {
        http_response_code($this->statusCode);
        foreach ($this->headers as $key => $value) {
            header("{$key}: {$value}");
        }
    }
}
