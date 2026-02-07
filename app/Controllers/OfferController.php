<?php

namespace App\Controllers;

use App\Controller;
use App\Models\Offer;
use App\Models\Category;

class OfferController extends Controller
{
    private $offer_model;
    private $category_model;
    
    public function __construct()
    {
        $this->offer_model = new Offer();
        $this->category_model = new Category();
    }
    
    /**
     * Toon alle aanbiedingen (admin only)
     */
    public function index()
    {
        $this->auth_required();
        $this->admin_required();
        
        $offers = $this->offer_model->all();
        $categories = $this->category_model->all();
        $flash = get_flash();
        
        $this->render('offers/index', [
            'offers' => $offers,
            'categories' => $categories,
            'flash' => $flash
        ]);
    }
    
    /**
     * Formulier nieuwe aanbieding
     */
    public function create()
    {
        $this->auth_required();
        $this->admin_required();
        
        $categories = $this->category_model->all();
        $flash = get_flash();
        
        $this->render('offers/create', [
            'categories' => $categories,
            'flash' => $flash
        ]);
    }
    
    /**
     * Sla nieuwe aanbieding op
     */
    public function store()
    {
        $this->auth_required();
        $this->admin_required();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/offers');
        }
        
        $data = [
            'provider' => $_POST['provider'] ?? '',
            'plan_name' => $_POST['plan_name'] ?? '',
            'price' => $_POST['price'] ?? 0,
            'frequency' => $_POST['frequency'] ?? 'monthly',
            'category' => $_POST['category'] ?? null,
            'description' => $_POST['description'] ?? null,
            'url' => $_POST['url'] ?? null,
            'conditions' => $_POST['conditions'] ?? null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        
        // Validaties
        if (empty($data['provider']) || empty($data['plan_name'])) {
            set_flash('error', 'Provider en plan naam zijn verplicht');
            redirect('/offers/create');
        }
        
        if ($data['price'] < 0) {
            set_flash('error', 'Prijs kan niet negatief zijn');
            redirect('/offers/create');
        }
        
        try {
            $this->offer_model->create($data);
            set_flash('success', 'Aanbieding succesvol toegevoegd');
            redirect('/offers');
        } catch (\Exception $e) {
            set_flash('error', 'Fout bij toevoegen: ' . $e->getMessage());
            redirect('/offers/create');
        }
    }
    
    /**
     * Toon enkele aanbieding
     */
    public function show($id)
    {
        $this->auth_required();
        $this->admin_required();
        
        $offer = $this->offer_model->find($id);
        
        if (!$offer) {
            set_flash('error', 'Aanbieding niet gevonden');
            redirect('/offers');
        }
        
        $flash = get_flash();
        
        $this->render('offers/show', [
            'offer' => $offer,
            'flash' => $flash
        ]);
    }
    
    /**
     * Formulier bewerken
     */
    public function edit($id)
    {
        $this->auth_required();
        $this->admin_required();
        
        $offer = $this->offer_model->find($id);
        $categories = $this->category_model->all();
        
        if (!$offer) {
            set_flash('error', 'Aanbieding niet gevonden');
            redirect('/offers');
        }
        
        $flash = get_flash();
        
        $this->render('offers/edit', [
            'offer' => $offer,
            'categories' => $categories,
            'flash' => $flash
        ]);
    }
    
    /**
     * Update aanbieding
     */
    public function update($id)
    {
        $this->auth_required();
        $this->admin_required();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/offers');
        }
        
        $data = [
            'provider' => $_POST['provider'] ?? '',
            'plan_name' => $_POST['plan_name'] ?? '',
            'price' => $_POST['price'] ?? 0,
            'frequency' => $_POST['frequency'] ?? 'monthly',
            'category' => $_POST['category'] ?? null,
            'description' => $_POST['description'] ?? null,
            'url' => $_POST['url'] ?? null,
            'conditions' => $_POST['conditions'] ?? null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        
        // Validaties
        if (empty($data['provider']) || empty($data['plan_name'])) {
            set_flash('error', 'Provider en plan naam zijn verplicht');
            redirect('/offers/' . $id . '/edit');
        }
        
        try {
            $this->offer_model->update($id, $data);
            set_flash('success', 'Aanbieding bijgewerkt');
            redirect('/offers');
        } catch (\Exception $e) {
            set_flash('error', 'Fout bij bijwerken: ' . $e->getMessage());
            redirect('/offers/' . $id . '/edit');
        }
    }
    
    /**
     * Verwijder aanbieding
     */
    public function delete($id)
    {
        $this->auth_required();
        $this->admin_required();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/offers');
        }
        
        try {
            $this->offer_model->delete($id);
            set_flash('success', 'Aanbieding verwijderd');
        } catch (\Exception $e) {
            set_flash('error', 'Fout bij verwijderen: ' . $e->getMessage());
        }
        
        redirect('/offers');
    }
}
