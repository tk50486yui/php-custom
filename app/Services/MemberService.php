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
        if (empty($data['email']) || empty($data['name'])) {
            $this->response->error('Failed', 422);
        }

        try {
            $this->memberRepo->begin();
            $member = $this->memberRepo->store($data);
            $this->memberRepo->commit();
        } catch (Exception $e) {
            $this->memberRepo->rollback();
            $this->response->error($e->getMessage(), 500);
        }

        return $member;
    }

    public function update($data, $id)
    {
        return $this->memberRepo->update($data, $id);
    }

}
