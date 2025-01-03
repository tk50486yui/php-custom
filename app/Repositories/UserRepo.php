<?php
require_once '../app/Models/User.php';
require_once '../app/Core/MyRepo.php';

class UserRepo extends MyRepo
{
    public function __construct()
    {
        parent::__construct(new User(Database::getConnection()));
    }

    public function find($id)
    {
        $sql = "SELECT
                    *
                FROM
                    user
                WHERE
                   id = ?";

        return $this->getFirst($this->executeQuery($sql, [$id]));
    }

    public function findAll()
    {
        $sql = "SELECT
                    *
                FROM
                    user
                ORDER BY
                    id ASC";

        return $this->toArray($this->executeQuery($sql));
    }

}
