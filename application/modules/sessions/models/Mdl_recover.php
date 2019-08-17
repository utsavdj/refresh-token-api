<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Mdl_recover extends MY_Model {

    function validate_recover($obj=NULL) {

        $obj->form_validation->set_error_delimiters('', '');

        $obj->form_validation->set_rules('username', $this->lang->line('username'), 'required|callback_check_email_verified');
        return parent::validate($this);

    }

    public function validate($obj = NULL) {
        
       // $this->load->library('form_validation');      
        $obj->form_validation->set_error_delimiters('', '');        
        $obj->form_validation->set_rules('password', $this->lang->line('password'), 'required|md5');
        $obj->form_validation->set_rules('confirm_password', $this->lang->line('confirm_password'), 'required|md5|matches[password]');
        
        return parent::validate($this);
        
    }

    function check_email_verified($username){

        $this->db->where('username', $username);
        $query = $this->db->get('sst_users');
        $user = $query->row();

        if ($query->num_rows()==0) {
            $this->form_validation->set_message('check_email_verified', $this->lang->line('user_not_exist'));
            return false;
        }
        elseif($user->email_verified == 1)
        {
            return true;
        }
        else{
            $this->form_validation->set_message('check_email_verified', $this->lang->line('user_not_verified'));
            return false;
        }
    }

    function recover_password($username) {

        $this->db->where('username', $username);

        $query = $this->db->get('sst_users');

        if ($query->num_rows()) {

            $this->load->library('email');
            $this->load->helper('string');
           // $this->load->helper('auth_helper');
            $this->load->helper('auth');
            $this->load->model('users/mdl_email_log');
            $this->load->model('mcb_data/mdl_mcb_data');

            $config = array();
            $config['protocol'] = 'smtp';
            $config['smtp_host'] = 'email-smtp.us-east-1.amazonaws.com';
            $config['smtp_user'] = 'AKIAV6QLUNMOEOR5R6MT';
            $config['smtp_pass'] = 'BJ00LuaPUse2ZBT2jGapz3DJYm53Fki7iCtc9Ksu2mf5';
            $config['smtp_crypto'] = 'tls';
            $config['smtp_port'] = '587';
            $config['mailtype']='html';
            $config['charset']='utf-8';
            
            $this->email->initialize($config);
            $this->email->set_newline("\r\n");
            $user = $query->row();

            if ($user->username) {

                $password = random_string('alnum', 8);

                $verification_code = md5( rand(0,1000) );
                //Generate random 32 character hash and assign it to a local variable.
                //Example output: f4552671f8909587cf485ea990207f3b

                $this->db->where('user_id', $user->user_id);
                //$this->db->set('password', md5_encrypt($password, $user->username ));
                $this->db->set('password_reset_code', $verification_code);
                $this->db->set('password_reset_code_ct',date("Y-m-d H:i:s"));
                //$this->db->set('last_pass_chg_date', date("Y-m-d H:i:s") );
                //$this->db->set('last_pass_chg_by', $user->user_id );
                //$this->db->set('last_login', date("Y-m-d H:i:s"));
               // $this->db->set('status', 1);
                $this->db->update('sst_users');

                $from_name = $this->mdl_mcb_data->get('email_from_name');
                $from_email = $this->mdl_mcb_data->get('email_from_email');
                $to = $user->username;
                $subject = $this->mdl_mcb_data->get('email_subject_recovery');
                $data = array('verification_link'=>$this->mdl_mcb_data->get('verification_link'),'password_reset_code' => $verification_code,'username'=>$user->username);
                $email_body = $this->load->view('sessions/email_templates/user/email_verification',$data,true);
                $this->mdl_mcb_data->set_session_data();


                $this->email->from($from_email, $from_name);
                $this->email->to($to);
                $this->email->subject($subject);
                $this->email->message($email_body);


                $this->load->library('user_agent');
                /***********/
                $email_data = array(
                    'email_receipient'=>$to,
               //     'email_sender_session'=>session_id(),
                    'email_module'=>'password_recover',
                    'email_sent_by'=>$user->user_id,
                    'user_agent'    => $this->agent->agent_string(),
                    'user_ip'=>$this->input->ip_address(),
                    'email_sent_ts'=>date('Y-m-d H:i:s')
                );
                try{
                    if ($this->email->send()) {

                        $email_data['email_status'] = 'SENT';
                        $error = 'Sent!';
                    } else {
                        $error =  strip_tags($this->email->print_debugger());
                        $email_data['email_status'] = 'FAILED';
                        $email_data['error_message'] = $error;

                    }
                }catch(Exception $ex){
                    $email_data['email_status'] = 'FAILED';
                    $email_data['error_message'] = $ex->getMessage();
                }
                $this->mdl_email_log->save($email_data);
                if($email_data['email_status']=='FAILED'){
                    $email_data['error_message'] = $this->lang->line('unable_to_send_email');
                }
                return $email_data;
            }else{
                return false;
            }

        }

    }


    public function updatePassword($data,$password_reset_code)
    {
        $this->db->where('password_reset_code',$password_reset_code);
        return $this->db->update($this->mdl_users->table_name,$data);

    }

}

?>