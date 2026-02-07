<?php

namespace App\Models;

use App\Model;

class Category extends Model
{
    protected $table = 'categories';

    /**
     * Alle categorieën
     */
    public function all()
    {
        $sql = 'SELECT * FROM ' . $this->table . ' ORDER BY name ASC';
        return $this->db->select($sql, []);
    }

    /**
     * Alle categorieën van gebruiker
     */
    public function user_categories($user_id = null)
    {
        return $this->all();
    }
}
