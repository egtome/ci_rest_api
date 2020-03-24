<?php

class Api_response extends CI_Model {
    public $api_request_id;
    public $solution_one;
    public $solution_two;

    public function insert_request(){
        $this->db->insert('api_responses', $this);
    }

    public function get_previous(){
        $this->db->select('solution_one,solution_two');
        $this->db->from('api_responses');
        $this->db->where('api_request_id', $this->api_request_id);

        $query = $this->db->get();

        if ($query && $query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }                  
    }      

}