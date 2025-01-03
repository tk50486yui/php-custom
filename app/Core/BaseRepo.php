<?php

class BaseRepo
{
    protected $conn;
    protected $model;

    public function __construct($model)
    {
        $this->conn = Database::getConnection();
        $this->model = $model;
    }

    public function begin()
    {
        $this->conn->begin_transaction();
    }

    public function commit()
    {
        $this->conn->commit();
    }

    public function rollback()
    {
        $this->conn->rollback();
    }

    public function store($data)
    {
        return $this->model->insert($data);
    }

    public function update($data, $id)
    {
        return $this->model->update($data, $id);
    }

    public function delete($id = null, $conditions = [])
    {
        return $this->model->delete($id, $conditions);
    }

    /**
     * $sql SQL查詢語法
     * $params 若不帶參數，則直接用 fetch_assoc 方法
     * $types  s i d b 若 $params 有三個參數，前兩個字串，第三個是整數，則 $types 可以傳 ssi
     */
    protected function executeQuery($sql, $params = [], $types = "")
    {
        if (empty($params)) {
            $result = $this->conn->query($sql);

            if ($result === false) {
                throw new Exception("Query failed: " . $this->conn->error);
            }

            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }

            $result->free();

            return $data;
        } else {
            $stmt = $this->conn->prepare($sql);

            if ($stmt === false) {
                throw new Exception("Prepare failed: " . $this->conn->error);
                return [];
            }

            if (!empty($params)) {
                if ($types === "") {
                    $types = str_repeat("s", count($params));
                }
                $stmt->bind_param($types, ...$params);
            }

            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
                return [];
            }

            $meta = $stmt->result_metadata();
            if ($meta === false) {
                throw new Exception("Result metadata failed: " . $stmt->error);
                return [];
            }

            $fields = $meta->fetch_fields();
            $row = [];
            $result = [];

            foreach ($fields as $field) {
                $row[$field->name] = null;
                $result[] = &$row[$field->name];
            }

            call_user_func_array([$stmt, 'bind_result'], $result);

            $data = [];
            while ($stmt->fetch()) {
                $data[] = array_map(function ($val) {
                    return $val;
                }, $row);
            }

            $stmt->close();

            return $data;
        }
    }
}
