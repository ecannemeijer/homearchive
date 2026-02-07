<?php

namespace App;

use PDO;
use PDOException;

class Database
{
    private $connection;

    public function __construct()
    {
        $config = config('db');
        
        if (!$config) {
            die('Database configuratie niet gevonden. Zorg ervoor dat .env bestand correct is ingesteld.');
        }
        
        try {
            $this->connection = new PDO(
                'mysql:host=' . (!empty($config['host']) ? $config['host'] : 'localhost') . 
                ';port=' . (!empty($config['port']) ? $config['port'] : 3306) . 
                ';dbname=' . (!empty($config['database']) ? $config['database'] : 'abonnementen') . 
                ';charset=utf8mb4',
                !empty($config['username']) ? $config['username'] : 'root',
                $config['password'] ?? '',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            die('Database connectie mislukt: ' . $e->getMessage());
        }
    }

    /**
     * Query uitvoeren
     */
    public function query($sql, $params = [])
    {
        $stmt = $this->connection->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        if (!$stmt->execute()) {
            throw new PDOException('Query error: ' . implode(' ', $stmt->errorInfo()));
        }
        
        return $stmt;
    }

    /**
     * Select query
     */
    public function select($sql, $params = [])
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Select één rij
     */
    public function selectOne($sql, $params = [])
    {
        return $this->query($sql, $params)->fetch();
    }

    /**
     * Insert query
     */
    public function insert($table, $data)
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ':' . $col, $columns);
        
        $sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $columns) . ') ' .
               'VALUES (' . implode(', ', $placeholders) . ')';
        
        $this->query($sql, $data);
        return $this->connection->lastInsertId();
    }

    /**
     * Update query
     */
    public function update($table, $data, $where = '', $whereParams = [])
    {
        $sets = array_map(fn($col) => $col . ' = :' . $col, array_keys($data));
        $sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $sets);
        
        if (!empty($where)) {
            $sql .= ' WHERE ' . $where;
        }
        
        $params = array_merge($data, $whereParams);
        return $this->query($sql, $params);
    }

    /**
     * Delete query
     */
    public function delete($table, $where = '', $whereParams = [])
    {
        $sql = 'DELETE FROM ' . $table;
        
        if (!empty($where)) {
            $sql .= ' WHERE ' . $where;
        }
        
        $stmt = $this->query($sql, $whereParams);
        return $stmt->rowCount();
    }

    /**
     * Aantal rijen
     */
    public function count($sql, $params = [])
    {
        $result = $this->selectOne('SELECT COUNT(*) as count FROM (' . $sql . ') as temp', $params);
        return $result['count'] ?? 0;
    }

    /**
     * Get PDO connectie
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
