<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home_model extends CI_model
{
    public function login($username,$password)
    {
        $myres_arr=array();
        $myres_arr['status']=FALSE;
        $myres_arr['data']=array();
        $this->db->where('username',$username);
        $this->db->where('password',$password);
        $res=$this->db->get('user')->row_array();
        if(!empty($res)):
            $myres_arr['status']=TRUE;
            $myres_arr['data']=$res;
        endif;
        return $myres_arr;
    }
    public function registeration($email,$username,$password,$my_image)
    {
        $status=FALSE;
        $mydata=array(
            'username'=>$username,
            'password'=>$password,
            'email'=>$email,
            'image'=>$my_image
        );
        $this->db->insert('user',$mydata);
        $id=$this->db->insert_id();
        if(!empty($id)):
            $status=TRUE;
        endif;
        return $status;
    }
    public function fetch_all_data($user_id)
    {
        $data = array(
                'id'=>$user_id
            );
        $q=$this->db->get_where('user',$data);
		$my_data=$q->result_array();
		return $my_data;       
    }

}
