<?php
/**
 * News Model
 * 
 * Handles news related database operations
 */
class NewsModel extends Model {
    /**
     * Get active news
     * @param int $limit Number of news items to return (0 for all)
     * @return array News items
     */
    public function getActiveNews($limit = 0) {
        $sql = "SELECT * FROM News WHERE Active = 1 ORDER BY Date DESC";
        
        if ($limit > 0) {
            $sql .= " LIMIT :limit";
        }
        
        $this->db->query($sql);
        
        if ($limit > 0) {
            $this->db->bind(':limit', $limit);
        }
        
        return $this->db->resultSet();
    }
    
    /**
     * Get latest active news
     * @return object News or false if none found
     */
    public function getLatestNews() {
        $this->db->query("SELECT * FROM News WHERE Active = 1 ORDER BY Date DESC LIMIT 1");
        return $this->db->single();
    }
    
    /**
     * Get all news
     * @return array News items
     */
    public function getAllNews() {
        $this->db->query("SELECT * FROM News ORDER BY Date DESC");
        return $this->db->resultSet();
    }
    
    /**
     * Add new news item
     * @param array $data News data
     * @return boolean Success status
     */
    public function addNews($data) {
        $this->db->query("INSERT INTO News (Title, Text, Date, Active) VALUES (:title, :text, :date, :active)");
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':text', $data['text']);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':active', $data['active'] ? 1 : 0);
        
        return $this->db->execute();
    }
    
    /**
     * Update news item
     * @param array $data News data
     * @return boolean Success status
     */
    public function updateNews($data) {
        $this->db->query("UPDATE News SET Title = :title, Text = :text, Date = :date, Active = :active WHERE NewsNo = :id");
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':text', $data['text']);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':active', $data['active'] ? 1 : 0);
        $this->db->bind(':id', $data['id']);
        
        return $this->db->execute();
    }
    
    /**
     * Delete news item
     * @param int $id News ID
     * @return boolean Success status
     */
    public function deleteNews($id) {
        $this->db->query("DELETE FROM News WHERE NewsNo = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    /**
     * Get news item by ID
     * @param int $id News ID
     * @return object News or false if not found
     */
    public function getNewsById($id) {
        $this->db->query("SELECT * FROM News WHERE NewsNo = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
}
