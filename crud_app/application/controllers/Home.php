<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller 
{
	public function register()
	{
		$this->load->view('registration');
	}
	public function registeration($param='')
	{
		if(empty($param)):
			show_404();
		else:
			if(empty($_POST)):
				show_404();
			else:
				if($param=="submit"):
					$this->form_validation->set_rules('email','Email ID','required');
					$this->form_validation->set_rules('username','Username','required');
					$this->form_validation->set_rules('password','Password','required');
					if($this->form_validation->run()===FALSE):
						echo "Invalid Form Inputs";
					else:
						$email=$this->input->post('email');
						$username=$this->input->post('username');
						$password=md5($this->input->post('password'));
						$my_image='Not Avialable';
						$config['upload_path']          = './uploads/';
						$config['allowed_types']        = 'gif|jpg|png';
						$config['max_size']             = 100;
						$config['max_width']            = 1024;
						$config['max_height']           = 768;
		
						$this->load->library('upload', $config);
						if(!$this->upload->do_upload('userfile')):
							$error = array('error' => $this->upload->display_errors());
							echo "Error uploading image";
						else:
							$img_data = array('upload_data' => $this->upload->data());
							$my_image=base_url()."uploads/".$img_data['upload_data']['file_name'];
						endif;
						$save_status=$this->home_model->registeration($email,$username,$password,$my_image);
						if($save_status):
							echo "Registration Successfull<br/><a href='".base_url()."'>login Now</a>";
						else:
							echo "Registration Failed";
						endif;
					endif;
				else:
					show_404();
				endif;
			endif;
		endif;
	}
	public function index()
	{
		$this->load->view('login');
	}
	public function login($param='')
	{
		if(empty($param)):
			show_404();
		else:
			if(empty($_POST)):
				show_404();
			else:
				if($param=="submit"):
					$this->form_validation->set_rules('username','Username','required');
					$this->form_validation->set_rules('password','Password','required');
					if($this->form_validation->run()===FALSE):
						echo "Invalid Form Inputs";
					else:
						$username=$this->input->post('username');
						$password=md5($this->input->post('password'));
						$user_data=$this->home_model->login($username,$password);
						
						if($user_data['status']):
							$login_status=$this->set_user_session($user_data['data']);
							if($login_status):
								redirect('home/dashboard','refresh');
							else:
								echo "Login Failed";
							endif;
						else:
							echo "Login Failed";
						endif;
					endif;
				else:
					show_404();
				endif;
			endif;
		endif;
	}
	private function set_user_session($user_data)
	{
		$status=false;
		$my_data_arr=array(
			'id'=>$user_data['id'],
			'username'=>$user_data['username'],
			'email'=>$user_data['email'],
			'image'=>$user_data['image'],
			'logged_in'=>TRUE,
			'login_time'=>date("Y-m-d h:i:sa")
		);
		if(!empty($my_data_arr)):
			$this->session->set_userdata($my_data_arr);
			$status=true;
		endif;
		return $status;
	}
	public function check_session()
	{
		if(!$this->session->userdata('logged_in')):
			redirect('home','refresh');
		endif;
	}
	public function logout()
	{
		$this->check_session();
		$this->session->unset_userdata('id');
		$this->session->unset_userdata('username');
		$this->session->unset_userdata('email');
		$this->session->unset_userdata('image');
		$this->session->unset_userdata('logged_in');
		$this->session->unset_userdata('login_time');
		$this->session->sess_destroy();
		redirect('home','refresh');
	}
	public function dashboard()
	{

		$this->check_session();
		$user_id= $this->session->userdata('id');
		$data['user_data']=$this->home_model->fetch_all_data($user_id);
		// echo "<pre>";
		// print_r($data);
		// exit();
		$this->load->view('dashboard',$data);
	}
}
