<?php
use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Member_Controller extends MX_Controller
{
    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }

    public $token_data;

    public function __construct()
    {
        parent::__construct();
        $this->__resTraitConstruct();

        $headers = $this->input->get_request_header('Authorization');
        $token= $this->input->get_request_header('token');
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers , $matches)) {
                $token = $matches[1];
            }
        }
        $refresh_token= $this->input->get_request_header('refresh_token');;

        try {
            $this->load->model(array('refresh_tokens/mdl_refresh_tokens'));
            $this->load->library('jwt_auth');
            $token_data['token'] = $token;
            $token_data['refresh_token'] = $refresh_token;
            $token_data['ip_address'] = $_SERVER['REMOTE_ADDR'];
            $token_data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            $token_data['host_address'] = $_SERVER['HTTP_HOST'];
            if($this->mdl_refresh_tokens->is_token_valid($token_data)){
                $this->mdl_refresh_tokens->delete_token($refresh_token);
                $this->load->library('jwt_auth');
                $user_data = $this->session->get_userdata();
                $token_data['token'] = $this->jwt_auth->encode($user_data['id'], $user_data['email'])['token'];
                $this->load->library('guid');
                $token_data['refresh_token'] = $this->guid->generate();
                while($this->mdl_refresh_tokens->token_exists($token_data['refresh_token'])){
                    $token_data['refresh_token'] = $this->guid->generate();
                }
                $this->mdl_refresh_tokens->save($token_data);
                $this->token_data['token'] = $token_data['token'];
                $this->token_data['refresh_token'] = $token_data['refresh_token'];
                $this->user_data = $this->jwt_auth->decode($this->token_data['token']);
            }else{
                $this->user_data = $this->jwt_auth->decode($token);
            }

        } catch (Exception $e) {
            $invalid = array('status'=>FALSE,'message' => $e->getMessage()); //Respon if credential invalid
            $this->response($invalid, 401);//401
        }
    }

}

?>