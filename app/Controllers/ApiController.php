<?php

namespace App\Controllers;

use App\Controller;
use App\Models\Subscription;
use App\Notification;

class ApiController extends Controller
{
    private $subscription_model;
    private $notification;

    public function __construct()
    {
        $this->subscription_model = new Subscription();
        $this->notification = new Notification();
    }

    /**
     * Get statistieken voor dashboard
     */
    public function stats()
    {
        $this->auth_required();
        $user_id = auth_id();

        $monthly_total = $this->subscription_model->total_monthly_cost($user_id);
        $yearly_total = $this->subscription_model->total_yearly_cost($user_id);
        $costs_by_type = $this->subscription_model->cost_by_type($user_id);
        $expiring = $this->subscription_model->expiring_soon($user_id);

        $this->json([
            'monthly_total' => $monthly_total,
            'yearly_total' => $yearly_total,
            'costs_by_type' => $costs_by_type,
            'expiring_count' => count($expiring)
        ]);
    }

    /**
     * Get notificaties
     */
    public function notifications()
    {
        $this->auth_required();
        $user_id = auth_id();

        $unread = $this->notification->unread($user_id);

        $this->json([
            'count' => count($unread),
            'notifications' => $unread
        ]);
    }

    /**
     * Mark notification as read
     */
    public function read_notification($id)
    {
        $this->auth_required();
        $user_id = auth_id();

        $this->notification->mark_read($id, $user_id);

        $this->json(['success' => true]);
    }

    /**
     * Check expiring items
     */
    public function check_expiring()
    {
        $this->auth_required();
        $user_id = auth_id();

        $days = (int)($_GET['days'] ?? 30);
        $this->notification->check_expiring($user_id, $days);

        $expiring = $this->subscription_model->expiring_soon($user_id, $days);

        $this->json([
            'success' => true,
            'count' => count($expiring),
            'items' => $expiring
        ]);
    }

    /**
     * Search in all items
     */
    public function search()
    {
        $this->auth_required();
        $user_id = auth_id();

        $query = trim($_GET['q'] ?? '');

        if (strlen($query) < 2) {
            $this->json(['results' => []]);
        }

        $search = '%' . $query . '%';
        $db = $this->subscription_model->db();

        // Search subscriptions
        $subs = $db->select(
            'SELECT id, name, cost, type FROM subscriptions 
             WHERE user_id = :user_id AND (name LIKE :search OR notes LIKE :search)
             LIMIT 10',
            ['user_id' => $user_id, 'search' => $search]
        );

        // Search passwords
        $pwds = $db->select(
            'SELECT id, title, username, website_url FROM passwords
             WHERE user_id = :user_id AND (title LIKE :search OR username LIKE :search)
             LIMIT 5',
            ['user_id' => $user_id, 'search' => $search]
        );

        $results = [];

        foreach ($subs as $sub) {
            $results[] = [
                'type' => 'subscription',
                'id' => $sub['id'],
                'title' => $sub['name'],
                'subtitle' => format_price($sub['cost']),
                'url' => '/subscriptions/' . $sub['id']
            ];
        }

        foreach ($pwds as $pwd) {
            $results[] = [
                'type' => 'password',
                'id' => $pwd['id'],
                'title' => $pwd['title'],
                'subtitle' => $pwd['username'] ?? 'Geen username',
                'url' => '/password-vault/' . $pwd['id']
            ];
        }

        $this->json(['results' => $results]);
    }

    /**
     * Get monthly cost trend data
     */
    public function cost_trend()
    {
        $this->auth_required();
        $user_id = auth_id();

        $months = (int)($_GET['months'] ?? 12);
        $months = min($months, 24);

        $trend = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = new \DateTime("-$i months");
            $month_key = $date->format('Y-m');

            // Approximate calculation
            $monthly_cost = $this->subscription_model->total_monthly_cost($user_id);
            
            $trend[] = [
                'month' => $month_key,
                'total' => $monthly_cost,
                'label' => $date->format('M Y')
            ];
        }

        $this->json(['trend' => $trend]);
    }

    /**
     * Health check
     */
    public function health()
    {
        $this->json([
            'status' => 'ok',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0.0'
        ]);
    }
}
