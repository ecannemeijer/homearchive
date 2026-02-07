<?php

namespace App\Controllers;

use App\Controller;
use App\Models\User;

class UserManagementController extends Controller
{
    private $user_model;

    public function __construct()
    {
        $this->user_model = new User();
    }

    /**
     * List all users (admin only)
     */
    public function index()
    {
        $this->auth_required();
        $this->admin_required();

        $users = $this->user_model->all();
        $current_user = auth_user();
        $flash = get_flash();

        $this->render('users/index', [
            'users' => $users,
            'current_user' => $current_user,
            'flash' => $flash
        ]);
    }

    /**
     * Show create user form
     */
    public function create()
    {
        $this->auth_required();
        $this->admin_required();

        $this->render('users/create');
    }

    /**
     * Store new user
     */
    public function store()
    {
        $this->auth_required();
        $this->admin_required();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/users');
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $is_admin = isset($_POST['is_admin']) ? 1 : 0;

        if (empty($name) || empty($email) || empty($password)) {
            set_flash('error', 'Alle velden zijn verplicht');
            redirect('/users/create');
        }

        if (!is_valid_email($email)) {
            set_flash('error', 'Ongeldig e-mailadres');
            redirect('/users/create');
        }

        if (strlen($password) < 6) {
            set_flash('error', 'Wachtwoord moet minstens 6 karakters zijn');
            redirect('/users/create');
        }

        // Check if email exists
        if ($this->user_model->by_email($email)) {
            set_flash('error', 'Dit e-mailadres is al geregistreerd');
            redirect('/users/create');
        }

        try {
            $data = [
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'is_admin' => $is_admin
            ];
            
            $this->user_model->create($data);
            set_flash('success', 'Gebruiker succesvol aangemaakt');
            redirect('/users');
        } catch (\Exception $e) {
            set_flash('error', 'Fout bij aanmaken gebruiker');
            redirect('/users/create');
        }
    }

    /**
     * Edit user form
     */
    public function edit($id)
    {
        $this->auth_required();
        $this->admin_required();

        $user = $this->user_model->find($id);
        
        if (!$user) {
            set_flash('error', 'Gebruiker niet gevonden');
            redirect('/users');
        }

        $this->render('users/edit', ['user' => $user]);
    }

    /**
     * Update user
     */
    public function update($id)
    {
        $this->auth_required();
        $this->admin_required();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/users');
        }

        $user = $this->user_model->find($id);
        
        if (!$user) {
            set_flash('error', 'Gebruiker niet gevonden');
            redirect('/users');
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $is_admin = isset($_POST['is_admin']) ? 1 : 0;

        if (empty($name) || empty($email)) {
            set_flash('error', 'Naam en e-mail zijn verplicht');
            redirect('/users/' . $id . '/edit');
        }

        if (!is_valid_email($email)) {
            set_flash('error', 'Ongeldig e-mailadres');
            redirect('/users/' . $id . '/edit');
        }

        // Check if email is taken by another user
        $existing = $this->user_model->by_email($email);
        if ($existing && $existing['id'] !== $id) {
            set_flash('error', 'Dit e-mailadres is al in gebruik');
            redirect('/users/' . $id . '/edit');
        }

        try {
            $data = [
                'name' => $name,
                'email' => $email,
                'is_admin' => $is_admin
            ];
            
            $this->user_model->update($id, $data);
            set_flash('success', 'Gebruiker bijgewerkt');
            redirect('/users');
        } catch (\Exception $e) {
            set_flash('error', 'Fout bij bijwerken');
            redirect('/users/' . $id . '/edit');
        }
    }

    /**
     * Delete user
     */
    public function delete($id)
    {
        $this->auth_required();
        $this->admin_required();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/users');
        }

        $user = $this->user_model->find($id);
        
        if (!$user) {
            set_flash('error', 'Gebruiker niet gevonden');
            redirect('/users');
        }

        // Prevent deleting self
        if ($id == auth_id()) {
            set_flash('error', 'U kunt uzelf niet verwijderen');
            redirect('/users');
        }

        try {
            $this->user_model->delete($id);
            set_flash('success', 'Gebruiker verwijderd');
            redirect('/users');
        } catch (\Exception $e) {
            set_flash('error', 'Fout bij verwijderen');
            redirect('/users');
        }
    }

    /**
     * Change password (self)
     */
    public function change_password()
    {
        $this->auth_required();
        $this->render('users/change_password');
    }

    /**
     * Update password
     */
    public function update_password()
    {
        $this->auth_required();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/change-password');
        }

        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            set_flash('error', 'Alle velden zijn verplicht');
            redirect('/change-password');
        }

        $user = $this->user_model->find(auth_id());

        if (!password_verify($current_password, $user['password'])) {
            set_flash('error', 'Huidige wachtwoord is onjuist');
            redirect('/change-password');
        }

        if (strlen($new_password) < 6) {
            set_flash('error', 'Nieuw wachtwoord moet minstens 6 karakters zijn');
            redirect('/change-password');
        }

        if ($new_password !== $confirm_password) {
            set_flash('error', 'Wachtwoorden komen niet overeen');
            redirect('/change-password');
        }

        try {
            $this->user_model->update(auth_id(), [
                'password' => password_hash($new_password, PASSWORD_DEFAULT)
            ]);
            set_flash('success', 'Wachtwoord gewijzigd');
            redirect('/dashboard');
        } catch (\Exception $e) {
            set_flash('error', 'Fout bij wijzigen wachtwoord');
            redirect('/change-password');
        }
    }

    /**
     * Check admin privilege
     */
    private function admin_required()
    {
        if (!auth_user()['is_admin']) {
            http_response_code(403);
            die('Geen toestemming. Alleen beheerders hebben toegang.');
        }
    }
}
