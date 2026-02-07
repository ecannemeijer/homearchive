<?php

namespace App\Controllers;

use App\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    private $category_model;

    public function __construct()
    {
        $this->category_model = new Category();
    }

    /**
     * Toon categorieën management
     */
    public function index()
    {
        $this->auth_required();

        $categories = $this->category_model->user_categories(null);
        $flash = get_flash();

        $this->render('categories/index', [
            'categories' => $categories,
            'flash' => $flash
        ]);
    }

    /**
     * Create categorie
     */
    public function store()
    {
        $this->auth_required();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Ongeldige request'], 400);
        }

        $name = $_POST['name'] ?? '';
        $color = $_POST['color'] ?? '#3B82F6';

        if (empty($name)) {
            $this->json(['error' => 'Naam is verplicht'], 400);
        }

        $data = [
            'user_id' => 0,
            'name' => $name,
            'color' => $color
        ];

        try {
            $id = $this->category_model->create($data);
            $this->json([
                'success' => true,
                'id' => $id,
                'name' => $name,
                'color' => $color
            ]);
        } catch (\Exception $e) {
            $this->json(['error' => 'Fout bij opslaan'], 500);
        }
    }

    /**
     * Update categorie
     */
    public function update($id)
    {
        $this->auth_required();

        $category = $this->category_model->find($id);
        if (!$category) {
            $this->json(['error' => 'Categorie niet gevonden'], 404);
        }

        $name = $_POST['name'] ?? '';
        $color = $_POST['color'] ?? '#3B82F6';

        if (empty($name)) {
            $this->json(['error' => 'Naam is verplicht'], 400);
        }

        $data = [
            'name' => $name,
            'color' => $color
        ];

        try {
            $this->category_model->update($id, $data);
            $this->json(['success' => true, 'message' => 'Categorie bijgewerkt']);
        } catch (\Exception $e) {
            $this->json(['error' => 'Fout bij bijwerken'], 500);
        }
    }

    /**
     * Delete categorie
     */
    public function delete($id)
    {
        $this->auth_required();

        $category = $this->category_model->find($id);
        if (!$category) {
            $this->json(['error' => 'Categorie niet gevonden'], 404);
        }

        try {
            $this->category_model->delete($id);
            $this->json(['success' => true, 'message' => 'Categorie verwijderd']);
        } catch (\Exception $e) {
            $this->json(['error' => 'Fout bij verwijderen'], 500);
        }
    }
}
