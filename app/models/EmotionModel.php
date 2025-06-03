<?php
/**
 * Emotion Model
 * 
 * Model untuk menangani operasi terkait dataset emosi
 */

class EmotionModel {
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Get all emotion data
     * 
     * @return array
     */
    public function getAllEmotions() {
        $this->db->query("SELECT * FROM dataset_emosi ORDER BY id");
        return $this->db->resultSet();
    }
    
    /**
     * Get emotion by ID
     * 
     * @param int $id Emotion ID
     * @return array
     */
    public function getEmotionById($id) {
        $this->db->query("SELECT * FROM dataset_emosi WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    /**
     * Get emotions by label
     * 
     * @param string $label Emotion label
     * @return array
     */
    public function getEmotionsByLabel($label) {
        $this->db->query("SELECT * FROM dataset_emosi WHERE label = :label");
        $this->db->bind(':label', $label);
        return $this->db->resultSet();
    }
    
    /**
     * Search emotions by text
     * 
     * @param string $text Text to search
     * @return array
     */
    public function searchEmotions($text) {
        $this->db->query("SELECT * FROM dataset_emosi WHERE text LIKE :text");
        $this->db->bind(':text', '%' . $text . '%');
        return $this->db->resultSet();
    }
    
    /**
     * Add new emotion data
     * 
     * @param array $data Emotion data
     * @return int|bool Last insert ID or false
     */
    public function addEmotion($data) {
        $this->db->query("INSERT INTO dataset_emosi (text, label) VALUES (:text, :label)");
        $this->db->bind(':text', $data['text']);
        $this->db->bind(':label', $data['label']);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update emotion data
     * 
     * @param array $data Emotion data
     * @return bool
     */
    public function updateEmotion($data) {
        $this->db->query("UPDATE dataset_emosi SET text = :text, label = :label WHERE id = :id");
        $this->db->bind(':text', $data['text']);
        $this->db->bind(':label', $data['label']);
        $this->db->bind(':id', $data['id']);
        
        return $this->db->execute();
    }
    
    /**
     * Delete emotion data
     * 
     * @param int $id Emotion ID
     * @return bool
     */
    public function deleteEmotion($id) {
        $this->db->query("DELETE FROM dataset_emosi WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    /**
     * Get emotion stats
     * 
     * @return array
     */
    public function getEmotionStats() {
        $this->db->query("SELECT label, COUNT(*) as count FROM dataset_emosi GROUP BY label");
        return $this->db->resultSet();
    }
}
