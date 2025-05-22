<?php
/**
 * Testimonial Model
 * 
 * Handles testimonial related database operations
 */
class TestimonialModel extends Model {
    /**
     * Get all testimonials
     * @return array Testimonials
     */
    public function getAllTestimonials() {
        $this->db->query("SELECT * FROM Testimonial ORDER BY Date DESC");
        return $this->db->resultSet();
    }
    
    /**
     * Get all approved testimonials
     * @return array Testimonials
     */
    public function getApprovedTestimonials() {
        $this->db->query("SELECT * FROM Testimonial WHERE Approved = 1 ORDER BY Date DESC");
        return $this->db->resultSet();
    }
    
    /**
     * Get all pending testimonials
     * @return array Testimonials
     */
    public function getPendingTestimonials() {
        $this->db->query("SELECT * FROM Testimonial WHERE Approved = 0 ORDER BY Date DESC");
        return $this->db->resultSet();
    }
    
    /**
     * Add new testimonial
     * @param array $data Testimonial data
     * @return boolean Success status
     */
    public function addTestimonial($data) {
        $this->db->query("INSERT INTO Testimonial (Name, Email, Rating, Text, Date, Approved) 
                          VALUES (:name, :email, :rating, :text, :date, :approved)");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':rating', $data['rating']);
        $this->db->bind(':text', $data['text']);
        $this->db->bind(':date', date('Y-m-d H:i:s'));
        $this->db->bind(':approved', 0); // Default to not approved
        
        return $this->db->execute();
    }
    
    /**
     * Approve testimonial
     * @param int $id Testimonial ID
     * @return boolean Success status
     */
    public function approveTestimonial($id) {
        $this->db->query("UPDATE Testimonial SET Approved = 1 WHERE TestimonialNo = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    /**
     * Delete testimonial
     * @param int $id Testimonial ID
     * @return boolean Success status
     */
    public function deleteTestimonial($id) {
        $this->db->query("DELETE FROM Testimonial WHERE TestimonialNo = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    /**
     * Get testimonial by ID
     * @param int $id Testimonial ID
     * @return object Testimonial or false if not found
     */
    public function getTestimonialById($id) {
        $this->db->query("SELECT * FROM Testimonial WHERE TestimonialNo = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
}
