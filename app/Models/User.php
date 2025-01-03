<?php
require_once '../app/Core/MyModel.php';

class User extends MyModel
{
    protected $table = 'user';
    protected $primaryKey = 'id';

    public function __construct()
    {
        $conn = Database::getConnection();
        parent::__construct($conn);
    }
}
