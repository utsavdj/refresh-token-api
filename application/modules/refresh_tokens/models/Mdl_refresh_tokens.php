<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Mdl_refresh_tokens extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'refresh_tokens';
    }

    public function token_exists($token){
        $this->db->select('refresh_token', false);
        $this->db->from($this->table_name);
        $this->db->where('refresh_token', $token);
        $result = $this->db->get();

        if ($result->num_rows() > 0) {
            return true;
        }

        return false;
    }

    public function is_token_valid($token_data){
        $this->db->select(array('DATE_ADD(created_on, INTERVAL '.$this->config->item('token_expiration_time').') 
        AS expires_on', 'NOW() AS now'));
        $this->db->from($this->table_name);
        $this->db->where('refresh_token', $token_data['refresh_token']);
        $this->db->where('token', $token_data['token']);
        $this->db->where('ip_address', $token_data['ip_address']);
        $this->db->where('user_agent', $token_data['user_agent']);
        $this->db->where('host_address', $token_data['host_address']);
        $result = $this->db->get();

        if($result->num_rows() > 0){
            $result = $result->result()[0];
            if ($result->now < $result->expires_on) {
                return true;
            }
        }

        return false;
    }

    public function delete_token($refresh_token){
        $this->db->where('refresh_token', $refresh_token);
        return $this->db->delete($this->table_name);
    }

    public function delete_expired_tokens(){
        $this->db->where('NOW() > DATE_ADD(created_on, INTERVAL '.$this->config->item('token_expiration_time').')');
        return $this->db->delete($this->table_name);
    }

}
