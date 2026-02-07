<?php

namespace App\Controllers;

use App\Controller;
use App\Models\Subscription;
use App\Models\Offer;
use App\Models\SavingsRecommendation;

class ComparisonController extends Controller
{
    private $subscription_model;
    private $offer_model;
    private $savings_model;
    
    public function __construct()
    {
        $this->subscription_model = new Subscription();
        $this->offer_model = new Offer();
        $this->savings_model = new SavingsRecommendation();
    }
    
    /**
     * Vergelijk prijzen voor een specifiek abonnement
     */
    public function compare($id)
    {
        $this->auth_required();
        
        $subscription = $this->subscription_model->find($id);
        
        if (!$subscription) {
            return $this->json(['error' => 'Abonnement niet gevonden'], 404);
        }
        
        // Zoek goedkopere alternatieven
        $alternatives = $this->offer_model->findCheaperAlternatives($subscription);
        
        // Sla aanbevelingen op
        foreach ($alternatives as $alt) {
            $this->savings_model->recommend(
                $id,
                $alt['id'],
                $alt['monthly_savings'],
                $alt['yearly_savings']
            );
        }
        
        return $this->json([
            'subscription' => $subscription,
            'alternatives' => $alternatives,
            'total_alternatives' => count($alternatives),
            'best_saving' => $alternatives[0] ?? null
        ]);
    }
    
    /**
     * Batch vergelijking voor alle abonnementen
     */
    public function compareAll()
    {
        $this->auth_required();
        
        $subscriptions = $this->subscription_model->all();
        $results = [];
        $totalSavingsMonthly = 0;
        $totalSavingsYearly = 0;
        
        foreach ($subscriptions as $sub) {
            if (!$sub['is_active']) {
                continue;
            }
            
            $alternatives = $this->offer_model->findCheaperAlternatives($sub);
            
            if (!empty($alternatives)) {
                $bestSaving = $alternatives[0]['yearly_savings'];
                $totalSavingsYearly += $bestSaving;
                $totalSavingsMonthly += $alternatives[0]['monthly_savings'];
                
                // Sla beste aanbeveling op
                $this->savings_model->recommend(
                    $sub['id'],
                    $alternatives[0]['id'],
                    $alternatives[0]['monthly_savings'],
                    $alternatives[0]['yearly_savings']
                );
                
                $results[] = [
                    'subscription' => $sub,
                    'best_alternative' => $alternatives[0],
                    'alternatives_count' => count($alternatives)
                ];
            }
        }
        
        return $this->json([
            'results' => $results,
            'total_potential_savings_monthly' => round($totalSavingsMonthly, 2),
            'total_potential_savings_yearly' => round($totalSavingsYearly, 2),
            'subscriptions_with_savings' => count($results)
        ]);
    }
    
    /**
     * Accepteer een aanbeveling
     */
    public function accept($recommendationId)
    {
        $this->auth_required();
        
        $notes = $_POST['notes'] ?? null;
        
        if ($this->savings_model->accept($recommendationId, $notes)) {
            set_flash('success', 'Aanbeveling geaccepteerd');
            return $this->json(['success' => true, 'message' => 'Aanbeveling geaccepteerd']);
        }
        
        return $this->json(['error' => 'Fout bij accepteren'], 500);
    }
    
    /**
     * Weiger een aanbeveling
     */
    public function reject($recommendationId)
    {
        $this->auth_required();
        
        $notes = $_POST['notes'] ?? null;
        
        if ($this->savings_model->reject($recommendationId, $notes)) {
            set_flash('success', 'Aanbeveling afgewezen');
            return $this->json(['success' => true, 'message' => 'Aanbeveling afgewezen']);
        }
        
        return $this->json(['error' => 'Fout bij afwijzen'], 500);
    }
    
    /**
     * Toon overzicht van alle besparingen
     */
    public function savingsOverview()
    {
        $this->auth_required();
        
        $totals = $this->savings_model->totalPotentialSavings();
        $subscriptions = $this->subscription_model->all();
        
        $savingsData = [];
        foreach ($subscriptions as $sub) {
            $recommendations = $this->savings_model->forSubscription($sub['id'], 'pending');
            if (!empty($recommendations)) {
                $savingsData[] = [
                    'subscription' => $sub,
                    'recommendations' => $recommendations,
                    'best_saving' => $recommendations[0] ?? null
                ];
            }
        }
        
        $flash = get_flash();
        
        $this->render('comparison/overview', [
            'totals' => $totals,
            'savings_data' => $savingsData,
            'flash' => $flash
        ]);
    }
    
    /**
     * Helper: JSON response
     */
    private function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
