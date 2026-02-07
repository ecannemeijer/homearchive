<?php

namespace App\Models;

use App\Model;

class User extends Model
{
    protected $table = 'users';

    /**
     * Vind gebruiker op email
     */
    public function by_email($email)
    {
        return $this->findWhere('email', $email);
    }

    /**
     * Create nieuwe gebruiker
     */
    public function register($name, $email, $password)
    {
        $data = [
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];
        return $this->create($data);
    }

    /**
     * Verify wachtwoord
     */
    public function verify_password($email, $password)
    {
        $user = $this->by_email($email);
        if (!$user) {
            return false;
        }
        return password_verify($password, $user['password']);
    }
}
