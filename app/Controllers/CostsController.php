<?php

namespace App\Controllers;

use App\Controller;
use App\Models\Subscription;
use App\Models\Password as PasswordModel;

class CostsController extends Controller
{
    private $subscription_model;
    private $password_model;

    public function __construct()
    {
        $this->subscription_model = new Subscription();
        $this->password_model = new PasswordModel();
    }

    /**
     * Kosten overzichtspagina
     */
    public function overview()
    {
        $this->auth_required();
        $user_id = auth_id();

        $type_filter = $_GET['type'] ?? '';

        // Totale kosten
        $monthly_total = $this->subscription_model->total_monthly_cost($user_id);
        $yearly_total = $this->subscription_model->total_yearly_cost($user_id);

        // Kosten per type
        $costs_by_type = $this->subscription_model->cost_by_type($user_id);

        // Alle abonnementen voor grafiek
        $db = $this->subscription_model->db();
        $all_subs = $db->select(
            'SELECT * FROM subscriptions WHERE user_id = :user_id AND is_active = 1 ORDER BY cost DESC',
            ['user_id' => $user_id]
        );

        // Breker per type
        $subscription_cost = 0;
        $insurance_cost = 0;
        $subscription_count = 0;
        $insurance_count = 0;

        foreach ($all_subs as $sub) {
            if ($sub['type'] === 'subscription') {
                $subscription_cost += $sub['cost'];
                $subscription_count++;
            } else {
                $insurance_cost += $sub['cost'];
                $insurance_count++;
            }
        }

        $this->render('costs/overview', [
            'monthly_total' => $monthly_total,
            'yearly_total' => $yearly_total,
            'costs_by_type' => $costs_by_type,
            'all_subs' => $all_subs,
            'subscription_cost' => $subscription_cost,
            'insurance_cost' => $insurance_cost,
            'subscription_count' => $subscription_count,
            'insurance_count' => $insurance_count
        ]);
    }

    /**
     * Maandelijkse trend API
     */
    public function monthly_trend_api()
    {
        $this->auth_required();
        $user_id = auth_id();

        $months = (int)($_GET['months'] ?? 12);
        $months = min($months, 24); // Max 24 maanden

        // Hier simple implementatie - kan verbeterd worden
        $data = [];
        for ($i = $months; $i > 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            $monthly_total = $this->subscription_model->total_monthly_cost($user_id);
            $data[] = [
                'month' => $date,
                'total' => $monthly_total
            ];
        }

        $this->json($data);
    }

    /**
     * Export naar CSV
     */
    public function export_csv()
    {
        $this->auth_required();
        $user_id = auth_id();

        $type = $_GET['type'] ?? 'all';

        $db = $this->subscription_model->db();
        $sql = 'SELECT * FROM subscriptions WHERE user_id = :user_id AND is_active = 1';
        $params = ['user_id' => $user_id];

        if ($type !== 'all') {
            $sql .= ' AND type = :type';
            $params['type'] = $type;
        }

        $sql .= ' ORDER BY name ASC';
        $subscriptions = $db->select($sql, $params);

        // CSV header
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="abonnementen-' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        
        // BOM voor Excel UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, [
            'Naam',
            'Type',
            'Categorie',
            'Maandelijks bedrag',
            'Frequentie',
            'Startdatum',
            'Einddatum',
            'Website',
            'Opmerkingen',
            'Actief'
        ]);

        foreach ($subscriptions as $sub) {
            fputcsv($output, [
                $sub['name'],
                type_label($sub['type']),
                $sub['category'],
                $sub['cost'],
                frequency_label($sub['frequency']),
                $sub['start_date'],
                $sub['end_date'],
                $sub['website_url'],
                $sub['notes'],
                $sub['is_active'] ? 'Ja' : 'Nee'
            ]);
        }

        fclose($output);
        exit;
    }
}
