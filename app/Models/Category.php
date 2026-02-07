<?php

namespace App\Models;

use App\Model;

class Category extends Model
{
    protected $table = 'categories';

    /**
     * Alle categorieën van gebruiker
     */
    public function user_categories($user_id)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE user_id = :user_id ORDER BY name ASC';
        return $this->db->select($sql, ['user_id' => $user_id]);
    }

    /**
     * Vind categorie met user check
     */
    public function find_for_user($id, $user_id)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE id = :id AND user_id = :user_id';
        return $this->db->selectOne($sql, ['id' => $id, 'user_id' => $user_id]);
    }
}
