<?php

namespace App\Models;

use App\Model;

class Document extends Model
{
    protected $table = 'documents';

    /**
     * Documenten van abonnement
     */
    public function subscription_documents($subscription_id)
    {
        $sql = 'SELECT * FROM ' . $this->table . 
               ' WHERE subscription_id = :subscription_id ORDER BY uploaded_at DESC';
        
        return $this->db->select($sql, ['subscription_id' => $subscription_id]);
    }

    /**
     * Vind document met user check
     */
    public function find_for_user($id, $user_id = null)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE id = :id';
        return $this->db->selectOne($sql, ['id' => $id]);
    }
}
