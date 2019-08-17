<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Mdl_User_family extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->table_name='user_family';
		$this->primary_key = 'user_family.id';
	}

	public function save($data,$id=null,$set_flashdata=false){
        if ($id == "null" || !$id) {
            if($this->db->insert_batch($this->table_name,$data)) {
                $query = 'UPDATE ' . $this->table_name . ' SET "main_id" = "id" WHERE "main_id"=0';
                $result = $this->db->query($query);
                if ($result) {
                    return $this->db->insert_id('digitalsherpadb.user_familay_id_seq');
                } else {
                    return false;
                }
            }else{
                return false;
            }
        }else{
            if($this->db->update_batch($this->table_name, $data, 'main_id')){
                return true;
            }else{
                return false;
            }
        }
	}

	public function removeUserFamily($id, $relation){
        $this->db->where('user_id', $id);
        $this->db->where('relation', $relation);
        return $this->db->delete($this->table_name);
    }

    public function getChildrenWithDataId($user_id){
        $this->load->model(array('networth/mdl_networth'));
        $id = array();
        $this->db->select('"uf"."main_id" AS id');
        $this->db->from('user_family AS uf');
        $this->db->where('uf.user_id', $user_id);
        $this->db->where('"uf"."relation"', 2);
        $result = $this->db->get()->result();
        foreach ($result as $value){
	        if($this->mdl_networth->checkChildUsage($value->id)[0]->count > 0){
                $id[] = $value->id;
            }
        }
        return $id;
    }

    public function hasJoint($user_id){
        $this->db->select('COUNT(main_id)');
        $this->db->from('user_family');
        $this->db->where('user_id', $user_id);
        $this->db->where('relation', 3);
        $result = $this->db->get()->result()[0]->count;
        return $result;
    }

    public function checkMaritalStatusUsage($user_id){
        $this->db->select('COUNT(uf.main_id)');
        $this->db->from('networth_items AS nw');
        $this->db->join('user_family AS uf', 'uf.main_id = nw."owner"', 'left');
        $this->db->where('uf.user_id', $user_id);
        $this->db->where('uf.relation', 1);
        $result = $this->db->get()->result()[0]->count;
        return $result;
    }

    public function getUserFamilyById($id, $relation=null){
        $this->db->select('main_id');
        $this->db->from($this->table_name);
        $this->db->where('user_id', $id);
        $this->db->where('relation', $relation);
        $result = $this->db->get();
        return $result->result();
    }

	public function getFamilyByUserId($user_id)
	{
//		$this->db->select('uf.user_id,uf.name,uf.status,uf.email,uf.relation, uf.dob');
//		$this->db->from($this->table_name.' AS uf');
//
//		$this->db->where('uf.user_id',$user_id);
//        $result = $this->db->get();

        $query = '(SELECT "u"."user_id", 0 AS "id", "u"."first_name" as "name", "u"."username" as "email", 
                0 as "relation"
                FROM "sst_users" AS "u" WHERE "u"."user_id" = '.$user_id.')
                UNION
                (SELECT "uf"."user_id", "uf"."main_id", "uf"."name", "uf"."email", "uf"."relation"
                FROM "user_family" AS "uf" WHERE "uf"."user_id" = '.$user_id.')
                ORDER BY "relation" ASC';
        $result=$this->db->query($query);

		return $result->result();

	}
	public function getUserDetail($user_id)
	{
		$this->db->select('u.user_id,u.first_name as name,u.username as email,0 as relation');
		$this->db->from($this->mdl_users->table_name.' AS u');		
		
		$this->db->where('u.user_id',$user_id);

		$result = $this->db->get();
		return $result->result();

	}

    public function getId($main_id)
    {
        $this->db->select('id');
        $this->db->from($this->table_name);
        $this->db->where('main_id',$main_id);
        $this->db->limit(1);

        $result = $this->db->get();
        return $result->row();

    }

    public function getUserChildren($user_id){
        $this->db->select('"uf"."main_id" AS id, "uf"."name", "uf"."email", "uf"."relation", 
        TO_CHAR("uf"."dob" :: DATE, \'YYYY-MM-DD\') AS dob');
        $this->db->from($this->table_name.' AS uf');
        $this->db->where('uf.user_id', $user_id);
        $this->db->where('"uf"."relation" IN (2,3)');
        $result = $this->db->get();
        return $result->result();
    }

}