<?php

namespace App\Models;

use App\Model;

class Subscription extends Model
{
    protected $table = 'subscriptions';

    /**
     * Alle abonnementen van gebruiker
     */
    public function user_subscriptions($user_id = null, $status = 'all')
    {
        $sql = 'SELECT * FROM ' . $this->table;
        $params = [];
        
        if ($status !== 'all') {
            $is_active = ($status === 'active') ? 1 : 0;
            $sql .= ' WHERE is_active = :active';
            $params['active'] = $is_active;
        }
        
        $sql .= ' ORDER BY name ASC';
        
        return $this->db->select($sql, $params);
    }

    /**
     * Fil gering met search, sort, pagination
     */
    public function filtered($user_id = null, $filters = [])
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE 1=1';
        $params = [];
        
        // Filter op type
        if (!empty($filters['type'])) {
            $sql .= ' AND type = :type';
            $params['type'] = $filters['type'];
        }
        
        // Filter op frequentie
        if (!empty($filters['frequency'])) {
            $sql .= ' AND frequency = :frequency';
            $params['frequency'] = $filters['frequency'];
        }
        
        // Filter op status
        if (isset($filters['active'])) {
            $sql .= ' AND is_active = :active';
            $params['active'] = $filters['active'] ? 1 : 0;
        }
        
        // Filter op categorie
        if (!empty($filters['category'])) {
            $sql .= ' AND category = :category';
            $params['category'] = $filters['category'];
        }
        
        // Search
        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $sql .= ' AND (name LIKE :search OR notes LIKE :search OR website_url LIKE :search)';
            $params['search'] = $search;
        }
        
        // Sort
        $sort_by = $filters['sort_by'] ?? 'name';
        $sort_order = $filters['sort_order'] ?? 'ASC';
        $allowed_sorts = ['name', 'cost', 'end_date', 'created_at'];
        
        if (in_array($sort_by, $allowed_sorts)) {
            $sql .= ' ORDER BY ' . $sort_by . ' ' . strtoupper($sort_order);
        } else {
            $sql .= ' ORDER BY name ASC';
        }
        
        // Limit en offset
        $limit = (int)($filters['limit'] ?? 10);
        $offset = (int)($filters['offset'] ?? 0);
        $sql .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
        
        return $this->db->select($sql, $params);
    }

    /**
     * Count met filters
     */
    public function count_filtered($user_id = null, $filters = [])
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE 1=1';
        $params = [];
        
        if (!empty($filters['type'])) {
            $sql .= ' AND type = :type';
            $params['type'] = $filters['type'];
        }
        if (!empty($filters['frequency'])) {
            $sql .= ' AND frequency = :frequency';
            $params['frequency'] = $filters['frequency'];
        }
        if (isset($filters['active'])) {
            $sql .= ' AND is_active = :active';
            $params['active'] = $filters['active'] ? 1 : 0;
        }
        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $sql .= ' AND (name LIKE :search OR notes LIKE :search)';
            $params['search'] = $search;
        }
        
        return $this->db->count($sql, $params);
    }

    /**
     * Items die bijna verlopen zijn
     */
    public function expiring_soon($user_id = null, $days = 30)
    {
        $sql = 'SELECT * FROM ' . $this->table . 
               ' WHERE is_active = 1' .
               ' AND is_monthly_cancelable = 0' .
               ' AND end_date IS NOT NULL AND end_date != "0000-00-00"' .
               ' AND DATEDIFF(end_date, CURDATE()) BETWEEN 0 AND :days ' .
               ' ORDER BY end_date ASC';
        
        return $this->db->select($sql, ['days' => $days]);
    }

    /**
     * Totaal maandelijks kosten
     */
    public function total_monthly_cost($user_id = null, $include_yearly = true)
    {
        $sql = 'SELECT SUM(cost) as total FROM ' . $this->table . 
               ' WHERE is_active = 1';
        
        if (!$include_yearly) {
            $sql .= ' AND frequency = "monthly"';
        }
        
        $result = $this->db->selectOne($sql, []);
        return $result['total'] ?? 0;
    }

    /**
     * Totaal jaarlijks kosten
     */
    public function total_yearly_cost($user_id = null)
    {
        $sql = 'SELECT SUM(CASE 
                    WHEN frequency = "monthly" THEN cost * 12
                    WHEN frequency = "yearly" THEN cost
                    ELSE 0
                END) as total FROM ' . $this->table . 
               ' WHERE is_active = 1';
        
        $result = $this->db->selectOne($sql, []);
        return $result['total'] ?? 0;
    }

    /**
     * Kosten per type
     */
    public function cost_by_type($user_id = null)
    {
        $sql = 'SELECT type, SUM(cost) as total, COUNT(*) as count FROM ' . $this->table . 
               ' WHERE is_active = 1 GROUP BY type';
        
        return $this->db->select($sql, []);
    }

    /**
     * Maandelijkse kosten trend
     */
    public function monthly_trend($user_id = null, $months = 12)
    {
        $sql = 'SELECT 
                    DATE_FORMAT(CURDATE(), "%Y-%m") as month,
                    SUM(CASE 
                        WHEN frequency = "monthly" THEN cost
                        WHEN frequency = "yearly" THEN cost / 12
                        ELSE 0
                    END) as total
                FROM ' . $this->table . 
               ' WHERE is_active = 1
                GROUP BY YEAR(CURDATE()), MONTH(CURDATE())
                ORDER BY month DESC
                LIMIT ' . (int)$months;
        
        return $this->db->select($sql, []);
    }
}
