<?php
class Request
{
    private $query;
    private $body;
    private $text;
    private $files;
    private $headers;
    private $attributes = [];
    private $excludedFields = [];

    public function __construct()
    {
        $this->query = $_GET;
        $this->text = $this->parseRequestBody();
        $this->body = $this->parseRequestBody();
        $this->body = $this->autoTrimBody($this->body);
        $this->files = $this->normalizeFiles($_FILES);
        $this->headers = $this->getAllHeaders();
    }

    private function parseRequestBody()
    {
        $contentType = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
        $body = [];

        if (stripos($contentType, 'application/json') !== false) {
            $body = json_decode(file_get_contents('php://input'), true);
            if (!is_array($body)) {
                $body = [];
            }
        } elseif (stripos($contentType, 'application/x-www-form-urlencoded') !== false || stripos($contentType, 'multipart/form-data') !== false) {
            $body = $_POST;
        }

        return $body;
    }

    private function autoTrimBody($body)
    {
        if (is_array($body)) {
            foreach ($body as $key => $value) {
                if (!in_array($key, $this->excludedFields)) {
                    $body[$key] = is_string($value) ? trim($value) : $value;
                }
            }
        }
        return $body;
    }

    private function normalizeFiles($files)
    {
        $normalized = [];
        foreach ($files as $key => $file) {
            if (is_array($file['name'])) {
                foreach ($file['name'] as $index => $name) {
                    $normalized[$key][$index] = [
                        'name' => $file['name'][$index],
                        'type' => $file['type'][$index],
                        'tmp_name' => $file['tmp_name'][$index],
                        'error' => $file['error'][$index],
                        'size' => $file['size'][$index],
                    ];
                }
            } else {
                $normalized[$key] = $file;
            }
        }
        return $normalized;
    }

    private function getAllHeaders()
    {
        if (!function_exists('getallheaders')) {
            $headers = [];
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
            return $headers;
        }
        return getallheaders();
    }

    public function query($key, $default = null)
    {
        return isset($this->query[$key]) ? $this->query[$key] : $default;
    }

    public function body($key = null, $default = null)
    {
        if ($key === null) {
            return $this->body;
        }
        return isset($this->body[$key]) ? $this->body[$key] : $default;
    }

    public function text($key = null, $default = null)
    {
        if ($key === null) {
            return $this->text;
        }
        return isset($this->text[$key]) ? $this->text[$key] : $default;
    }

    public function files($key = null, $default = null)
    {
        if ($key === null) {
            return $this->files;
        }
        return isset($this->files[$key]) ? $this->files[$key] : $default;
    }

    public function headers($key = null, $default = null)
    {
        if ($key === null) {
            return $this->headers;
        }
        return isset($this->headers[$key]) ? $this->headers[$key] : $default;
    }

    public function bearerToken()
    {
        $authHeader = $this->headers('Authorization');
        if ($authHeader && strpos($authHeader, 'Bearer ') === 0) {
            return substr($authHeader, 7);
        }
        return null;
    }

    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function getAttribute($key)
    {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
    }

    public function all()
    {
        return [
            'query' => $this->query,
            'body' => $this->body,
            'files' => $this->files,
            'headers' => $this->headers,
        ];
    }
}
