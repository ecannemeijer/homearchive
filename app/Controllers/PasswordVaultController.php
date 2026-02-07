<?php

namespace App\Controllers;

use App\Controller;
use App\Models\Password as PasswordModel;

class PasswordVaultController extends Controller
{
    private $password_model;

    public function __construct()
    {
        $this->password_model = new PasswordModel();
    }

    /**
     * Overzicht alle wachtwoorden
     */
    public function index()
    {
        $this->auth_required();

        $search = $_GET['search'] ?? '';
        
        if (!empty($search)) {
            $passwords = $this->password_model->search(null, $search);
        } else {
            $passwords = $this->password_model->user_passwords(null);
        }

        $flash = get_flash();

        $this->render('password_vault/index', [
            'passwords' => $passwords,
            'search' => $search,
            'flash' => $flash
        ]);
    }

    /**
     * Maak nieuw wachtwoord
     */
    public function create()
    {
        $this->auth_required();
        $flash = get_flash();

        // Genereer veilig wachtwoord
        $suggested_password = generate_secure_password(16);

        $this->render('password_vault/create', [
            'suggested_password' => $suggested_password,
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
            redirect('/password-vault');
        }

        $data = [
            'user_id' => 0,
            'title' => $_POST['title'] ?? '',
            'username' => $_POST['username'] ?? '',
            'password_encrypted' => '',
            'website_url' => $_POST['website_url'] ?? '',
            'notes' => $_POST['notes'] ?? '',
            'tags' => $_POST['tags'] ?? ''
        ];

        if (empty($data['title'])) {
            set_flash('error', 'Titel is verplicht');
            redirect('/password-vault/create');
        }

        if (empty($_POST['password'])) {
            set_flash('error', 'Wachtwoord is verplicht');
            redirect('/password-vault/create');
        }

        // Encrypt wachtwoord
        $data['password_encrypted'] = encrypt_password($_POST['password'], config('encryption.key'));

        try {
            $this->password_model->create($data);
            set_flash('success', 'Wachtwoord opgeslagen');
            redirect('/password-vault');
        } catch (\Exception $e) {
            set_flash('error', 'Fout bij opslaan');
            redirect('/password-vault/create');
        }
    }

    /**
     * Toon wachtwoord (met decryptie)
     */
    public function show($id)
    {
        $this->auth_required();
        $user_id = auth_id();

        $password = $this->password_model->find_for_user($id, $user_id);

        if (!$password) {
            set_flash('error', 'Wachtwoord niet gevonden');
            redirect('/password-vault');
        }

        // Decrypt
        $password['password_decrypted'] = decrypt_password($password['password_encrypted'], config('encryption.key'));

        $this->render('password_vault/show', [
            'password' => $password
        ]);
    }

    /**
     * Bewerk wachtwoord
     */
    public function edit($id)
    {
        $this->auth_required();

        $password = $this->password_model->find_for_user($id, null);

        if (!$password) {
            set_flash('error', 'Wachtwoord niet gevonden');
            redirect('/password-vault');
        }

        $password['password_decrypted'] = decrypt_password($password['password_encrypted'], config('encryption.key'));
        $flash = get_flash();

        $this->render('password_vault/edit', [
            'password' => $password,
            'flash' => $flash
        ]);
    }

    /**
     * Handle update
     */
    public function update($id)
    {
        $this->auth_required();

        $password = $this->password_model->find_for_user($id, null);

        if (!$password) {
            set_flash('error', 'Wachtwoord niet gevonden');
            redirect('/password-vault');
        }

        $data = [
            'title' => $_POST['title'] ?? '',
            'username' => $_POST['username'] ?? '',
            'website_url' => $_POST['website_url'] ?? '',
            'notes' => $_POST['notes'] ?? '',
            'tags' => $_POST['tags'] ?? ''
        ];

        if (!empty($_POST['password'])) {
            $data['password_encrypted'] = encrypt_password($_POST['password'], config('encryption.key'));
        }

        try {
            $this->password_model->update($id, $data);
            set_flash('success', 'Wachtwoord bijgewerkt');
            redirect('/password-vault/' . $id);
        } catch (\Exception $e) {
            set_flash('error', 'Fout bij bijwerken');
            redirect('/password-vault/' . $id . '/edit');
        }
    }

    /**
     * Delete wachtwoord
     */
    public function delete($id)
    {
        $this->auth_required();

        $password = $this->password_model->find_for_user($id, null);

        if (!$password) {
            set_flash('error', 'Wachtwoord niet gevonden');
            redirect('/password-vault');
        }

        try {
            $this->password_model->delete($id);
            set_flash('success', 'Wachtwoord verwijderd');
        } catch (\Exception $e) {
            set_flash('error', 'Fout bij verwijderen');
        }

        redirect('/password-vault');
    }

    /**
     * API: Genereer nieuw wachtwoord
     */
    public function generate_password_api()
    {
        $this->auth_required();

        $length = (int)($_GET['length'] ?? 16);
        $length = max(8, min($length, 32)); // Min 8, max 32

        $password = generate_secure_password($length);

        $this->json([
            'password' => $password,
            'length' => strlen($password)
        ]);
    }
}
