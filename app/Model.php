<?php

namespace App;

class Model
{
    protected $db;
    protected $table;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Alle records
     */
    public function all()
    {
        $sql = 'SELECT * FROM ' . $this->table;
        return $this->db->select($sql);
    }

    /**
     * Record met ID
     */
    public function find($id)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE id = :id';
        return $this->db->selectOne($sql, ['id' => $id]);
    }

    /**
     * Eerste record matching criteria
     */
    public function findWhere($column, $value)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE ' . $column . ' = :value LIMIT 1';
        return $this->db->selectOne($sql, ['value' => $value]);
    }

    /**
     * Alle records matching criteria
     */
    public function where($column, $value)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE ' . $column . ' = :value';
        return $this->db->select($sql, ['value' => $value]);
    }

    /**
     * Insert
     */
    public function create($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update
     */
    public function update($id, $data)
    {
        return $this->db->update($this->table, $data, 'id = :id', ['id' => $id]);
    }

    /**
     * Delete
     */
    public function delete($id)
    {
        return $this->db->delete($this->table, 'id = :id', ['id' => $id]);
    }

    /**
     * Database instance
     */
    public function db()
    {
        return $this->db;
    }
}
