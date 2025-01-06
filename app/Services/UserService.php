<?php
require_once '../app/Repositories/UserRepo.php';
require_once '../app/Core/MyService.php';

class UserService extends MyService
{
    protected $response;
    protected $userRepo;

    public function __construct($response)
    {
        $this->response = $response;
        $this->userRepo = new UserRepo();
    }

    public function find($id)
    {
        return $this->userRepo->find($id);
    }

    public function findAll()
    {
        return $this->userRepo->findAll();
    }

    public function store($data)
    {
        try {
            $this->userRepo->begin();
            $user = $this->userRepo->store($data);
            $this->userRepo->commit();
        } catch (Exception $e) {
            $this->userRepo->rollback();
            $this->response->error($e->getMessage(), 500);
        }

        return $user;
    }

    public function update($data, $id)
    {
        return $this->userRepo->update($data, $id);
    }

}
