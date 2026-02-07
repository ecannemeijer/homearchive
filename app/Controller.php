<?php

namespace App;

class Controller
{
    protected $view_path = __DIR__ . '/Views/';

    /**
     * Render view
     */
    public function view($view, $data = [])
    {
        // Maak variabelen beschikbaar in view
        extract($data);
        
        $file = $this->view_path . $view . '.php';
        
        if (!file_exists($file)) {
            die('View bestand niet gevonden: ' . $file);
        }
        
        ob_start();
        include $file;
        $content = ob_get_clean();
        
        return $content;
    }

    /**
     * Render JSON
     */
    public function json($data, $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    /**
     * Render layout met view
     */
    public function render($view, $data = [], $layout = 'layout')
    {
        $data['content'] = $this->view($view, $data);
        
        extract($data);
        $file = $this->view_path . $layout . '.php';
        
        if (!file_exists($file)) {
            die('Layout bestand niet gevonden: ' . $file);
        }
        
        include $file;
    }

    /**
     * Controleer authenticatie
     */
    public function auth_required()
    {
        if (!is_logged_in()) {
            set_flash('error', 'U moet ingelogd zijn');
            redirect('/login');
        }
    }
}
