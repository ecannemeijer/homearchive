<?php

namespace App\Models;

use App\Model;

class Password extends Model
{
    protected $table = 'passwords';

    /**
     * Alle wachtwoorden van gebruiker
     */
    public function user_passwords($user_id = null)
    {
        $sql = 'SELECT id, title, username, website_url, tags, created_at FROM ' . $this->table . 
               ' ORDER BY title ASC';
        
        return $this->db->select($sql, []);
    }

    /**
     * Vind wachtwoord met ID
     */
    public function find_for_user($id, $user_id = null)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE id = :id';
        return $this->db->selectOne($sql, ['id' => $id]);
    }

    /**
     * Zoeken naar wachtwoord
     */
    public function search($user_id = null, $query)
    {
        $search = '%' . $query . '%';
        $sql = 'SELECT id, title, username, website_url, tags FROM ' . $this->table . 
               ' WHERE (title LIKE :search OR username LIKE :search OR website_url LIKE :search)' .
               ' ORDER BY title ASC';
        
        return $this->db->select($sql, ['search' => $search]);
    }
}
