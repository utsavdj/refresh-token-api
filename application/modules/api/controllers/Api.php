<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends API_Controller
{

    function __construct()
    {
        parent::__construct();
//        $this->load->language("api", $this->mdl_settings_data->setting('default_language'));
        $this->load->language("api", "english");
    }

    public function login_post()
    {
        $this->load->model(array('sessions/mdl_sessions', 'sessions/mdl_auth', 'users/mdl_users',
            'refresh_tokens/mdl_refresh_tokens'));
        if ($this->mdl_sessions->validate()) {
            $result = $this->mdl_auth->auth('email', trim($this->post('email')), $this->post('password'));
            $user = $result['user'];
            $user_data = array();
            if ($result['status']) {
                $this->load->library('jwt_auth');
                $token_data = $this->jwt_auth->encode($user->id, $user->email);
                $this->load->library('guid');
                $token_data['refresh_token'] = $this->guid->generate();
                $this->mdl_refresh_tokens->delete_expired_tokens();
                while($this->mdl_refresh_tokens->token_exists($token_data['refresh_token'])){
                    $token_data['refresh_token'] = $this->guid->generate();
                }
                $user_data = $token_data;
                $token_data['ip_address'] = $_SERVER['REMOTE_ADDR'];
                $token_data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
                $token_data['host_address'] = $_SERVER['HTTP_HOST'];

                $this->mdl_refresh_tokens->save($token_data);
                $object_vars = array('id', 'email');
                // set the session variables
                $this->mdl_auth->set_session($user, $object_vars, array('email' => $user->email, 'is_admin' => TRUE, 'is_loggedin' => TRUE));

                $login_datetime = date("Y-m-d H:i:s");
                // update the last login field for this user
                $this->mdl_auth->update_last_login($user->id, 'last_login', $login_datetime);
            }
            $this->response(array(
                'status' => $result['status'],
                'status_code' => 0,
                "data" => $user,
                'user_data' => $user_data,
                'message' => $result['message']
            ), 200);
        } else {
            $this->response(array(
                'status' => FALSE,
                'status_code' => 0,
                'message' => validation_errors()
            ), 200);
        }
    }

    public function user_registration_post()
    {
        $this->load->model(array('users/mdl_users', 'settings_data/mdl_settings_data'));

        $data = array(
            "email" => $this->post('email'),
            "status" => (int)$this->post('status'),
            "usergroup_id" => (int)$this->mdl_settings_data->get('register_usergroup'),
            'created_ts' => date('Y-m-d:H:i:s'),
            'modified_ts' => date('Y-m-d:H:i:s'),
        );
        $verification_code = md5(rand(0, 1000));
        $password = $this->post('password');
        if (!empty($password)) {
            $data["password"] = crypt($password, '$6$rounds=5000$dpn6fIJvGnVjuSGqo4DL5/gJ47z6MS+jt2Iy8EjGWCY=$');
        }
        if (!$this->mdl_settings_data->get('sendemail')) {
            $data['email_verified'] = 1;
            $success_msg = $this->lang->line('registration_success');
        } else {
            $success_msg = $this->lang->line('registration_success_email_verify');
        }


        if ($this->mdl_users->validate($this)) {
            $data['email_verification_code'] = $verification_code;
            $data['email_verification_code_ct'] = date('Y-m-d H:i:s');
            if ($user_id = $this->mdl_users->save($data)) {
                if ($this->mdl_settings_data->get('sendemail')) {
                    $this->_sendEmail($data);
                }

                $data['created_by'] = $user_id;
                $data['modified_by'] = $user_id;
                $data['action'] = 'add';
                $data['id'] = $user_id;
                if(!$this->mdl_users->save(array('created_by'=>$user_id, 'modified_by'=>$user_id),$user_id) ||
                    !$this->mdl_users->log($data)){
                    $this->response(array(
                        'status' => FALSE,
                        'status_code' => 0,
                        'message' => 'Failed to log registered user '
                    ), 200);
                }
                $this->response(array(
                    'status' => TRUE,
                    'status_code' => 0,
                    'message' => $success_msg
                ), 200);

            } else {

                $this->response(array(
                    'status' => FALSE,
                    'status_code' => 0,
                    'message' => $this->lang->line('registration_failure')
                ), 200);
            }
        } else {
            $this->response(array(
                'status' => FALSE,
                'status_code' => 0,
                'message' => validation_errors()
            ), 200);
        }


    }

    public function check_user_post()
    {
        $this->load->model(array('users/mdl_users'));

        $email = trim($this->post('email'));

        if (!empty($username)) {
            $check = $this->mdl_users->email_check($email, '0');
            if (!$check) {
                $this->response(array(
                    'status' => FALSE,
                    'status_code' => 0,
                    'message' => $this->lang->line('username_taken')
                ), 200);
            } else {
                $this->response(array(
                    'status' => TRUE,
                    'status_code' => 0,
                    'message' => $this->lang->line('username_available')
                ), 200);
            }
        } else {
            $this->response(array(
                'status' => FALSE,
                'status_code' => 0,
                'message' => ''
            ), 200);
        }

    }

    public function get_user_info_post()
    {
        $this->load->model(array('users/mdl_users'));
        $username = $this->post('username');

        $user =  $this->mdl_users->getUserInfo($username);

        if (!empty($user))
        {
            $this->load->helper('auth');
            $user->first_name = md5_decrypt($user->first_name, $user->username);
            $user->last_name = md5_decrypt($user->last_name, $user->username);
            $user->dob = md5_decrypt($user->dob, $user->username);
            $this->response(array(
                'status' => TRUE,
                'status_code'=>0,
                'data' => $user,
                'message' => ''
            ), 200);
        }
        else
        {
            $this->response(array(
                'status' => FALSE,
                'status_code'=>0,
                'message' => $this->lang->line('record_not_found')
            ), 200); // NOT_FOUND (404) being the HTTP response code
        }
    }

    function verification_post()
    {

        $this->load->model(array('users/mdl_users'));
        $this->load->helper(array('url', 'form', 'auth'));
        $verification_code = $this->post('verification_code');
        $response = false;

        $result = $this->mdl_users->verifyUser($verification_code);
        if ($result['status']) {
            $field_data = array(
                'email_verified' => 1,
                'status' => 1,
                'email_verification_code' => '',
            );

            $response = $this->mdl_users->save($field_data, $result['user_id']);
        } else {
            if ($result['message'] == $this->lang->line('email_verification_code_expired')) {
                $verification_code = md5(rand(0, 1000));
                $data['email_verification_code'] = $verification_code;
                $data['email_verification_code_ct'] = date('Y-m-d H:i:s');
                if ($this->mdl_users->updateUserVerificationCode($data, $result['user_id'])) {
                    if ($this->mdl_settings_data->get('sendemail')) {
                        $data['username'] = $result['username'];
                        $this->_sendEmail($data);
                    } else {
                        $this->response(array(
                            'status' => FALSE,
                            'status_code' => 0,
                            'data' => array(),
                            'message' => $this->lang->line('email_verification_code_expired_failed_email_resend')
                        ), 200);
                    }
                } else {
                    $this->response(array(
                        'status' => FALSE,
                        'status_code' => 0,
                        'data' => array(),
                        'message' => $this->lang->line('email_verification_code_expired_failed_email_resend')
                    ), 200);
                }
            }
            $this->response(array(
                'status' => FALSE,
                'status_code' => 0,
                'data' => array(),
                'message' => $result['message']
            ), 200);
        }
        if ($response) {

            $this->response(array(
                'status' => TRUE,
                'status_code' => 0,
                'data' => array(),
                'message' => $this->lang->line('successfully_verified')
            ), 200);
        } else {
            $this->response(array(
                'status' => FALSE,
                'status_code' => 0,
                'message' => $this->lang->line('verification_failed')
            ), 200);
        }
    }

    function reset_password_post()
    {
        $this->load->model(array('users/mdl_users'));
        $this->load->helper(array('url', 'form', 'auth'));
        $password_reset_code = $this->post('verification_code');
        if ($password_reset_code == '' || $password_reset_code == null) {
            $this->response(array(
                'status' => FALSE,
                'status_code' => 0,
                'message' => $this->lang->line('invalid_password_reset_code')
            ), 200);
        } else {
            $result = $this->mdl_users->verifyPasswordCode($password_reset_code);
        }

        if ($result['status']) {
            $this->response(array(
                'status' => TRUE,
                'status_code' => 0,
                'data' => $result['password_reset_code'],
                'message' => ''
            ), 200);
        } else {
            $this->response(array(
                'status' => FALSE,
                'status_code' => 0,
                'message' => $result['message']
            ), 200);
        }
    }

    function new_password_post()
    {
        $this->load->helper('auth');
        $this->load->model(array('sessions/mdl_recover', 'users/mdl_users'));
        $password_reset_code = $this->post('password_reset_code');
        $this->load->language("api", $this->mdl_settings_data->setting('default_language'));
        if ($password_reset_code == '' || $password_reset_code == null) {
            $this->response(array(
                'status' => FALSE,
                'status_code' => 0,
                'message' => $this->lang->line('invalid_password_reset_code')
            ), 200);
        } else {
            $codeverify = $this->mdl_users->verifyPasswordCode($password_reset_code);
        }

        if ($codeverify['status']) {
            if ($this->mdl_recover->validate($this)) {
                $data["password"] = md5_encrypt($this->post('password'), strtolower($codeverify['username']));
                $data['last_pass_chg_by'] = $codeverify['user_id'];
                $data["last_pass_chg_date"] = date("Y-m-d H:i:s");
                $data["password_reset_code"] = '';
                $result = $this->mdl_recover->updatePassword($data, $password_reset_code);
                if ($result) {
                    $this->response(array(
                        'status' => TRUE,
                        'status_code' => 0,
                        'message' => $this->lang->line('password_reset_successfully')
                    ), 200);
                } else {
                    $this->response(array(
                        'status' => FALSE,
                        'status_code' => 0,
                        'message' => $this->lang->line('could_not_reset_password')
                    ), 200);
                }
            } else {
                $this->response(array(
                    'status' => FALSE,
                    'status_code' => 0,
                    'message' => validation_errors()
                ), 200);
            }
        } else {
            $this->response(array(
                'status' => FALSE,
                'status_code' => 0,
                'message' => $codeverify['message']
            ), 200);
        }

    }

    function recover_post()
    {

        $this->load->model('sessions/mdl_recover');

        $this->load->helper(array('url', 'form'));
        $data['username'] = $this->post('username');
        if ($this->mdl_recover->validate_recover($this)) {
            $result = $this->mdl_recover->recover_password(trim(strtolower($this->post('username'))));
            if ($result['email_status'] == "SENT") {
                $this->response(array(
                    'status' => TRUE,
                    'status_code' => 0,
                    'message' => $this->lang->line('password_reset_link_sent')
                ), 200);
            } else {
                $this->response(array(
                    'status' => FALSE,
                    'status_code' => 0,
                    'message' => $result['error_message']
                ), 200);
            }
        } else {
            $this->response(array(
                'status' => FALSE,
                'status_code' => 0,
                'message' => validation_errors()
            ), 200);
        }

    }

    function _sendEmail($userdata)
    {
        $this->load->model(array('users/mdl_email_log', 'mcb_data/mdl_settings_data'));

        $this->load->library('parser');

        $this->load->helper('text');
        $to = $userdata['email'];

        $this->load->library('email');
        $config = array();
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'smtp.gmail.com';
        $config['smtp_user'] = 'utsavdjnew@gmail.com';
        $config['smtp_pass'] = 'udj326788';
        $config['smtp_crypto'] = 'tls';
        $config['smtp_port'] = '587';
        $config['mailtype'] = 'html';
        $config['charset'] = 'utf-8';

        $this->email->initialize($config);
        $this->email->set_newline("\r\n");
        $userdata['verification_link'] = $this->mdl_settings_data->get('email_verification_link');
        $from_name = $this->mdl_settings_data->get('email_from_name');
        $from_email = $this->mdl_settings_data->get('email_from_email');
        $subject = $this->mdl_settings_data->get('email_subject_registration');
        $message = $this->load->view('users/email_templates/user/email_verification', $userdata, true);
        $this->email->from($from_email, $from_name);
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($message);
        $this->load->library('user_agent');
        /***********/
        $email_data = array(
            'email_receipient' => $to,
            //     'email_sender_session'=>session_id(),
            'email_module' => 'user_verification',
            // 'email_sent_by'=>$this->user_data->user_id,
            'user_agent' => $this->agent->agent_string(),
            'user_ip' => $this->input->ip_address(),
            'email_sent_ts' => date('Y-m-d H:i:s')
        );
        $error = '';
        try {
            if ($this->email->send()) {

                $email_data['email_status'] = 'SENT';
                $error = 'Sent!';
            } else {
                $error = $this->lang->line('unable_to_send_email');
                //  $error =  strip_tags($this->email->print_debugger());
                $email_data['email_status'] = 'FAILED';
                $email_data['error_message'] = $error;
            }
        } catch (Exception $ex) {
            $email_data['email_status'] = 'FAILED';
            $email_data['error_message'] = $ex->getMessage();
        }
        $this->mdl_email_log->save($email_data);
        return $error;
    }

    public function template_upload_post(){

        $path = 'uploads/';
        $fileName = $_FILES['file']['name'];
        $uploadData = array();
        if (!empty($fileName)) {
            $config['upload_path'] = $path;
            $config['allowed_types'] = 'xlsx|csv';
            // $config['max_size'] = 2048;
            $config['remove_spaces'] = TRUE;
            // $config['encrypt_name'] = TRUE;
            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if (!$this->upload->do_upload('file')) {
                $this->response(array(
                    'status' => false,
                    'status_code' => 0,
                    'message' => strip_tags($this->upload->display_errors())
                ), 200);
                // json_error("File Upload Error", $this->upload->display_errors(), 400);
            } else {

                $uploadData = array('upload_data' => $this->upload->data());
                if ($uploadData) {
                    $this->response(array(
                        'status' => TRUE,
                        'status_code' => 0
                    ), 200);
                }
            }
        }
    }

}
