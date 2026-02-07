<?php

namespace App\Controllers;

use App\Controller;
use App\Models\Subscription;
use App\Models\Document;
use App\Models\Category;

class SubscriptionController extends Controller
{
    private $subscription_model;
    private $document_model;
    private $category_model;

    public function __construct()
    {
        $this->subscription_model = new Subscription();
        $this->document_model = new Document();
        $this->category_model = new Category();
    }

    /**
     * Overzicht alle abonnementen
     */
    public function index()
    {
        $this->auth_required();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = (int)($_GET['limit'] ?? 10);
        $offset = ($page - 1) * $limit;

        $filters = [
            'type' => $_GET['type'] ?? '',
            'frequency' => $_GET['frequency'] ?? '',
            'search' => $_GET['search'] ?? '',
            'sort_by' => $_GET['sort_by'] ?? 'name',
            'sort_order' => $_GET['sort_order'] ?? 'ASC',
            'limit' => $limit,
            'offset' => $offset
        ];

        // Alle categorieën
        $categories = $this->category_model->all();

        // Filter op categorie als geselecteerd
        if (!empty($_GET['category'])) {
            $filters['category'] = $_GET['category'];
        }

        // Abonnementen
        $subscriptions = $this->subscription_model->filtered(null, $filters);
        $total = $this->subscription_model->count_filtered(null, $filters);

        $pages = ceil($total / $limit);
        $flash = get_flash();

        $this->render('subscriptions/index', [
            'subscriptions' => $subscriptions,
            'total' => $total,
            'page' => $page,
            'pages' => $pages,
            'limit' => $limit,
            'filters' => $filters,
            'categories' => $categories,
            'flash' => $flash
        ]);
    }

    /**
     * Maak nieuw abonnement
     */
    public function create()
    {
        $this->auth_required();

        $categories = $this->category_model->all();
        $flash = get_flash();

        $this->render('subscriptions/create', [
            'categories' => $categories,
            'flash' => $flash
        ]);
    }

    /**
     * Handle create form
     */
    public function store()
    {
        $this->auth_required();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/subscriptions');
        }

        $data = [
            'user_id' => 0,
            'name' => $_POST['name'] ?? '',
            'type' => $_POST['type'] ?? 'subscription',
            'category' => $_POST['category'] ?? '',
            'cost' => $_POST['cost'] ?? 0,
            'frequency' => $_POST['frequency'] ?? 'monthly',
            'billing_date' => !empty($_POST['billing_date']) ? (int)$_POST['billing_date'] : null,
            'start_date' => $_POST['start_date'] ?? null,
            'end_date' => $_POST['end_date'] ?? null,
            'is_monthly_cancelable' => isset($_POST['is_monthly_cancelable']) ? 1 : 0,
            'username' => $_POST['username'] ?? '',
            'website_url' => $_POST['website_url'] ?? '',
            'notes' => $_POST['notes'] ?? '',
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'renewal_reminder' => $_POST['renewal_reminder'] ?? 7
        ];

        // Password encrypten als gevuld
        if (!empty($_POST['password'])) {
            $data['password_encrypted'] = encrypt_password($_POST['password'], config('encryption.key'));
        }

        // Validaties
        if (empty($data['name'])) {
            set_flash('error', 'Naam is verplicht');
            redirect('/subscriptions/create');
        }

        if ($data['cost'] < 0) {
            set_flash('error', 'Kosten kunnen niet negatief zijn');
            redirect('/subscriptions/create');
        }

