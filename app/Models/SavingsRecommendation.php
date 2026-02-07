<?php

namespace App\Models;

use App\Model;

class SavingsRecommendation extends Model
{
    protected $table = 'savings_recommendations';
    
    /**
     * Maak een nieuwe besparing-aanbeveling
     */
    public function recommend($subscriptionId, $offerId, $monthlySavings, $yearlySavings)
    {
        // Check of deze combinatie al bestaat
        $existing = $this->db->selectOne(
            "SELECT id FROM {$this->table} 
             WHERE subscription_id = :sub_id AND offer_id = :offer_id 
             AND status = 'pending'",
            ['sub_id' => $subscriptionId, 'offer_id' => $offerId]
        );
        
        if ($existing) {
            // Update bestaande aanbeveling
            return $this->db->update(
                $this->table,
                [
                    'monthly_savings' => $monthlySavings,
                    'yearly_savings' => $yearlySavings,
                    'recommended_at' => date('Y-m-d H:i:s')
                ],
                'id = :id',
                ['id' => $existing['id']]
            );
        }
        
        // Maak nieuwe aanbeveling
        return $this->create([
            'subscription_id' => $subscriptionId,
            'offer_id' => $offerId,
            'monthly_savings' => $monthlySavings,
            'yearly_savings' => $yearlySavings
        ]);
    }
    
    /**
     * Vind aanbevelingen voor een abonnement
     */
    public function forSubscription($subscriptionId, $status = null)
    {
        $sql = "SELECT sr.*, o.provider, o.plan_name, o.url, o.description, o.price, o.frequency
                FROM {$this->table} sr
                JOIN offers o ON sr.offer_id = o.id
                WHERE sr.subscription_id = :sub_id";
        
        $params = ['sub_id' => $subscriptionId];
        
        if ($status) {
            $sql .= " AND sr.status = :status";
            $params['status'] = $status;
        }
        
        $sql .= " ORDER BY sr.monthly_savings DESC";
        
        return $this->db->select($sql, $params);
    }
    
    /**
     * Totale potentiële besparingen
     */
    public function totalPotentialSavings($userId = null)
    {
        $sql = "SELECT 
                    SUM(sr.monthly_savings) as total_monthly,
                    SUM(sr.yearly_savings) as total_yearly,
                    COUNT(DISTINCT sr.subscription_id) as subscriptions_count
                FROM {$this->table} sr";
        
        if ($userId) {
            $sql .= " JOIN subscriptions s ON sr.subscription_id = s.id
                     WHERE s.user_id = :user_id AND sr.status = 'pending'";
            $result = $this->db->selectOne($sql, ['user_id' => $userId]);
        } else {
            $sql .= " WHERE sr.status = 'pending'";
            $result = $this->db->selectOne($sql);
        }
        
        return [
            'total_monthly' => $result['total_monthly'] ?? 0,
            'total_yearly' => $result['total_yearly'] ?? 0,
            'subscriptions_count' => $result['subscriptions_count'] ?? 0
        ];
    }
    
    /**
     * Accepteer een aanbeveling
     */
    public function accept($id, $notes = null)
    {
        return $this->update($id, [
            'status' => 'accepted',
            'responded_at' => date('Y-m-d H:i:s'),
            'notes' => $notes
        ]);
    }
    
    /**
     * Weiger een aanbeveling
     */
    public function reject($id, $notes = null)
    {
        return $this->update($id, [
            'status' => 'rejected',
            'responded_at' => date('Y-m-d H:i:s'),
            'notes' => $notes
        ]);
    }
    
    /**
     * Markeer verlopen aanbevelingen
     */
    public function markExpired($days = 30)
    {
        $sql = "UPDATE {$this->table} 
                SET status = 'expired' 
                WHERE status = 'pending' 
                AND recommended_at < DATE_SUB(NOW(), INTERVAL :days DAY)";
        
        return $this->db->query($sql, ['days' => $days]);
    }
}
