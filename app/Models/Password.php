<?php

namespace App\Models;

use App\Model;

class Password extends Model
{
    protected $table = 'passwords';

    /**
     * Alle wachtwoorden van gebruiker
     */
    public function user_passwords($user_id)
    {
        $sql = 'SELECT id, title, username, website_url, tags, created_at FROM ' . $this->table . 
               ' WHERE user_id = :user_id ORDER BY title ASC';
        
        return $this->db->select($sql, ['user_id' => $user_id]);
    }

    /**
     * Vind wachtwoord met ID
     */
    public function find_for_user($id, $user_id)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE id = :id AND user_id = :user_id';
        return $this->db->selectOne($sql, ['id' => $id, 'user_id' => $user_id]);
    }

    /**
     * Zoeken naar wachtwoord
     */
    public function search($user_id, $query)
    {
        $search = '%' . $query . '%';
        $sql = 'SELECT id, title, username, website_url, tags FROM ' . $this->table . 
               ' WHERE user_id = :user_id AND (title LIKE :search OR username LIKE :search OR website_url LIKE :search)' .
               ' ORDER BY title ASC';
        
        return $this->db->select($sql, ['user_id' => $user_id, 'search' => $search]);
    }
}
