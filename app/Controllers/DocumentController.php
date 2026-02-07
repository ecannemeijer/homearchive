<?php

namespace App\Controllers;

use App\Controller;
use App\Models\Document;
use App\Models\Subscription;

class DocumentController extends Controller
{
    private $document_model;
    private $subscription_model;

    public function __construct()
    {
        $this->document_model = new Document();
        $this->subscription_model = new Subscription();
    }

    /**
     * Upload document
     */
    public function upload()
    {
        $this->auth_required();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Ongeldige request'], 400);
        }

        $sub_id = (int)($_POST['subscription_id'] ?? 0);

        // Check of subscription bestaat
        $subscription = $this->subscription_model->find($sub_id);
        if (!$subscription) {
            $this->json(['error' => 'Abonnement niet gevonden'], 403);
        }

        if (!isset($_FILES['file'])) {
            $this->json(['error' => 'Geen bestand geüpload'], 400);
        }

        $config = config('upload');
        $validation = validate_file_upload($_FILES['file'], $config['max_size'], $config['allowed_types']);

        if (!empty($validation['error'])) {
            $this->json(['error' => $validation['error']], 400);
        }

        // Genereer unieke bestandsnaam
        $unique_name = generate_unique_filename($_FILES['file']['name']);
        $upload_dir = $config['dir'] . '/subscription_' . $sub_id;
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_path = $upload_dir . '/' . $unique_name;

        if (!move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
            $this->json(['error' => 'Fout bij uploaden'], 500);
        }

        // Opslaan in database
        $data = [
            'subscription_id' => $sub_id,
            'user_id' => 6,
            'filename' => $unique_name,
            'original_filename' => $_FILES['file']['name'],
            'file_type' => strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION)),
            'file_size' => $_FILES['file']['size'],
            'file_path' => $file_path
        ];

        try {
            $doc_id = $this->document_model->create($data);
            $this->json([
                'success' => true,
                'id' => $doc_id,
                'message' => 'Document geüpload'
            ]);
        } catch (\Exception $e) {
            unlink($file_path);
            $this->json(['error' => 'Fout bij opslaan in database'], 500);
        }
    }

    /**
     * Download document
     */
    public function download($id)
    {
        $this->auth_required();

        $document = $this->document_model->find_for_user($id);

        if (!$document) {
            set_flash('error', 'Document niet gevonden');
            redirect('/subscriptions');
        }

        if (!file_exists($document['file_path'])) {
            set_flash('error', 'Bestand niet gevonden op server');
            redirect('/subscriptions');
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $document['original_filename'] . '"');
        readfile($document['file_path']);
        exit;
    }

    /**
     * Delete document
     */
    public function delete($id)
    {
        $this->auth_required();

        $document = $this->document_model->find_for_user($id);

        if (!$document) {
            $this->json(['error' => 'Document niet gevonden'], 404);
        }

        try {
            if (file_exists($document['file_path'])) {
                unlink($document['file_path']);
            }
            
            $this->document_model->delete($id);
            $this->json(['success' => true, 'message' => 'Document verwijderd']);
        } catch (\Exception $e) {
            $this->json(['error' => 'Fout bij verwijderen'], 500);
        }
    }

    /**
     * Preview document (PDF of afbeelding)
     */
    public function preview($id)
    {
        $this->auth_required();
        $user_id = auth_id();

        $document = $this->document_model->find_for_user($id, $user_id);

        if (!$document) {
            set_flash('error', 'Document niet gevonden');
            redirect('/subscriptions');
        }

        if (!is_previewable_file($document['file_type'])) {
            set_flash('error', 'Dit bestandstype kan niet worden geopend');
            redirect('/subscriptions');
        }

        if (!file_exists($document['file_path'])) {
            set_flash('error', 'Bestand niet gevonden');
            redirect('/subscriptions');
        }

        $mime_types = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif'
        ];

        header('Content-Type: ' . ($mime_types[$document['file_type']] ?? 'application/octet-stream'));
        readfile($document['file_path']);
        exit;
    }
}
