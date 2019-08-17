<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Usergroups extends Admin_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->helper('auth');
       	$this->load->model('usergroups/mdl_usergroups');
		$this->load->language("usergroups",  $this->mdl_mcb_data->setting('default_language'));
    }

	 public function usergroup_list_post(){
	 	if ( ! check_access('viewlist','usergroups',$this))
		{

		$this->response([
		'status' => FALSE,
		'status_code'=>1,
		'message' => sprintf($this->lang->line('permission_denied'),$this->lang->line('viewlist'))
		], REST_Controller::HTTP_OK);

		}
		$grouplist=$this->mdl_usergroups->getGrouplist();
         $data = array('user_groups'=>$grouplist,'per_page'=>$this->mdl_mcb_data->get('per_page'));
		if (!empty($grouplist))
        {
            // Set the response and exit
            $this->response(
                [
                    'status' => TRUE,
                    'status_code'=>0,
                    'data'=>$data,
                    'message' => ''
                ]
                , REST_Controller::HTTP_OK);
        }
        else
        {
            // Set the response and exit
            $this->response([
                'status' => FALSE,
                'status_code'=>0,
                'data'=>array(),
                'message' => $this->lang->line('record_not_found')
            ], REST_Controller::HTTP_OK);
        }
		
	}

	function usergroup_create_post(){
	if ( ! check_access('add','usergroups',$this))
        {
            $this->response([
                'status' => FALSE,
                'status_code'=>1,
                'message' => sprintf($this->lang->line('permission_denied'),$this->lang->line('add'))
            ], REST_Controller::HTTP_OK);

        }
		
		$data=array(
						"group_name"=>$this->post('group_name'),
						"group_description"=>$this->post('group_description'),
						"group_code"=>$this->post('group_code'),
						"group_status"=>$this->input->post('group_status'),
						
						);
		if($this->mdl_usergroups->validate($this))
		{
			$data["created_ts"]=date('Y-m-d H:i:s');
			$data["created_by"]=$this->user_data->user_id;
			$result=$this->mdl_usergroups->save($data);
			if($result)
			{
				$this->response(
                [
                    'status' => TRUE,
                    'status_code'=>0,
                    'message' => $this->lang->line('this_record_has_been_saved')
                ], REST_Controller::HTTP_OK);
			}
			else
			{
				$this->response([
                'status' => FALSE,
                'status_code'=>0,
                'data'=>array(),
                'message' => $this->lang->line('this_record_not_saved')
            	], REST_Controller::HTTP_OK);
			}
		}
		else
		{
			$this->response([
                'status' => FALSE,
                'status_code'=>0,
                'data' =>$data,
                'message' => validation_errors()
            ], REST_Controller::HTTP_OK);
		}
		 
	}

	function usergroup_update_post()
	{
		if ( ! check_access('edit','usergroups',$this))
        {
            $this->response([
                'status' => FALSE,
                'status_code'=>1,
                'message' => sprintf($this->lang->line('permission_denied'),$this->lang->line('edit'))
            ], REST_Controller::HTTP_OK);
            

        }
        $group_id=$this->post('group_id');
        $data=array(
						"group_name"=>$this->post('group_name'),
						"group_description"=>$this->post('group_description'),
						"group_code"=>$this->post('group_code'),
						"group_status"=>$this->input->post('group_status'),
						
						);
        if($this->mdl_usergroups->validate($this))
        {
        	if($group_id>0)
        	{
        		$data["modified_ts"]=date('Y-m-d H:i:s');
				$data["modified_by"]=$this->user_data->user_id;
				$result=$this->mdl_usergroups->save($data, $group_id);
				if($result)
				{
					$this->response([
	                'status' => TRUE,
	                'message' => $this->lang->line('this_record_has_been_updated')
	            	], REST_Controller::HTTP_OK);
				}
				else{
					$this->response([
	                'status' => FALSE,
	                'status_code'=>0,
	                'data'=>$data,
	                'message' => $this->lang->line('this_record_not_updated')
	            	], REST_Controller::HTTP_OK);
				}
        	}
        	else
        	{
        		$this->response([
                'status' => FALSE,
                'data'=>$data,
                'message' => $this->lang->line('this_record_not_update')
            	], REST_Controller::HTTP_OK);
        	}
        }
        else
        {
        	$this->response([
                'status' => FALSE,
                'status_code'=>0,
                'data' =>$data,
                'message' => validation_errors()
            ], REST_Controller::HTTP_OK);
        }
        
		
		
	}

	function get_group_post(){
		$group=$this->mdl_usergroups->getGroup($this->post('group_id'));
		if($group!=null){
			$this->response(
                [
                    'status' => TRUE,
                    'status_code'=>0,
                    'data'=>$group,
                    'message' => ''
                ]
                , REST_Controller::HTTP_OK);
		}else{
			    $this->response([
                'status' => FALSE,
                'status_code'=>0,
                'data'=>array(),
                'message' => 'Group not found'
            ], REST_Controller::HTTP_OK);
		}
	}

	function usergroup_delete_post(){
		if ( ! check_access('delete','usergroups',$this))
        {
            $this->response([
                'status' => FALSE,
                'status_code'=>1,
                'message' => sprintf($this->lang->line('permission_denied'),$this->lang->line('delete'))
            ], REST_Controller::HTTP_OK);
        }			
		$group_id=$this->post('group_id');
		if($group_id>0){
			$check_user=$this->db->get_where('sst_users', array('usergroup_id =' => $group_id))->result();
			$total_num=count($check_user);
			$success = false;
			if ($total_num > 0) 
			{
				$this->response([
	                'status' => FALSE,
	                'data'=>array(),
	                'status_code'=>0,
	                'message' => $this->lang->line('user_exists')
	            ], REST_Controller::HTTP_OK);
				
			}
			else 
			{
				
				if($this->mdl_usergroups->delete(array('id'=>$group_id)))
				{
					$this->response([
		                'status' => TRUE,
		                'data'=>array(),
		                'status_code'=>0,
		                'message' => $this->lang->line('this_record_has_been_deleted')
		            ], REST_Controller::HTTP_OK);

				}
				else
				{
					$this->response([
		                'status' => FALSE,
		                'data'=>array(),
		                'status_code'=>0,
		                'message' => $this->lang->line('this_record_not_deleted')
		            ], REST_Controller::HTTP_OK);
				}
			}
		}
		else
		{
			$this->response([
		                'status' => FALSE,
		                'data'=>array(),
		                'status_code'=>0,
		                'message' => $this->lang->line('this_record_not_deleted')
		            ], REST_Controller::HTTP_OK);
		}
		
	}

	function group_options_post(){
		$group_option=$this->mdl_usergroups->getUserGroupOptions();
		if($group_option){
				$this->response([
	                'status' => TRUE,
	                'status_code'=>0,
	                'data'=>$group_option,
	                'message' => ''
	            ], REST_Controller::HTTP_OK);

		}else{
				$this->response([
	                'status' => FALSE,
	                'status_code'=>0,
	                'data'=>array(),
	                'message' => $this->lang->line('record_not_found')
	            ], REST_Controller::HTTP_OK);

		}
	}
    

}