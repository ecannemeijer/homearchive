<?php

namespace App\Controllers;

use App\Controller;
use App\Models\Subscription;
use App\Models\Category;

class DashboardController extends Controller
{
    private $subscription_model;
    private $category_model;

    public function __construct()
    {
        $this->subscription_model = new Subscription();
        $this->category_model = new Category();
    }

    /**
     * Dashboard overzicht
     */
    public function index()
    {
        $this->auth_required();
        $user_id = auth_id();

        // Bijna verlopen items
        $expiring = $this->subscription_model->expiring_soon($user_id);

        // Totale kosten
        $monthly_total = $this->subscription_model->total_monthly_cost($user_id);
        $yearly_total = $this->subscription_model->total_yearly_cost($user_id);

        // Kosten per type
        $costs_by_type = $this->subscription_model->cost_by_type($user_id);

        // Aantal abonnementen
        $all_subs = $this->subscription_model->user_subscriptions($user_id);
        $active_subs = $this->subscription_model->user_subscriptions($user_id, 'active');

        // Recente items
        $db = $this->subscription_model->db();
        $recent = $db->select(
            'SELECT * FROM subscriptions WHERE user_id = :user_id AND is_active = 1 ORDER BY created_at DESC LIMIT 5',
            ['user_id' => $user_id]
        );

        $flash = get_flash();

        $this->render('dashboard/index', [
            'expiring' => $expiring,
            'monthly_total' => $monthly_total,
            'yearly_total' => $yearly_total,
            'costs_by_type' => $costs_by_type,
            'all_subs' => count($all_subs),
            'active_subs' => count($active_subs),
            'recent' => $recent,
            'flash' => $flash
        ]);
    }
}
