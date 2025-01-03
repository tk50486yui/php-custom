<?php
require_once '../app/Core/MyController.php';

class HomeController extends MyController
{

    public function __construct($response)
    {
        parent::__construct($response);
    }

    public function index(Request $request)
    {
        $params['items'][] = [
            'id' => 1,
            'name' => 'John Doe',
        ];

        $params['items'][] = [
            'id' => 2,
            'name' => 'Alice Smith',
        ];

        $params['message'] = 'Hello World!';

        $this->view('home/index', $params);
    }

}
