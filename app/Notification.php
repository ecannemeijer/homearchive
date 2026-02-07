<?php

namespace App;

class Notification
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Maak notificatie aan
     */
    public function create($user_id, $type, $title, $message, $subscription_id = null)
    {
        $data = [
            'user_id' => $user_id,
            'subscription_id' => $subscription_id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'is_read' => 0
        ];

        return $this->db->insert('notifications', $data);
    }

    /**
     * Stuur notificatie voor items die verlopen
     */
    public function check_expiring($user_id, $days = 7)
    {
        $sql = 'SELECT * FROM subscriptions 
                WHERE user_id = :user_id 
                AND is_active = 1
                AND end_date IS NOT NULL 
                AND DATEDIFF(end_date, CURDATE()) BETWEEN 0 AND :days';

        $items = $this->db->select($sql, ['user_id' => $user_id, 'days' => $days]);

        foreach ($items as $item) {
            $days_left = (int)(new \DateTime())->diff(new \DateTime($item['end_date']))->format('%d');
            $title = $item['name'] . ' verloopt binnenkort';
            $message = 'Uw ' . strtolower($item['type'] === 'subscription' ? 'abonnement' : 'verzekering') .
                      ' verloopt over ' . $days_left . ' dag' . ($days_left !== 1 ? 'en' : '');

            $this->create($user_id, 'expiring', $title, $message, $item['id']);
        }
    }

    /**
     * Get ongelezen notificaties
     */
    public function unread($user_id)
    {
        $sql = 'SELECT * FROM notifications 
                WHERE user_id = :user_id AND is_read = 0
                ORDER BY created_at DESC';

        return $this->db->select($sql, ['user_id' => $user_id]);
    }

    /**
     * Mark as read
     */
    public function mark_read($id, $user_id)
    {
        $this->db->update('notifications', 
            ['is_read' => 1], 
            'id = :id AND user_id = :user_id',
            ['id' => $id, 'user_id' => $user_id]
        );
    }
}
