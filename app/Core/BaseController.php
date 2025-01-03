<?php

class BaseController
{
    protected $response;

    public function __construct($response)
    {
        $this->response = $response;
    }

    protected function view($view, $data = [])
    {
        static $viewLoaded = false;
        if (!$viewLoaded) {
            $viewLoaded = true;
            $viewPath = "../views/$view.php";
            if (file_exists($viewPath)) {
                extract($data);
                include $viewPath;
                exit;
            } else {
                throw new Exception("View file not found: $viewPath");
            }
        } else {
            throw new Exception("View already loaded: $view");
        }
    }

}
