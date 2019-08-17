<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends Member_Controller
{

//    private $count;
//
////    private $memberid;
    function __construct()
    {
        parent::__construct();
    }
//        parent::__construct();
//        $this->load->helper('auth');
//        $this->load->model(array('users/mdl_users,mcb_data/mdl_mcb_data'));
//        $this->load->language("users", $this->mdl_mcb_data->setting('default_language'));
////        $this->memberid=str_pad($this->user_data->user_id, 9, '0', STR_PAD_LEFT);
////        $this->count=$this->_initMembercount();
//    }
//
//    public function users_list_post()
//    {
//        if (!check_access('viewlist', 'users', $this)) {
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 1,
//                'message' => sprintf($this->lang->line('permission_denied'), $this->lang->line('viewlist'))
//            ], REST_Controller::HTTP_OK);
//        }
//
//
//        $users = $this->mdl_users->getUsers();
//        $data = array('users' => $users, 'per_page' => $this->mdl_mcb_data->get('per_page'));
//        if (!empty($users)) {
//            foreach ($users as $key => $user) {
//                $this->load->helper('auth');
//                $users[$key]->first_name = md5_decrypt($user->first_name, $user->username);
//                $users[$key]->last_name = md5_decrypt($user->last_name, $user->username);
//            }
//            // Set the response and exit
//            $this->response(
//                [
//                    'status' => TRUE,
//                    'status_code' => 0,
//                    //'menus'=>$this->menus,
//                    'data' => $users,
//                    'message' => ''
//                ]
//                , REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
//        } else {
//            // Set the response and exit
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 0,
//                //'menus'=>$this->menus,
//                'data' => array(),
//                'message' => $this->lang->line('records_not_found')
//            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
//        }
//
//    }

    public function user_add_post()
    {
        $this->response([
                    'status' => TRUE,
                    'status_code' => 0,
                    'user_data' => $this->token_data,
                    'message' => $this->lang->line('this_record_has_been_saved')
                ], 200);

//        if (!check_access('add', 'users', $this)) {
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 1,
//                'message' => sprintf($this->lang->line('permission_denied'), $this->lang->line('add'))
//            ], REST_Controller::HTTP_OK);
//        }
        $verification_code = md5(rand(0, 1000));
//        $data = array(
//            'username' => trim(strtolower($this->post('username'))),
//            'password' => md5_encrypt($this->input->post('password'), strtolower($this->input->post('username'))),
//            "phone_number" => $this->post('phone_number'),
//            "dob" => md5_encrypt($this->post('dob'), $this->post('username')),
//            "first_name" => md5_encrypt($this->post('first_name'), $this->post('username')),
//            "middle_name" => $this->post('middle_name'),
//            "last_name" => md5_encrypt($this->post('last_name'), $this->post('username')),
//            "marital_status" => $this->post('marital_status'),
//            "retirement_age" => (int)$this->post('retirement_age'),
//            "occupation" => $this->post('occupation'),
//            "street1" => $this->post('street1'),
//            "street2" => $this->post('street2'),
//            "city" => $this->post('city'),
//            "state" => $this->post('state'),
//            "zipcode" => $this->post('zipcode')
//        );
//        $data['status'] = (int)$this->post('status');
//        $data['usergroup_id'] = (int)$this->post('usergroup_id');
////        $password = $this->input->post('password');
//        if ($this->mdl_users->validate_admin($this, 'add')) {
//            if ($this->post('usergroup_id') == 1) {
//                $data["global_admin"] = 1;
//            } else {
//                $data["global_admin"] = 0;
//            }
//            $data['email_verification_code'] = $verification_code;
//            $data['created_by'] = $this->user_data->user_id;
//            $data['created_ts'] = date('Y-m-d H:i:s');
//            $data['modified_by'] = $this->user_data->user_id;
//            $data['modified_ts'] = date('Y-m-d H:i:s');
//            $data['email_verification_code_ct'] = date('Y-m-d H:i:s');
//            if (!$this->mdl_mcb_data->get('sendemail')) {
//                $data['email_verified'] = 1;
//            }
//
//            $result = $this->mdl_users->save($data);
//            if ($result) {
//                $this->load->model('users/mdl_user_family');
//                $spouse_name = $this->post('spouse_name');
//                if ($spouse_name) {
//                    $spouse_data = array(
//                        'name' => $spouse_name,
//                        'relation' => 1,
//                        'user_id' => $result,
//                        'status' => 1
//                    );
//                    $this->mdl_user_family->save(array($spouse_data));
//                }
//
//                $joint_data = array(
//                    'name' => 'Joint',
//                    'relation' => 3,
//                    'user_id' => $result,
//                    'status' => 1
//                );
//                $this->mdl_user_family->save(array($joint_data));
//
//                if ($this->post('child') != null && $this->post('child')) {
//                    $childarray = $this->post('child');
//                    foreach ($childarray as $key => $value) {
//                        if ($childarray[$key]['name'] && $childarray[$key]['dob']) {
//                            $child_data = array();
//                            $child_data[] = array(
//                                'name' => $childarray[$key]['name'],
//                                'relation' => 2,
//                                'user_id' => $result,
//                                'status' => 1,
//                                'dob' => $childarray[$key]['dob']
//                            );
//                            $this->mdl_user_family->save($child_data);
//                        }
//                    }
//                }
//
//                $data['password'] = $this->post('password');
//                if ($this->mdl_mcb_data->get('sendmail')) {
//                    $result = $this->_sendEmail($data, 'add');
//
//                }
//                $this->response([
//                    'status' => TRUE,
//                    'status_code' => 0,
//                    'message' => $this->lang->line('this_record_has_been_saved')
//                ], REST_Controller::HTTP_OK);
//
//            } else {
//                $this->response([
//                    'status' => FALSE,
//                    'status_code' => 0,
//                    'message' => $this->lang->line('this_record_not_saved')
//                ], REST_Controller::HTTP_OK);
//            }
//        } else {
//            $data['password'] = $this->post('password');
//            $data['confirm_password'] = $this->post('confirm_password');
//            $this->response(['status' => FALSE,
//                'data' => $data,
//                'message' => validation_errors()], REST_Controller::HTTP_OK);
//        }

    }

//    public function user_update_post()
//    {
//        if (!check_access('edit', 'users', $this)) {
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 1,
//                'message' => sprintf($this->lang->line('permission_denied'), $this->lang->line('add'))
//            ], REST_Controller::HTTP_OK);
//        }
//
//        $this->load->model('users/mdl_user_family');
//        $user_id = $this->post('user_id');
//        if ($this->post('child_id')) {
//            foreach ($this->mdl_user_family->getChildrenWithDataId($user_id) as $value) {
//                if (!in_array($value, $this->post('child_id'))) {
//                    // Set the response and exit
//                    $this->response([
//                        'status' => FALSE,
//                        'message' => "Couldn't delete child as it has items in networth."
//                    ], REST_Controller::HTTP_UNPROCESSABLE_ENTITY); // NOT_FOUND (404) being the HTTP response code
//                }
//            }
//        }
//
//        $spouse_name = $this->post('spouse_name');
//        if ($this->mdl_user_family->checkMaritalStatusUsage($user_id) > 0 && $this->post('marital_status') == 2) {
//            $this->response([
//                'status' => FALSE,
//                'message' => "Couldn't remove spouse as your spouse has items in networth."
//            ], REST_Controller::HTTP_UNPROCESSABLE_ENTITY); // NOT_FOUND (404) being the HTTP response code
//        } else if ($this->mdl_user_family->checkMaritalStatusUsage($user_id) == 0 && $this->post('marital_status') == 2) {
//            $this->mdl_user_family->removeUserFamily($user_id, 1);
//            $spouse_name = '';
//        }
//        $username = $this->mdl_users->getUsername($user_id);
//        $data = array(
//            "phone_number" => $this->post('phone_number'),
//            "dob" => md5_encrypt($this->post('dob'), $username),
//            "first_name" => md5_encrypt($this->post('first_name'), $username),
//            "middle_name" => $this->post('middle_name'),
//            "last_name" => md5_encrypt($this->post('last_name'), $username),
//            "marital_status" => $this->post('marital_status'),
//            "retirement_age" => (int)$this->post('retirement_age'),
//            "occupation" => $this->post('occupation'),
//            "street1" => $this->post('street1'),
//            "street2" => $this->post('street2'),
//            "city" => $this->post('city'),
//            "state" => $this->post('state'),
//            "zipcode" => $this->post('zipcode')
//        );
//        $data['status'] = (int)$this->post('status');
//        $data['usergroup_id'] = (int)$this->post('usergroup_id');
//
//        if ($this->mdl_users->validate_admin($this, 'edit')) {
//            if ($this->post('password') != NULL && $this->post('password') != '') {
//                $data['password'] = md5_encrypt($this->post('password'), $username);
//            }
//            $data['modified_by'] = $this->user_data->user_id;
//            $data['modified_ts'] = date('Y-m-d H:i:s');
//            $result = $this->mdl_users->save($data, $user_id);
//            if ($result) {
//                if ($spouse_name) {
//                    $spouse_id = $this->post('spouse_id');
//                    $spouse_data = array(
//                        'name' => $this->post('spouse_name'),
//                        'relation' => 1,
//                        'user_id' => $user_id,
//                        'status' => 1
//                    );
//                    if (($spouse_id != "null") && $spouse_id) {
//                        $spouse_data['main_id'] = $spouse_id;
//                    }
//                    $this->mdl_user_family->save(array($spouse_data), $spouse_id);
//                }
//
//                if (!$this->mdl_user_family->hasJoint($user_id)) {
//                    $joint_data = array(
//                        'name' => 'Joint',
//                        'relation' => 3,
//                        'user_id' => $user_id,
//                        'status' => 1
//                    );
//                    $this->mdl_user_family->save(array($joint_data));
//                }
//
//                if ($this->post('child') != null && $this->post('child')) {
//                    $this->mdl_user_family->removeUserFamily($user_id, 2);
//                    $child_data1 = array();
//                    $childarray = $this->post('child');
//                    foreach ($childarray as $key => $value) {
////                      foreach ($value as $k => $v) {
//                        if ($childarray[$key]['name'] && $childarray[$key]['dob']) {
//                            $child_id = $this->post('child_id')[$key];
//                            if ($child_id != "null" && $child_id != "undefined" && $child_id) {
//                                $child_data1[] = array(
//                                    'name' => $childarray[$key]['name'],
//                                    'relation' => 2,
//                                    'user_id' => $user_id,
//                                    'status' => 1,
//                                    'dob' => $childarray[$key]['dob'],
//                                    'main_id' => $child_id
//                                );
//                            } else {
//                                $child_data2 = array();
//                                $child_data2[] = array(
//                                    'name' => $childarray[$key]['name'],
//                                    'relation' => 2,
//                                    'user_id' => $user_id,
//                                    'status' => 1,
//                                    'dob' => $childarray[$key]['dob']
//                                );
//                                $this->mdl_user_family->save($child_data2);
//                            }
//                        }
////                	  }
//                    }
//
//                }
//                if (!empty($child_data1)) {
//                    $this->mdl_user_family->save($child_data1);
//                }
//                $this->response([
//                    'status' => TRUE,
//                    'status_code' => 0,
//                    'message' => $this->lang->line('this_record_has_been_updated')
//                ], REST_Controller::HTTP_OK);
//            } else {
//                $this->response([
//                    'status' => FALSE,
//                    'status_code' => 0,
//                    'message' => $this->lang->line('this_record_could_not_be_updated')
//                ], REST_Controller::HTTP_OK);
//            }
//        } else {
//            $this->response([
//                'status' => FALSE,
//                'data' => $data,
//                'message' => validation_errors()
//            ], REST_Controller::HTTP_OK);
//        }
//    }
//
//    function user_details_post()
//    {
//
//        if (!check_access('view', 'users', $this)) {
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 1,
//                'message' => sprintf($this->lang->line('permission_denied'), $this->lang->line('view'))
//            ], REST_Controller::HTTP_OK);
//        }
//        $this->load->model(array('usergroups/mdl_usergroups', 'mcb_data/mdl_mcb_data', 'users/mdl_user_family'));
//        $user_id = $this->post('user_id');
//        $user_details = $this->mdl_users->userDetails($user_id);
//
//        if ($user_details) {
//            $this->load->helper('auth');
//            $user_details->first_name = md5_decrypt($user_details->first_name, $user_details->username);
//            $user_details->last_name = md5_decrypt($user_details->last_name, $user_details->username);
//            $user_details->dob = md5_decrypt($user_details->dob, $user_details->username);
//            $user_details->password = '';
//            $this->response([
//                'status' => TRUE,
//                'status_code' => 0,
//                'data' => $user_details,
//                'message' => ''
//            ], REST_Controller::HTTP_OK);
//        } else {
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 0,
//                'message' => $this->lang->line('records_not_found')
//            ], REST_Controller::HTTP_OK);
//        }
//    }
//
//    function user_delete_post()
//    {
//        $error = array();
//        if (!check_access('delete', 'users', $this)) {
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 1,
//                'message' => sprintf($this->lang->line('permission_denied'), $this->lang->line('delete'))
//            ], REST_Controller::HTTP_OK);
//        }
//        $user_id = $this->post('user_id');
//        if ($user_id == $this->user_data->user_id) {
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 0,
//                'message' => $this->lang->line('cannot_delete_logged_user_account')
//            ], REST_Controller::HTTP_OK);
//        } else {
//            if ($this->mdl_users->checkGlobalUser($this->post('user_id'))) {
//                if (!$this->mdl_users->checkGlobalUser($this->user_data->user_id)) {
//                    $this->response([
//                        'status' => FALSE,
//                        'status_code' => 0,
//                        'message' => $this->lang->line('not_authorized_to_delete')
//                    ], REST_Controller::HTTP_OK);
//                }
//            }
//            $result = $this->mdl_users->delete(array('user_id' => $user_id));
//            if ($result==1) {
//                $this->response([
//                    'status' => true,
//                    'status_code' => 0,
//                    'message' => $this->lang->line('user_account_deleted')
//                ], REST_Controller::HTTP_OK);
//            } else if($result==2){
//                $this->response([
//                    'status' => FALSE,
//                    'status_code' => 0,
//                    'message' => 'Could not delete user as this user has data in networth or cashflow'
//                ], REST_Controller::HTTP_OK);
//            } else {
//                $this->response([
//                    'status' => FALSE,
//                    'status_code' => 0,
//                    'message' => $this->lang->line('user_account_not_deleted')
//                ], REST_Controller::HTTP_OK);
//            }
//        }
//    }
//
//    function change_status_post()
//    {
//        if (!check_access('edit', 'users', $this)) {
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 1,
//                'message' => sprintf($this->lang->line('permission_denied'), $this->lang->line('add'))
//            ], REST_Controller::HTTP_OK);
//        }
//        $user_id = $this->post('user_id');
//        $this->load->model(array('users/mdl_users'));
//        $ustatus = ($this->post('status') == 1) ? "0" : "1";
//        if ($user_id > 0) {
//            $data = array('status' => $ustatus);
//            $result = $this->mdl_users->save($data, $user_id);
//            if ($result) {
//                $this->response([
//                    'status' => TRUE,
//                    'status_code' => 0,
//                    'message' => $this->lang->line('user_status_successfully_updated')
//                ]);
//            } else {
//                $this->response([
//                    'status' => FALSE,
//                    'status_code' => 0,
//                    'message' => $this->lang->line('status_update_fail')
//                ]);
//            }
//        } else {
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 0,
//                'message' => $this->lang->line('status_update_fail')
//            ], REST_Controller::HTTP_OK);
//        }
//    }
//
//    function user_unlock_post()
//    {
//        if (!check_access('unlock', 'users', $this)) {
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 1,
//                'message' => sprintf($this->lang->line('permission_denied'), $this->lang->line('unlock'))
//            ], REST_Controller::HTTP_OK);
//        }
//        $error = array();
//        $user_id = $this->post('user_id');
//        if ($user_id > 0) {
//            $data = array('status' => 1, 'no_failed_logins' => 0);
//            if ($this->mdl_users->save($data, $user_id)) {
//                $this->response([
//                    'status' => TRUE,
//                    'status_code' => 0,
//                    'message' => $this->lang->line('user_unlocked_success')
//                ], REST_Controller::HTTP_OK);
//            } else {
//                $this->response([
//                    'status' => TRUE,
//                    'status_code' => 0,
//                    'message' => $this->lang->line('user_unlocked_failed')
//                ], REST_Controller::HTTP_OK);
//            }
//        } else {
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 0,
//                'message' => $this->lang->line('user_unlocked_failed')
//            ], REST_Controller::HTTP_OK);
//        }
//
//    }
//
//    public
//    function user_check_post()
//    {
//        $this->load->model(array('users/mdl_users'));
//
//        $username = $this->post('username');
//
//        $check = $this->mdl_users->checkUniqueUser($username, 0);
//        if ($check != '') {
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 0,
//                'message' => $this->lang->line('username_taken')
//            ], REST_Controller::HTTP_OK);
//        } else {
//            $this->response([
//                'status' => TRUE,
//                'status_code' => 0,
//                'message' => $this->lang->line('username_available')
//            ], REST_Controller::HTTP_OK);
//        }
//
//    }
//
//    public
//    function get_user_info_post()
//    {
//        if (!check_access('view', 'users', $this)) {
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 1,
//                'message' => sprintf($this->lang->line('permission_denied'), $this->lang->line('view'))
//            ], REST_Controller::HTTP_OK);
//        }
//        $this->load->model(array('users/mdl_users'));
//        $username = $this->post('username');
//
//        $user = $this->mdl_users->getUserInfo($username);
//
//        if (!empty($user)) {
//            $this->load->helper('auth');
//            $user->first_name = md5_decrypt($user->first_name, $user->username);
//            $user->last_name = md5_decrypt($user->last_name, $user->username);
//            $user->dob = md5_decrypt($user->dob, $user->username);
//            $this->response([
//                'status' => TRUE,
//                'status_code' => 0,
//                'data' => $user,
//                'message' => ''
//            ], REST_Controller::HTTP_OK);
//        } else {
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 0,
//                'message' => $this->lang->line('record_not_found')
//            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
//        }
//    }
//
//    function updateuserinfo_post()
//    {
//        if (!check_access('edit', 'users', $this)) {
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 1,
//                'message' => sprintf($this->lang->line('permission_denied'), $this->lang->line('edit'))
//            ], REST_Controller::HTTP_OK);
//        }
//        $this->load->model(array('users/mdl_users', 'users/mdl_user_family'));
//        $user_id = $this->post('user_id');
//        if ($this->post('child_id')) {
//            foreach ($this->mdl_user_family->getChildrenWithDataId($user_id) as $value) {
//                if (!in_array($value, $this->post('child_id'))) {
//                    // Set the response and exit
//                    $this->response([
//                        'status' => FALSE,
//                        'message' => "Couldn't delete child as it has items in networth."
//                    ], REST_Controller::HTTP_UNPROCESSABLE_ENTITY); // NOT_FOUND (404) being the HTTP response code
//                }
//            }
//        }
//
//        $spouse_name = $this->post('spouse_name');
//        if ($this->mdl_user_family->checkMaritalStatusUsage($user_id) > 0 && $this->post('marital_status') == 2) {
//            $this->response([
//                'status' => FALSE,
//                'message' => "Couldn't remove spouse as your spouse has items in networth."
//            ], REST_Controller::HTTP_UNPROCESSABLE_ENTITY); // NOT_FOUND (404) being the HTTP response code
//        } else if ($this->mdl_user_family->checkMaritalStatusUsage($user_id) == 0 && $this->post('marital_status') == 2) {
//            $this->mdl_user_family->removeUserFamily($user_id, 1);
//            $spouse_name = '';
//        }
//
////        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
////        $this->form_validation->set_rules('phone_number', 'Phone Number', 'required');
////        $this->form_validation->set_rules('dob', 'Date of Birth', 'required');
////        $this->form_validation->set_rules('first_name', 'First Name', 'required');
////        $this->form_validation->set_rules('last_name', 'Last Name', 'required');
////        $this->form_validation->set_rules('street1', 'Street1', 'required');
////        $this->form_validation->set_rules('city', 'City', 'required');
//        if (!$this->mdl_users->validate_accountinfo($this)) {
//            $this->response([
//                'status' => FALSE,
//                'message' => $this->form_validation->error_array(),
//            ], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
//        } else {
//            $this->load->helper('auth');
//            $userdata = array(
//                //"username"=>$this->post('email'),
//                "phone_number" => $this->post('phone_number'),
//                "dob" => md5_encrypt($this->post('dob'), $this->user_data->username),
//                "first_name" => md5_encrypt($this->post('first_name'), $this->user_data->username),
//                "middle_name" => $this->post('middle_name'),
//                "last_name" => md5_encrypt($this->post('last_name'), $this->user_data->username),
//                "marital_status" => $this->post('marital_status'),
//                "retirement_age" => (int)$this->post('retirement_age'),
//                "occupation" => $this->post('occupation'),
//                "street1" => $this->post('street1'),
//                "street2" => $this->post('street2'),
//                "city" => $this->post('city'),
//                "state" => $this->post('state'),
//                "zipcode" => $this->post('zipcode'),
//                "email_verified" => 1
//            );
//        }
//        if($this->mdl_users->getUserStep($user_id)->step == 0){
//            $userdata['step'] = 1;
//        }
//        $userdata['status'] = 1;
//        $userdata['usergroup_id'] = 3;
//        $userdata['modified_by'] = (int)$this->user_data->user_id;
//        $userdata['modified_ts'] = date("Y-m-d H:i:s");
//        $success = $this->mdl_users->saveProfile($userdata, $user_id);
//
//        if ($success) {
//            if ($spouse_name) {
//                $spouse_id = $this->post('spouse_id');
//                $spouse_data = array(
//                    'name' => $this->post('spouse_name'),
//                    'relation' => 1,
//                    'user_id' => $user_id,
//                    'status' => 1
//                );
//                if (($spouse_id != "null") && $spouse_id) {
//                    $spouse_data['main_id'] = $spouse_id;
//                }
//                $this->mdl_user_family->save(array($spouse_data), $spouse_id);
//            }
//
//            if (!$this->mdl_user_family->hasJoint($user_id)) {
//                $joint_data = array(
//                    'name' => 'Joint',
//                    'relation' => 3,
//                    'user_id' => $user_id,
//                    'status' => 1
//                );
//                $this->mdl_user_family->save(array($joint_data));
//            }
//
//            if ($this->post('child') != null && $this->post('child')) {
//                $this->mdl_user_family->removeUserFamily($user_id, 2);
//                $child_data1 = array();
//                $childarray = $this->post('child');
//                foreach ($childarray as $key => $value) {
////                      foreach ($value as $k => $v) {
//                    if ($childarray[$key]['name'] && $childarray[$key]['dob']) {
//                        $child_id = $this->post('child_id')[$key];
//                        if ($child_id != "null" && $child_id != "undefined" && $child_id) {
//                            $child_data1[] = array(
//                                'name' => $childarray[$key]['name'],
//                                'relation' => 2,
//                                'user_id' => $user_id,
//                                'status' => 1,
//                                'dob' => $childarray[$key]['dob'],
//                                'main_id' => $child_id
//                            );
//                        } else {
//                            $child_data2 = array();
//                            $child_data2[] = array(
//                                'name' => $childarray[$key]['name'],
//                                'relation' => 2,
//                                'user_id' => $user_id,
//                                'status' => 1,
//                                'dob' => $childarray[$key]['dob']
//                            );
//                            $this->mdl_user_family->save($child_data2);
//                        }
//                    }
////                	  }
//                }
//
//            }
//            if (!empty($child_data1)) {
//                $this->mdl_user_family->save($child_data1);
//            }
//
////
////                if(!empty($data)) {
////                    foreach ($data as $key=>$item) {
//////                        $id = $data[$key]['main_id']?$this->mdl_user_family->getId($data[$key]['main_id']):null;
////                        $this->mdl_user_family->save(array($item));
////                    }
//            $this->response([
//                'status' => True,
//                'message' => "User Information Successfully saved."
//            ], REST_Controller::HTTP_OK);
////                }
//        } else {
//            // Set the response and exit
//            $this->response([
//                'status' => FALSE,
//                'message' => "Couldn't save User Information."
//            ], REST_Controller::HTTP_UNPROCESSABLE_ENTITY); // NOT_FOUND (404) being the HTTP response code
//        }
//    }
//
//    function getStates_post()
//    {
//        $states = $this->mdl_users->getStates();
//        if (!empty($states)) {
//            $this->response([
//                'status' => TRUE,
//                'status_code' => 0,
//                'data' => $states,
//                'message' => ''
//            ], REST_Controller::HTTP_OK);
//        } else {
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 0,
//                'message' => $this->lang->line('record_not_found')
//            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
//        }
//    }
//
//    function getUserStep_post()
//    {
//        if (!check_access('edit', 'users', $this)) {
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 1,
//                'message' => sprintf($this->lang->line('permission_denied'), $this->lang->line('edit'))
//            ], REST_Controller::HTTP_OK);
//        }
//        $id = $this->user_data->user_id;
//        $step = $this->mdl_users->getUserStep($id);
//        if (!empty($step)) {
//            $this->response([
//                'status' => TRUE,
//                'status_code' => 0,
//                'data' => $step->step,
//                'message' => ''
//            ], REST_Controller::HTTP_OK);
//        } else {
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 0,
//                'message' => 'Step not found.'
//            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
//        }
//    }
//
//    function checkMaritalStatusUsage_post()
//    {
//        $this->load->model(array('users/mdl_user_family'));
//        $list = $this->mdl_user_family->checkMaritalStatusUsage($this->user_data->user_id);
//        $this->response(
//            [
//                'status' => TRUE,
//                'data' => $list,
//                'message' => ''
//            ]
//            , REST_Controller::HTTP_OK);
//    }
//
//    function getUserChildren_post()
//    {
//        if (!check_access('view', 'users', $this)) {
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 1,
//                'message' => sprintf($this->lang->line('permission_denied'), $this->lang->line('view'))
//            ], REST_Controller::HTTP_OK);
//        }
//        $this->load->model(array('users/mdl_user_family'));
//        $id = $this->input->post('userid');
//        if (!isset($id)) {
//            $id = $this->user_data->user_id;
//        }
//        $list = $this->mdl_user_family->getUserChildren($id);
//        if (!empty($list)) {
//            $this->response([
//                'status' => TRUE,
//                'status_code' => 0,
//                'data' => $list,
//                'message' => ''
//            ], REST_Controller::HTTP_OK);
//        } else {
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 0,
//                'message' => $this->lang->line('record_not_found')
//            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
//        }
//    }
//
//    function logout_post()
//    {
//
//        $this->load->helper('url');
//
//        $this->session->sess_destroy();
//        $this->response([
//            'status' => TRUE,
//            'status_code' => 0,
//            'message' => $this->lang->line('Logged Out succesfully')
//        ], REST_Controller::HTTP_OK);
//
//    }
//
//    function online_users_post()
//    {
//
//    }
//
//    function notifyemail_post()
//    {
//        if (!check_access('notify', 'users', $this)) {
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 1,
//                'message' => sprintf($this->lang->line('permission_denied'), $this->lang->line('notify'))
//            ], REST_Controller::HTTP_OK);
//        }
//        $success = false;
//        $error = array();
//        $this->load->model(array('users/mdl_users', 'users/mdl_user_family', 'usergroups/mdl_usergroups',
//            'mcb_data/mdl_mcb_data'));
//
//        $user_id = $this->post('user_id');
//
//        $data = $this->mdl_users->userDetails($user_id);
//
//        $verification_code = md5(rand(0, 1000));
//        // Generate random 32 character hash and assign it to a local variable.
//        // Example output: f4552671f8909587cf485ea990207f3b
//
//        $userdata = array(
//            'email_verification_code' => $verification_code,
//            'modified_by' => $this->user_data->user_id,
//            'modified_ts' => date("Y-m-d H:i:s"),
//        );
//        $success = $this->mdl_users->save($userdata, $user_id);
//
//        if ($success) {
//            $emaildata = array(
//                'first_name' => $data->first_name,
//                'last_name' => $data->last_name,
//                'username' => $data->username,
//                'password' => '',
//                'email_address' => $data->username,
//                'email_verification_code' => $verification_code,
//            );
//            $result = $this->_sendEmail($emaildata);
//            if ($result['email_status'] == 'SENT') {
//                $this->response([
//                    'status' => TRUE,
//                    'status_code' => 0,
//                    'message' => $this->lang->line('email_has_been_sent')
//                ], REST_Controller::HTTP_OK);
//            } else {
//                $this->response([
//                    'status' => FALSE,
//                    'status_code' => 0,
//                    'message' => $result['error_message']
//                ], REST_Controller::HTTP_OK);
//            }
//
//        } else {
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 0,
//                'message' => $this->lang->line('email_sent_failure')
//            ], REST_Controller::HTTP_OK);
//        }
//
//    }
//
//    function _sendEmail($userdata, $func = null)
//    {
//        $this->load->model(array('users/mdl_email_log', 'mcb_data/mdl_mcb_data'));
//
//        $this->load->library('parser');
//
//        $this->load->helper('text');
//
//        $to = $userdata['email_address'];
//
//        $this->load->library('email');
//        $config = array();
//        $config['protocol'] = 'smtp';
//        $config['smtp_host'] = 'email-smtp.us-east-1.amazonaws.com';
//        $config['smtp_user'] = 'AKIAV6QLUNMOEOR5R6MT';
//        $config['smtp_pass'] = 'BJ00LuaPUse2ZBT2jGapz3DJYm53Fki7iCtc9Ksu2mf5';
//        $config['smtp_crypto'] = 'tls';
//        $config['smtp_port'] = '587';
//        $config['mailtype'] = 'html';
//        $config['charset'] = 'utf-8';
//
//        $this->email->initialize($config);
//        $this->email->set_newline("\r\n");
//        $userdata['verification_link'] = $this->mdl_mcb_data->get('email_verification_link');
//        $from_name = $this->mdl_mcb_data->get('email_from_name');
//        $from_email = $this->mdl_mcb_data->get('email_from_email');
//        if ($func == 'add') {
//            $subject = $this->mdl_mcb_data->get('email_subject_registration');
//        } else {
//            $subject = $this->mdl_mcb_data->get('email_subject_notifyemail');
//        }
//
//        $message = $this->load->view('email_templates/user/email_verification', $userdata, true);
//        $this->email->from($from_email, $from_name);
//        $this->email->to($to);
//        $this->email->subject($subject);
//        $this->email->message($message);
//        $this->load->library('user_agent');
//        /***********/
//        $email_data = array(
//            'email_receipient' => $to,
//            //     'email_sender_session'=>session_id(),
//            'email_module' => 'user_verification',
//            'email_sent_by' => $this->user_data->user_id,
//            'user_agent' => $this->agent->agent_string(),
//            'user_ip' => $this->input->ip_address(),
//            'email_sent_ts' => date('Y-m-d H:i:s')
//        );
//        $error = '';
//        try {
//            if ($this->email->send()) {
//
//                $email_data['email_status'] = 'SENT';
//                $error = 'Sent!';
//            } else {
//                $error = strip_tags($this->email->print_debugger());
//                $email_data['email_status'] = 'FAILED';
//                $email_data['error_message'] = $error;
//            }
//        } catch (Exception $ex) {
//            $email_data['email_status'] = 'FAILED';
//            $email_data['error_message'] = $ex->getMessage();
//        }
////        if ($email_data['FAILED']) {
////            $email_data['error_message'] = $this->lang->line('unable_to_send_email');
////        }
//        $this->mdl_email_log->save($email_data);
//        return $email_data;
//    }
//
//
//    function user_verify_post()
//    {
//        if (!check_access('user_verify', 'users', $this)) {
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 1,
//                'message' => sprintf($this->lang->line('permission_denied'), $this->lang->line('user_verify'))
//            ], REST_Controller::HTTP_OK);
//        }
//        $user_id = $this->post('user_id');
//        $result = $this->mdl_users->userEmailVerify($user_id);
//        if ($result) {
//            $this->response([
//                'status' => TRUE,
//                'status_code' => 0,
//                'message' => $this->lang->line('user_verified')
//            ], REST_Controller::HTTP_OK);
//        } else {
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 0,
//                'message' => $this->lang->line('user_verified')
//            ], REST_Controller::HTTP_OK);
//        }
//    }
//
//    public
//    function accountinfo_update_post()
//    {
//        if (!check_access('edit', 'users', $this)) {
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 1,
//                'message' => sprintf($this->lang->line('permission_denied'), $this->lang->line('add'))
//            ], REST_Controller::HTTP_OK);
//        }
//
//        $user_id = $this->post('user_id');
//        $data = array(
//            'first_name' => $this->post('first_name'),
//            'middle_name' => $this->post('middle_name'),
//            'last_name' => $this->post('last_name'),
//            'address1' => $this->post('address_1'),
//            'address2' => $this->post('address_2'),
//            'city' => $this->post('city'),
//            'state' => $this->post('state'),
//            'zipcode' => $this->post('zip_code'),
//            'phone_number_pri' => $this->post('phone_number_pri'),
//            'phone_number_alt' => $this->post('phone_number_alt'),
//            'email_address' => $this->post('email_address'),
//            'method_of_contact_pri' => (int)$this->post('preferred_method_of_contact'),
//            'method_of_contact_pref' => (int)$this->post('preffered_secondary_method_of_'),
//        );
//        if ($this->post('page') != 'profile') {
//            $data['step'] = 1;
//        }
//        if ($this->mdl_users->validate_accountinfo($this)) {
//            $data['modified_by'] = $this->user_data->user_id;
//            $data['modified_ts'] = date('Y-m-d H:i:s');
//            $result = $this->mdl_users->save($data, $user_id);
//            if ($result) {
//                $this->response([
//                    'status' => TRUE,
//                    'status_code' => 0,
//                    'message' => $this->lang->line('this_record_has_been_updated')
//                ], REST_Controller::HTTP_OK);
//            } else {
//                $this->response([
//                    'status' => FALSE,
//                    'status_code' => 0,
//                    'message' => $this->lang->line('this_record_could_not_be_updated')
//                ], REST_Controller::HTTP_OK);
//            }
//        } else {
//            $this->response([
//                'status' => FALSE,
//                'data' => $data,
//                'message' => validation_errors()
//            ], REST_Controller::HTTP_OK);
//        }
//    }
//
//    function excel_users_upload_post(){
////        print_r('Currently disabled. Please enable if required.');die();
//        if (!check_access('bulk_upload', 'users', $this)) {
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 1,
//                'message' => sprintf($this->lang->line('permission_denied'), $this->lang->line('bulk_upload'))
//            ], REST_Controller::HTTP_OK);
//        }
//
////        if($this->mdl_users->removeBulkUploadedUsers()){
//            $filename = $this->post('filename');
//            $filePath = FCPATH.'uploads/'.$filename;
//            $this->load->library('excel');
//            $objPHPExcel = PHPExcel_IOFactory::load($filePath);
//
//            //get only the Cell Collection
//            $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
//            //extract to a PHP readable array format
//            $j=2;
//            $i=0;
//            $id=0;
//            $k=0;
//            $l=0;
//            foreach ($cell_collection as $cell) {
//                $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
//                $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
//                $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
//
//                //The header will/should be in row 1 only. of course, this can be modified to suit your need.
//                if ($row == 1) {
//                    switch ($data_value) {
//                        case 'Last Name':
//                            $header[$row][$column]='last_name';
//                            break;
//                        case 'First Name':
//                            $header[$row][$column]='first_name';
//                            break;
//                        case 'Home Phone':
//                            $header[$row][$column]='phone_number';
//                            break;
//                        case 'Email':
//                            $header[$row][$column]='username';
//                            break;
//                        case 'Address 1':
//                            $header[$row][$column]='street1';
//                            break;
//                        case 'Address 2':
//                            $header[$row][$column]='street2';
//                            break;
//                        case 'Address 3':
//                            $header[$row][$column]='street3';
//                            break;
//                        case 'City':
//                            $header[$row][$column]='city';
//                            break;
//                        case 'Country':
//                            $header[$row][$column]='country';
//                            break;
//                        case 'DOB':
//                            $header[$row][$column]='dob';
//                            break;
//                        case 'Gender':
//                            $header[$row][$column]='gender';
//                            break;
//                        case 'Mobile':
//                            $header[$row][$column]='mobile';
//                            break;
//                        case 'State':
//                            $header[$row][$column]='state';
//                            break;
//                        case 'Zip':
//                            $header[$row][$column]='zipcode';
//                            break;
//                        default:
//                            # code...
//                            break;
//                    }
//
//                } else {
//                    $arr_data[$row-1][$header[1][$column]] = trim($data_value);
//
//                    unset($arr_data[$row-1]['street3']);
//                    unset($arr_data[$row-1]['country']);
////                    unset($arr_data[$row-1]['gender']);
//                    unset($arr_data[$row-1]['mobile']);
//                    $this->load->helper('auth');
//
////                    if(isset($arr_data[$row-1]['username']) && isset($arr_data[$row-1]['first_name'])
////                            && isset($arr_data[$row-1]['last_name']) &&$i==0&&$row==$j){
////                        $arr = array('username'=>$arr_data[$row-1]['username'],
////                            'password'=>md5_encrypt($arr_data[$row-1]['username'].'$S913', $arr_data[$row-1]['username']),
////                            'first_name'=>md5_encrypt($arr_data[$row-1]['first_name'], $arr_data[$row-1]['username']),
////                            'last_name'=>md5_encrypt($arr_data[$row-1]['last_name'], $arr_data[$row-1]['username']),
////                            'usergroup_id'=>3,
////                            'created_by'=>$this->user_data->user_id,
////                            'created_ts'=>date("Y-m-d H:i:s"),
////                            'modified_by'=>$this->user_data->user_id,
////                            'modified_ts'=>date("Y-m-d H:i:s"),
////                            'status'=>1,
////                            'email_verified'=>1,
////                            'bulk_upload'=>1
////                        );
////
////                        $id = $this->mdl_users->insertBulkUpload($arr);
////
////                        if(!$id){
////                            $this->response([
////                                'status' => FALSE,
////                                'status_code' => 0,
////                                'message' => 'Failed to bulk upload users'
////                            ], REST_Controller::HTTP_OK);
////                        }
////                        $i++;
////                    }
//
//                    if(isset($arr_data[$row-1]['username'])&&$i==0&&$row==$j){
//                        $arr_data[$row-1]['username'] = strtolower(trim($arr_data[$row-1]['username']));
//                        $password = $this->randomPassword();
//                        $arr = array('username'=>$arr_data[$row-1]['username'],
//                            'password'=>md5_encrypt($password, $arr_data[$row-1]['username']),
//                            'usergroup_id'=>3,
//                            'created_by'=>$this->user_data->user_id,
//                            'created_ts'=>date("Y-m-d H:i:s"),
//                            'modified_by'=>$this->user_data->user_id,
//                            'modified_ts'=>date("Y-m-d H:i:s"),
//                            'status'=>1,
//                            'email_verified'=>1,
//                            'bulk_upload'=>1,
//                            'step'=>1
//                        );
//
//                        $id = $this->mdl_users->insertBulkUpload($arr);
//                        $data = array('user_id'=>$id,
//                            'username'=>strtolower(trim($arr_data[$row-1]['username'])),'password'=>$password,
//                            'created_on'=>date('Y-m-d H:i:s'),'created_by'=>$this->user_data->user_id);
//                        if(!$id || !$this->mdl_users->insertBulkUploadPassword($data)){
//                            $this->response([
//                                'status' => FALSE,
//                                'status_code' => 0,
//                                'message' => 'Failed to bulk upload users'
//                            ], REST_Controller::HTTP_OK);
//                        }
//                        $i++;
//                    }
//
//                    if((!isset($arr_data[$row-1]['street1']))){
//                        $arr_data[$row-1]['street1']='';
//                    }
//                    if((!isset($arr_data[$row-1]['street2']))){
//                        $arr_data[$row-1]['street2']='';
//                    }
//                    if((!isset($arr_data[$row-1]['city']))){
//                        $arr_data[$row-1]['city']='';
//                    }
//                    if((!isset($arr_data[$row-1]['state'])) ){
//                        $arr_data[$row-1]['state']='';
//                    }else if(isset($arr_data[$row-1]['state']) && $header[1][$column]=='state'){
//                        $arr_data[$row-1]['state']=$this->mdl_users->getStateId($arr_data[$row-1]['state']);
//                    }
//                    if((!isset($arr_data[$row-1]['phone_number'])) ){
//                        $arr_data[$row-1]['phone_number']='';
//                    }
//                    if((!isset($arr_data[$row-1]['zipcode']))){
//                        $arr_data[$row-1]['zipcode']='';
//                    }
//
//                    // 10 is no of columns
//                    if(sizeof($arr_data[$row-1])>=10){
//                        if((!isset($arr_data[$row-1]['dob']))){
//                            $arr_data[$row-1]['dob']='';
//                        }
//
//                        if(isset($arr_data[$row-1]['username']) && isset($arr_data[$row-1]['first_name'])
//                            && isset($arr_data[$row-1]['last_name']) && $k==0&&$row==$j){
//
//                            $arr_data[$row-1]['username'] = strtolower(trim($arr_data[$row-1]['username']));
//                            $arr_data[$row-1]['first_name'] = md5_encrypt($arr_data[$row-1]['first_name'], $arr_data[$row-1]['username']);
//                            $arr_data[$row-1]['last_name'] = md5_encrypt($arr_data[$row-1]['last_name'], $arr_data[$row-1]['username']);
////                            if(isset($arr_data[$row-1]['username']) && isset($arr_data[$row-1]['dob']) && $arr_data[$row-1]['dob']) {
////                            }
//                            $k++;
//                        }
//
//                        if(isset($arr_data[$row-1]['username']) && isset($arr_data[$row-1]['dob']) && $arr_data[$row-1]['dob'] && $l==0&&$row==$j){
//                            $arr_data[$row - 1]['dob'] = md5_encrypt(
//                                date('Y-m-d', strtotime($arr_data[$row - 1]['dob'])),
//                                $arr_data[$row - 1]['username']);
//                            $l++;
//                        }
//
//                        if($k && $l){
//                            unset($arr_data[$row-1]['username']);
//                        }
//
////                        if(isset($arr_data[$row-1]['username']) && isset($arr_data[$row-1]['dob'])){
//////                            print_r($arr_data[$row-1]);die();
////                            $arr_data[$row-1]['dob'] = md5_encrypt(
////                                date('Y-m-d', strtotime($arr_data[$row-1]['dob'])),
////                                $arr_data[$row-1]['username']);
//////                            print_r($arr_data[$row-1]['dob']);die();
////                            unset($arr_data[$row-1]['username']);
////
////                        }else{
////                            $arr_data[$row-1]['dob']='';
////                            unset($arr_data[$row-1]['username']);
////                        }
////                        if(isset($arr_data[$row-1]['state'])) {
////                            $arr_data[$row - 1]['state'] = $this->mdl_users->getStateId($arr_data[$row - 1]['state']);
////                        }
////                        if(!$this->mdl_users->updateBulkUpload($arr_data[$row-1],$id)){
////                            $this->mdl_users->updateOnErrorBulkUpload($id);
////                        }
////                        if(isset($arr_data[$row-1]['username'])) {
//                            $this->mdl_users->updateBulkUpload($arr_data[$row - 1], $id);
////                        }
//                    }
//                    if($row!=$j){
//                        $j=$row;
//                        $i=0;
//                        $k=0;
//                        $l=0;
//                    }
//
//                }
//            }
//            $this->response([
//                'status' => TRUE,
//                'status_code' => 0,
//                'message' => 'Users successfully bulk uploaded'
//            ], REST_Controller::HTTP_OK);
////        }else{
////            $this->response([
////                'status' => FALSE,
////                'status_code' => 0,
////                'message' => 'Failed to remove previously bulk uploaded users'
////            ], REST_Controller::HTTP_OK);
////        }
//    }
//
//    public function bulkUploadPasswordUpdate_post(){
//        if (!check_access('bulk_upload', 'users', $this)) {
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 1,
//                'message' => sprintf($this->lang->line('permission_denied'), $this->lang->line('bulk_upload'))
//            ], REST_Controller::HTTP_OK);
//        }
//        $users = $this->mdl_users->getBulkUploadUserId();
//        if($users) {
//            foreach ($users AS $user) {
//                $password = $this->randomPassword();
////                $data = array('user_id'=>$user->user_id,
////                    'username'=>$user->username,'password'=>$password,
////                    'created_on'=>date('Y-m-d H:i:s'),'created_by'=>$this->user_data->user_id);
////                if($this->mdl_users->insertBulkUploadPassword($data)) {
//                if ($this->mdl_users->updateBulkUploadPassword($user->user_id, $password, 'user_password_bulk_upload')) {
//                    $this->load->helper('auth');
//                    $password = md5_encrypt($password, $user->username);
//                    if (!$this->mdl_users->updateBulkUploadPassword($user->user_id, $password, 'sst_users', true)) {
//                        $this->response([
//                            'status' => FALSE,
//                            'status_code' => 0,
//                            'message' => 'Failed to update password for bulk uploaded users'
//                        ], REST_Controller::HTTP_OK);
//                    }
//                }else{
//                    $this->response([
//                        'status' => FALSE,
//                        'status_code' => 0,
//                        'message' => 'Failed to update password for bulk uploaded users'
//                    ], REST_Controller::HTTP_OK);
//                }
//            }
//        }else{
//            $this->response([
//                'status' => FALSE,
//                'status_code' => 0,
//                'message' => 'Failed to update password for bulk uploaded users'
//            ], REST_Controller::HTTP_OK);
//        }
//        $this->response([
//            'status' => FALSE,
//            'status_code' => 0,
//            'message' => 'Password successfully updated for bulk uploaded users'
//        ], REST_Controller::HTTP_OK);
//    }
//
////    function validateDate($date, $format = 'Y-m-d')
////    {
////        $d = DateTime::createFromFormat($format, $date);
////        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
////        return $d && $d->format($format) === $date;
////    }
//
//    private function randomPassword($len = 8) {
//
//        //enforce min length 8
//        if($len < 8)
//            $len = 8;
//
//        //define character libraries - remove ambiguous characters like iIl|1 0oO
//        $sets = array();
//        $sets[] = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
//        $sets[] = 'abcdefghjkmnpqrstuvwxyz';
//        $sets[] = '1234567890';
//        $sets[]  = '!@#$%^*_?';
//
//        $password = '';
//
//        //append a character from each set - gets first 4 characters
//        foreach ($sets as $set) {
//            $password .= $set[array_rand(str_split($set))];
//        }
//
//        //use all characters to fill up to $len
//        while(strlen($password) < $len) {
//            //get a random set
//            $randomSet = $sets[array_rand($sets)];
//
//            //add a random char from the random set
//            $password .= $randomSet[array_rand(str_split($randomSet))];
//        }
//
//        //shuffle the password string before returning!
//        return str_shuffle($password);
//    }

}
