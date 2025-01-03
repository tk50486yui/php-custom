<?php

class BaseModel
{
    protected $table;
    protected $primaryKey = 'id'; // 預設主鍵名稱
    protected $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function insert($data)
    {
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $sql = "INSERT INTO `{$this->table}` ({$columns}) VALUES ({$placeholders})";

        $params = [];
        $types = '';

        foreach ($data as $value) {
            $params[] = $value;

            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }

        $stmt = mysqli_prepare($this->conn, $sql);

        if ($stmt === false) {
            throw new Exception('Prepare failed: ' . htmlspecialchars(mysqli_error($this->conn)));
        }

        // 動態綁定參數
        $bind_names = [];
        $bind_names[] =  & $types;
        for ($i = 0; $i < count($params); $i++) {
            $bind_names[] =  & $params[$i];
        }

        call_user_func_array([$stmt, 'bind_param'], $bind_names);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Execute failed: ' . htmlspecialchars(mysqli_stmt_error($stmt)));
        }

        $insert_id = mysqli_insert_id($this->conn);

        mysqli_stmt_close($stmt);

        return $this->find($insert_id);
    }

    public function update($data, $id)
    {
        $sql = "UPDATE `{$this->table}` SET ";
        $params = [];
        $types = '';

        foreach ($data as $key => $value) {
            $sql .= "`$key` = ?, ";
            $params[] = $value;

            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }

        // 移除末尾的逗號和空格，並添加 WHERE 子句
        $sql = rtrim($sql, ', ') . " WHERE `{$this->primaryKey}` = ?";
        $params[] = $id;
        $types .= 'i';

        $stmt = mysqli_prepare($this->conn, $sql);

        if ($stmt === false) {
            throw new Exception('Prepare failed: ' . htmlspecialchars(mysqli_error($this->conn)));
        }

        // 動態綁定參數
        $bind_names = [];
        $bind_names[] =  & $types;
        for ($i = 0; $i < count($params); $i++) {
            $bind_names[] =  & $params[$i];
        }

        call_user_func_array([$stmt, 'bind_param'], $bind_names);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Execute failed: ' . htmlspecialchars(mysqli_stmt_error($stmt)));
        }

        $affected_rows = mysqli_stmt_affected_rows($stmt);

        mysqli_stmt_close($stmt);

        if ($affected_rows >= 0) {
            return $this->find($id);
        }

        // 如果更新失敗，返回 false
        return false;
    }

    public function find($id)
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?";
        $stmt = mysqli_prepare($this->conn, $sql);

        if ($stmt === false) {
            throw new Exception('Prepare failed: ' . htmlspecialchars(mysqli_error($this->conn)));
        }

        mysqli_stmt_bind_param($stmt, 'i', $id);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Execute failed: ' . htmlspecialchars(mysqli_stmt_error($stmt)));
        }

        $meta = $stmt->result_metadata();
        $fields = $meta->fetch_fields();
        $result = [];

        $row = [];
        foreach ($fields as $field) {
            $row[$field->name] = null;
            $result[] = &$row[$field->name];
        }

        call_user_func_array([$stmt, 'bind_result'], $result);

        if (!mysqli_stmt_fetch($stmt)) {
            mysqli_stmt_close($stmt);
            return [];
        }

        mysqli_stmt_close($stmt);

        return $row;
    }

    public function delete($id = null, $conditions = [])
    {
        $sql = "DELETE FROM `{$this->table}` WHERE ";
        $params = [];
        $types = '';

        // 如果提供了 ID，則根據主鍵刪除
        if ($id !== null) {
            $sql .= "`{$this->primaryKey}` = ?";
            $params[] = $id;
            $types .= 'i'; // 假設主鍵是整數，根據實際情況調整
        }
        // 否則根據指定的條件刪除
        else {
            $clauses = [];
            foreach ($conditions as $key => $value) {
                $clauses[] = "`$key` = ?";
                $params[] = $value;

                if (is_int($value)) {
                    $types .= 'i';
                } elseif (is_float($value)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
            }
            $sql .= implode(' AND ', $clauses);
        }

        $stmt = mysqli_prepare($this->conn, $sql);

        if ($stmt === false) {
            throw new Exception('Prepare failed: ' . htmlspecialchars(mysqli_error($this->conn)));
        }

        // 動態綁定參數
        $bind_names = [];
        $bind_names[] =  & $types;
        for ($i = 0; $i < count($params); $i++) {
            $bind_names[] =  & $params[$i];
        }

        call_user_func_array([$stmt, 'bind_param'], $bind_names);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Execute failed: ' . htmlspecialchars(mysqli_stmt_error($stmt)));
        }

        $affected_rows = mysqli_stmt_affected_rows($stmt);

        mysqli_stmt_close($stmt);

        return $affected_rows;
    }
}
