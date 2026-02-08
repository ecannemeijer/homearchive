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
        
        $alternatives = [];
        
        // Haal interne aanbiedingen op
        $internalOffers = $this->forCategory($category);
        foreach ($internalOffers as $offer) {
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
                    'savings_percentage' => round(($monthlySaving / $currentMonthly) * 100, 1),
                    'source' => 'internal'
                ]);
            }
        }
        
        // Haal externe aanbiedingen op als API geconfigureerd
        $externalOffers = $this->fetchExternalOffers($category, $currentMonthly);
        foreach ($externalOffers as $offer) {
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
                    'savings_percentage' => round(($monthlySaving / $currentMonthly) * 100, 1),
                    'source' => 'external'
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
     * Haal externe aanbiedingen op via API
     */
    private function fetchExternalOffers($category, $maxPrice)
    {
        $apiUrl = getenv('PRICE_API_URL');
        $apiKey = getenv('PRICE_API_KEY');
        
        if (empty($apiUrl) || empty($apiKey)) {
            return [];
        }
        
        try {
            // Bouw API URL met parameters
            $url = $apiUrl . '?' . http_build_query([
                'category' => $category,
                'max_price' => $maxPrice,
                'key' => $apiKey
            ]);
            
            // Maak HTTP request
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => 'Accept: application/json',
                    'timeout' => 10
                ]
            ]);
            
            $response = file_get_contents($url, false, $context);
            
            if ($response === false) {
                error_log("Price API request failed for category: $category");
                return [];
            }
            
            $data = json_decode($response, true);
            
            if (!isset($data['offers']) || !is_array($data['offers'])) {
                error_log("Invalid API response format");
                return [];
            }
            
            // Normaliseer API response naar interne format
            $offers = [];
            foreach ($data['offers'] as $offer) {
                $offers[] = [
                    'id' => 'ext_' . ($offer['id'] ?? uniqid()),
                    'provider' => $offer['provider'] ?? 'Onbekend',
                    'plan_name' => $offer['plan_name'] ?? $offer['name'] ?? 'Standaard',
                    'price' => (float) ($offer['price'] ?? 0),
                    'frequency' => $offer['frequency'] ?? 'monthly',
                    'category' => $category,
                    'description' => $offer['description'] ?? '',
                    'url' => $offer['url'] ?? '',
                    'is_active' => 1
                ];
            }
            
            return $offers;
            
        } catch (\Exception $e) {
            error_log("Error fetching external offers: " . $e->getMessage());
            return [];
        }
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
