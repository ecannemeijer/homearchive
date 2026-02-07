<?php

namespace App\Controllers;

use App\Controller;
use App\Models\User;

class AuthController extends Controller
{
    private $user_model;

    public function __construct()
    {
        $this->user_model = new User();
    }

    /**
     * Login pagina
     */
    public function login()
    {
        if (is_logged_in()) {
            redirect('/dashboard');
        }
        
        $flash = get_flash();
        echo $this->view('auth/login', ['flash' => $flash]);
    }

    /**
     * Handle login form
     */
    public function handle_login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/login');
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Debug logging
        error_log('LOGIN_ATTEMPT: ' . $email . ' | Time: ' . date('Y-m-d H:i:s') . ' | Host: ' . $_SERVER['HTTP_HOST']);

        if (empty($email) || empty($password)) {
            error_log('LOGIN_FAILED: Empty credentials for ' . $email);
            set_flash('error', 'Email en wachtwoord zijn verplicht');
            redirect('/login');
        }

        if (!is_valid_email($email)) {
            error_log('LOGIN_FAILED: Invalid email format: ' . $email);
            set_flash('error', 'Ongeldig e-mailadres');
            redirect('/login');
        }

        if (!$this->user_model->verify_password($email, $password)) {
            error_log('LOGIN_FAILED: Invalid password for ' . $email);
            set_flash('error', 'Ongeldige inloggegevens');
            redirect('/login');
        }

        $user = $this->user_model->by_email($email);
        
        if (!$user) {
            error_log('LOGIN_FAILED: User not found after verification: ' . $email);
            set_flash('error', 'Gebruiker niet gevonden');
            redirect('/login');
        }
        
        error_log('LOGIN_SUCCESS: ' . $email . ' (ID: ' . $user['id'] . ')');
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user'] = $user;

        set_flash('success', 'Welkom terug ' . $user['name']);
        redirect('/dashboard');
    }

    /**
     * Register pagina
     */
    public function register()
    {
        if (is_logged_in()) {
            redirect('/dashboard');
        }
        
        $flash = get_flash();
        echo $this->view('auth/register', ['flash' => $flash]);
    }

    /**
     * Handle register form
     */
    public function handle_register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/register');
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        // Validatie
        if (empty($name) || empty($email) || empty($password)) {
            set_flash('error', 'Alle velden zijn verplicht');
            redirect('/register');
        }

        if (strlen($password) < 6) {
            set_flash('error', 'Wachtwoord moet minstens 6 karakters zijn');
            redirect('/register');
        }

        if ($password !== $password_confirm) {
            set_flash('error', 'Wachtwoorden komen niet overeen');
            redirect('/register');
        }

        if (!is_valid_email($email)) {
            set_flash('error', 'Ongeldig e-mailadres');
            redirect('/register');
        }

        // Controleer of email al bestaat
        if ($this->user_model->by_email($email)) {
            set_flash('error', 'Dit e-mailadres is al in gebruik');
            redirect('/register');
        }

        // Maak gebruiker aan
        try {
            $this->user_model->register($name, $email, $password);
            set_flash('success', 'Account aangemaakt! U kunt nu inloggen');
            redirect('/login');
        } catch (\Exception $e) {
            set_flash('error', 'Er is een fout opgetreden bij registratie');
            redirect('/register');
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        logout();
        set_flash('success', 'U bent uitgelogd');
        redirect('/login');
    }
}
