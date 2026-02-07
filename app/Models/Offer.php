<?php

namespace App\Models;

use App\Model;

class Offer extends Model
{
    protected $table = 'offers';
    
    /**
     * Vind aanbiedingen voor een specifieke categorie
     */
    public function forCategory($category)
    {
        $sql = "SELECT * FROM {$this->table} WHERE category = :category AND is_active = 1 ORDER BY price ASC";
        return $this->db->select($sql, ['category' => $category]);
    }
    
    /**
     * Vind goedkopere alternatieven voor een abonnement
     */
    public function findCheaperAlternatives($subscription)
    {
        // Normaliseer naar maandprijs voor vergelijking
        $frequency = $subscription['frequency'] ?? 'monthly';
        $currentMonthly = ($frequency === 'yearly') 
            ? $subscription['cost'] / 12 
            : $subscription['cost'];
        
        $category = $subscription['category'] ?? null;
        if (!$category) {
            return [];
        }
        
        $offers = $this->forCategory($category);
        $alternatives = [];
        
        foreach ($offers as $offer) {
            $offerMonthly = ($offer['frequency'] === 'yearly') 
                ? $offer['price'] / 12 
                : $offer['price'];
            
            if ($offerMonthly < $currentMonthly) {
                $monthlySaving = $currentMonthly - $offerMonthly;
                $yearlySaving = $monthlySaving * 12;
                
                $alternatives[] = array_merge($offer, [
                    'monthly_price' => round($offerMonthly, 2),
                    'monthly_savings' => round($monthlySaving, 2),
                    'yearly_savings' => round($yearlySaving, 2),
                    'savings_percentage' => round(($monthlySaving / $currentMonthly) * 100, 1)
                ]);
            }
        }
        
        // Sorteer op hoogste besparing
        usort($alternatives, function($a, $b) {
            return $b['monthly_savings'] <=> $a['monthly_savings'];
        });
        
        return $alternatives;
    }
    
    /**
     * Alle actieve categorieën met aanbiedingen
     */
    public function activeCategories()
    {
        $sql = "SELECT DISTINCT category FROM {$this->table} 
                WHERE is_active = 1 AND category IS NOT NULL 
                ORDER BY category";
        return $this->db->select($sql);
    }
    
    /**
     * Aantal actieve aanbiedingen
     */
    public function countActive()
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE is_active = 1";
        $result = $this->db->selectOne($sql);
        return $result['count'] ?? 0;
    }
    
    /**
     * Zoek aanbiedingen
     */
    public function search($query, $category = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1";
        $params = [];
        
        if (!empty($query)) {
            $sql .= " AND (provider LIKE :query OR plan_name LIKE :query OR description LIKE :query)";
            $params['query'] = '%' . $query . '%';
        }
        
        if (!empty($category)) {
            $sql .= " AND category = :category";
            $params['category'] = $category;
        }
        
        $sql .= " ORDER BY price ASC";
        
        return $this->db->select($sql, $params);
    }
}