        // Opslaan
        try {
            $this->subscription_model->create($data);
            set_flash('success', 'Abonnement toegevoegd');
            redirect('/subscriptions');
        } catch (\Exception $e) {
            set_flash('error', 'Fout bij opslaan: ' . $e->getMessage());
            redirect('/subscriptions/create');
        }
    }

    /**
     * Toon enkel abonnement
     */
    public function show($id)
    {
        $this->auth_required();

        $subscription = $this->subscription_model->find($id);

        if (!$subscription) {
            set_flash('error', 'Abonnement niet gevonden');
            redirect('/subscriptions');
        }

        // Decrypt wachtwoord als aanwezig
        if (!empty($subscription['password_encrypted'])) {
            $subscription['password'] = decrypt_password($subscription['password_encrypted'], config('encryption.key'));
        }

        // Documenten
        $documents = $this->document_model->subscription_documents($id);

        $this->render('subscriptions/show', [
            'subscription' => $subscription,
            'documents' => $documents
        ]);
    }

    /**
     * Bewerk abonnement
     */
    public function edit($id)
    {
        $this->auth_required();

        $subscription = $this->subscription_model->find($id);

        if (!$subscription) {
            set_flash('error', 'Abonnement niet gevonden');
            redirect('/subscriptions');
        }

        if (!empty($subscription['password_encrypted'])) {
            $subscription['password'] = decrypt_password($subscription['password_encrypted'], config('encryption.key'));
        }

        $categories = $this->category_model->user_categories($user_id);
        $flash = get_flash();

        $this->render('subscriptions/edit', [
            'subscription' => $subscription,
            'categories' => $categories,
            'flash' => $flash
        ]);
    }

    /**
     * Handle update
     */
    public function update($id)
    {
        $this->auth_required();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/subscriptions');
        }

        $subscription = $this->subscription_model->find($id);

        if (!$subscription) {
            set_flash('error', 'Abonnement niet gevonden');
            redirect('/subscriptions');
        }

        $data = [
            'name' => $_POST['name'] ?? '',
            'type' => $_POST['type'] ?? 'subscription',
            'category' => $_POST['category'] ?? '',
            'cost' => $_POST['cost'] ?? 0,
            'frequency' => $_POST['frequency'] ?? 'monthly',
            'billing_date' => !empty($_POST['billing_date']) ? (int)$_POST['billing_date'] : null,
            'start_date' => $_POST['start_date'] ?? null,
            'end_date' => $_POST['end_date'] ?? null,
            'is_monthly_cancelable' => isset($_POST['is_monthly_cancelable']) ? 1 : 0,
            'username' => $_POST['username'] ?? '',
            'website_url' => $_POST['website_url'] ?? '',
            'notes' => $_POST['notes'] ?? '',
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'renewal_reminder' => $_POST['renewal_reminder'] ?? 7
        ];

        if (!empty($_POST['password'])) {
            $data['password_encrypted'] = encrypt_password($_POST['password'], config('encryption.key'));
        }

        if (empty($data['name'])) {
            set_flash('error', 'Naam is verplicht');
            redirect('/subscriptions/' . $id . '/edit');
        }

        try {
            $this->subscription_model->update($id, $data);
            set_flash('success', 'Abonnement bijgewerkt');
            redirect('/subscriptions/' . $id);
        } catch (\Exception $e) {
            set_flash('error', 'Fout bij bijwerken');
            redirect('/subscriptions/' . $id . '/edit');
        }
    }

    /**
     * Delete abonnement
     */
    public function delete($id)
    {
        $this->auth_required();

        $subscription = $this->subscription_model->find($id);

        if (!$subscription) {
            set_flash('error', 'Abonnement niet gevonden');
            redirect('/subscriptions');
        }

        try {
            $this->subscription_model->delete($id);
            
            // Verwijder ook bijbehorende documenten
            $db = $this->subscription_model->db();
            $documents = $this->document_model->subscription_documents($id);
            foreach ($documents as $doc) {
                if (file_exists($doc['file_path'])) {
                    unlink($doc['file_path']);
                }
            }
            
            $db->delete('documents', 'subscription_id = :id', ['id' => $id]);
            
            set_flash('success', 'Abonnement verwijderd');
        } catch (\Exception $e) {
            set_flash('error', 'Fout bij verwijderen');
        }

        redirect('/subscriptions');
    }
}
