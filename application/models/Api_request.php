<?php

class Api_request extends CI_Model {
    public $remote_address;
    public $token;
    public $a_value;
    public $b_value;
    public $c_value;

    public function insert_request(){
        $this->db->insert('api_requests', $this);
        return $this->db->insert_id();
    }

    public function check_duplicate(){
        $this->db->select('id,request_counter');
        $this->db->from('api_requests');
        $this->db->where('token', $this->token);

        $query = $this->db->get();

        if ($query && $query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }                  
    }    

    public function update_duplicated($id,$counter){
        $data['request_counter'] = $counter;    
        $this->db->where('id', $id);
        $this->db->update('api_requests', $data);   
    }

}