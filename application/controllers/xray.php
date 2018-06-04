<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Xray extends CI_Controller {
  
  public function __construct()
  {
    parent::__construct();

    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
  }

  public function index($limit = 15,$page = 1)
  {
    if (!$this->bitauth->logged_in())
    {
      $this->session->set_userdata('redir', current_url());
      redirect('account/login');
    }
    
    $this->load->model('xrays');
    
    $data['xrays'] = $this->xrays->get();
    $data['title'] = 'Xray List';
    $data['navActiveId']='navbarLiXray';
    
    $data['page'] = (int)$page;
    $data['per_page'] = (int)$limit;
    $this->load->library('pagination');
    $this->load->library('my_pagination');
    $config['base_url'] = site_url('xray/index/'.$data['per_page']);
    $config['total_rows'] = count($data['xrays']);
    $config['per_page'] = $data['per_page'];
    $this->my_pagination->initialize($config); 
    $data['pagination']=$this->my_pagination->create_links();
    
    $path='xray/list';
    if(isset($_GET['ajax'])&&$_GET['ajax']==true)
    {
        $this->load->view($path, $data);
    }else{
        $data['includes']=array($path);
        $this->load->view('header',$data);
        $this->load->view('index',$data);
        $this->load->view('footer',$data);
    }
  }
  
  public function search()
  {
    if (!$this->bitauth->logged_in())
    {
      $this->session->set_userdata('redir', current_url());
      redirect('account/login');
    }
    if($this->input->post())
    {
        $this->load->model('xrays');
        $q=$this->input->post('q');
        $xrays=$this->xrays->search(array('xray_name_en'=>$q,'xray_name_fa'=>$q));
        $data['xrays']=$xrays;
        $this->load->view('xray/result',$data);
        return TRUE;
    }
    $data['title']='Xray Search';
    $this->load->view('xray/search');
  }
  
  public function edit($xray_id=0)
  {
    if (!$this->bitauth->logged_in())
    {
      $this->session->set_userdata('redir', current_url());
      redirect('account/login');
    }
    if(!$this->bitauth->has_role('pharmacy'))
    {
      $this->_no_access();
      return;
    }
    $this->load->model('xrays');
    $this->xrays->load($xray_id);
    
    if($this->input->post())
    {
      $this->form_validation->set_rules(array(
        array( 'field' => 'xray_name_en', 'label' => 'Xray Name in English', 'rules' => 'required|trim|has_no_schar', ),
        array( 'field' => 'xray_name_fa', 'label' => 'Xray Name in Dari', 'rules' => 'required|trim|has_no_schar', ),
        array( 'field' => 'catagory', 'label' => 'Category', 'rules' => 'trim|has_no_schar', ),
        array( 'field' => 'price', 'label' => 'Price', 'rules' => 'required|trim|has_no_schar', ),
        array( 'field' => 'memo', 'label' => 'Memo', 'rules' => 'trim', ),
      ));
      if($this->form_validation->run() == TRUE)
      {
        //check if patient form already loaded from this app -> should be checked with session
        $session_check=$this->session->userdata(current_url());
        $this->session->unset_userdata(current_url());
        if($session_check && $session_check[0]==$xray_id)
        {
            unset($_POST['submit']);
            $xray=$this->input->post();
            $this->load->model('xrays');
            foreach($xray as $key => $value)
              $this->xrays->$key = $value;
            $this->xrays->save();
            unset($_POST);
            $data['script'] = '<script>alert("'. html_escape($this->xrays->xray_name_en). ' has been updated successfuly.");</script>';
            redirect('xray');
        }else{
          $data['error'] = '<div class="alert alert-danger">Form URL Error</div>';
        }
      }else{
        $data['error']=validation_errors();
      }
    }
    $this->session->set_userdata(current_url(),array($xray_id));
    $data['title']='Edit Xray';
    $data['xray']=$this->xrays;
    $path='xray/edit';
    if(isset($_GET['ajax'])&&$_GET['ajax']==true)
    {
        $this->load->view($path, $data);
    }else{
        $data['includes']=array($path);
        $this->load->view('header',$data);
        $this->load->view('index',$data);
        $this->load->view('footer',$data);
    }
  }

  public function delete($xray_id=0)
  {
    if (!$this->bitauth->logged_in())
    {
      $this->session->set_userdata('redir', current_url());
      redirect('account/login');
    }
    if(!$this->bitauth->has_role('pharmacy'))
    {
      $this->_no_access();
      return;
    }
    $this->load->model('xrays');
    $this->xrays->load($xray_id);
    
    if($this->input->post())
    {
      $this->form_validation->set_rules(array(
        array( 'field' => 'xray_id', 'label' => 'ID', 'rules' => 'required|trim|has_no_schar', ),
        array( 'field' => 'del', 'label' => '', 'rules' => 'required|trim|has_no_schar', ),
      ));
      if($this->form_validation->run() == TRUE&&
         $this->input->post('xray_id')==$xray_id)
      {
        $session_check=$this->session->userdata(current_url());
        $this->session->unset_userdata(current_url());
        if($session_check && $session_check[0]==$xray_id)
        {
            $this->load->model('xray_patient');
            $this->xray_patient->get_by_fkey('xray_id',$xray_id);
            if(!$this->xray_patient->xray_patient_id){
                $this->xrays->delete();
                echo 'ok';
                return;
            }else{
                echo 'nok';
                return;
            }
        }else{
          $data['error'] = 'mismatch';
          return;
        }
      }else{
        $data['error']='invalid';
        return;
      }
    }
    $this->session->set_userdata(current_url(),array($xray_id));
    $data['xray']=$this->xrays;
    $this->load->view('xray/confirm_delete',$data);
  }
  
  public function new_xray()
  {
    if (!$this->bitauth->logged_in())
    {
      $this->session->set_userdata('redir', current_url());
      redirect('account/login');
    }
    if(!$this->bitauth->has_role('pharmacy'))
    {
      $this->_no_access();
      return;
    }
    if($this->input->post())
    {
      $this->form_validation->set_rules(array(
        array( 'field' => 'xray_name_en', 'label' => 'Xray Name in English', 'rules' => 'required|trim|has_no_schar', ),
        array( 'field' => 'xray_name_fa', 'label' => 'Xray Name in Dari', 'rules' => 'required|trim|has_no_schar', ),
        array( 'field' => 'catagory', 'label' => 'Category', 'rules' => 'trim|has_no_schar', ),
        array( 'field' => 'price', 'label' => 'Price', 'rules' => 'required|trim|has_no_schar', ),
        array( 'field' => 'memo', 'label' => 'Memo', 'rules' => 'trim', ),
      ));
      if($this->form_validation->run() == TRUE)
      {
        unset($_POST['submit']);
        $xray=$this->input->post();
        $this->load->model('xrays');
        foreach($xray as $key => $value)
          $this->xrays->$key = $value;
        $this->xrays->save();
        unset($_POST);
        $data['script'] = '<script>alert("'. html_escape($this->xrays->xray_name_en). ' has been registered successfuly.");</script>';
      }else{
        $data['error']=validation_errors();
      }
    }
    $data['title']='New Xray';
    $data['css'] = "<style>.form-group{margin-bottom:0px;} .form-group .form-control{margin-bottom:10px;}</style>";
    $path='xray/new';
    if(isset($_GET['ajax'])&&$_GET['ajax']==true)
    {
        $this->load->view($path, $data);
    }else{
        $data['includes']=array($path);
        $this->load->view('header',$data);
        $this->load->view('index',$data);
        $this->load->view('footer',$data);
    }
  }

  public function assign()
  {
    if (!$this->bitauth->logged_in())
    {
      $this->session->set_userdata('redir', current_url());
      redirect('account/login');
    }
    if(!($this->bitauth->has_role('doctor')||$this->bitauth->has_role('xray')))
    {
      $this->_no_access();
      return;
    }
    
    if($this->input->post())
    {
      $this->form_validation->set_rules(array(
        array( 'field' => 'xray_id', 'label' => 'Xray ID', 'rules' => 'required|is_numeric', ),
        array( 'field' => 'patient_id', 'label' => 'Patient ID', 'rules' => 'required|is_numeric', ),
        array( 'field' => 'no_of_item', 'label' => 'Number of Item', 'rules' => 'required|is_numeric', ),
        array( 'field' => 'total_cost', 'label' => 'Total Cost', 'rules' => 'required|is_numeric', ),
        array( 'field' => 'memo', 'label' => 'Memo', 'rules' => 'trim', ),
      ));
      if($this->form_validation->run() == TRUE)
      {
        $this->load->model('xray_patient');
        unset($_POST['submit']);
        foreach ($this->input->post() as $key => $value)
          $this->xray_patient->$key = $value;
        $this->xray_patient->user_id_assign=$this->session->userdata('ba_user_id');
        $this->xray_patient->assign_date=now();
        $this->xray_patient->save();
        $this->load->model('xrays');
        $this->xrays->load($this->xray_patient->xray_id);
        
        echo '<tr id="dpi'.$this->xray_patient->xray_patient_id.'"><td class="id"></td>'.
            '<td>'.$this->xrays->xray_name_en.'</td>'.
            '<td>'.$this->xrays->xray_name_fa.'</td>'.
            '<td>'.$this->xrays->price.'</td>'.
            '<td>'.$this->xray_patient->no_of_item.'</td>'.
            '<td>'.$this->xray_patient->total_cost.'</td>'.
            '<td class="actions">'.anchor('#', 'Delete ',array('dpi'=>$this->xray_patient->xray_patient_id,'di'=>$this->xray_patient->xray_id,'pi'=>$this->xray_patient->patient_id,'tc'=>$this->xray_patient->total_cost,'action'=>'delete'));
            if($this->bitauth->has_role('receptionist')) echo anchor('#', 'Pay ',array('dpi'=>$this->xray_patient->xray_patient_id,'di'=>$this->xray_patient->xray_id,'pi'=>$this->xray_patient->patient_id,'tc'=>$this->xray_patient->total_cost,'action'=>'pay'));
            echo '</td></tr>';
        return;
      }
      else{
        
      }
    }
  }
  
  public function payment($xray_patient_id)
  {
    if (!$this->bitauth->logged_in())
    {
      $this->session->set_userdata('redir', current_url());
      redirect('account/login');
    }
    if(!$this->bitauth->has_role('receptionist'))
    {
      $this->_no_access();
      return;
    }
    
    if($this->input->post())
    {
      $this->form_validation->set_rules(array(
        array( 'field' => 'xray_patient_id', 'label' => 'ID', 'rules' => 'required|trim|is_numeric|has_no_schar', ),
        array( 'field' => 'xray_id', 'label' => 'Xray ID', 'rules' => 'required|trim|is_numeric|has_no_schar', ),
        array( 'field' => 'patient_id', 'label' => 'Patient ID', 'rules' => 'required|trim|is_numeric|has_no_schar', ),
      ));
      if($this->form_validation->run() == TRUE && $this->input->post('xray_patient_id')==$xray_patient_id)
      {
        $this->load->model('xray_patient');
        $this->xray_patient->load($this->input->post('xray_patient_id'));
        if($this->xray_patient->xray_id==$this->input->post('xray_id') &&
           $this->xray_patient->patient_id==$this->input->post('patient_id') &&
           $this->xray_patient->user_id_discharge==NULL &&
           $this->xray_patient->discharge_date==NULL)
        {
          $this->xray_patient->user_id_discharge=$this->session->userdata('ba_user_id');
          $this->xray_patient->discharge_date=now();
          $this->xray_patient->save();
          unset($_POST);
          echo 'ok';
        }else{
          echo 'mismatch';
        }
      }else{
          echo 'invalid';
      }
    }
  }

  public function deletedpi($xray_patient_id)
  {
    if (!$this->bitauth->logged_in())
    {
      $this->session->set_userdata('redir', current_url());
      redirect('account/login');
    }
    if(!($this->bitauth->has_role('receptionist')||$this->bitauth->has_role('doctor')||$this->bitauth->has_role('pharmacy')))
    {
      $this->_no_access();
      return;
    }
    
    if($this->input->post())
    {
      $this->form_validation->set_rules(array(
        array( 'field' => 'xray_patient_id', 'label' => 'ID', 'rules' => 'required|trim|is_numeric|has_no_schar', ),
        array( 'field' => 'xray_id', 'label' => 'Xray ID', 'rules' => 'required|trim|is_numeric|has_no_schar', ),
        array( 'field' => 'patient_id', 'label' => 'Patient ID', 'rules' => 'required|trim|is_numeric|has_no_schar', ),
      ));
      if($this->form_validation->run() == TRUE && $this->input->post('xray_patient_id')==$xray_patient_id)
      {
        $this->load->model('xray_patient');
        $this->xray_patient->load($this->input->post('xray_patient_id'));
        if($this->xray_patient->xray_id==$this->input->post('xray_id') &&
           $this->xray_patient->patient_id==$this->input->post('patient_id') &&
           $this->xray_patient->user_id_discharge==NULL &&
           $this->xray_patient->discharge_date==NULL)
        {
          $this->xray_patient->delete();
          unset($_POST);
          echo 'ok';
        }else{
          echo 'mismatch';
        }
      }else{
          echo 'invalid';
      }
    }
  }

  public function details($xray_patient_id)
  {
    if (!$this->bitauth->logged_in())
    {
      $this->session->set_userdata('redir', current_url());
      redirect('account/login');
    }
    if(!($this->bitauth->has_role('doctor')||$this->bitauth->has_role('xray')))
    {
      $this->_no_access();
      return;
    }
    
    $this->load->model('xray_files');
    if($this->input->post())
    {
      $this->form_validation->set_rules(array(
        array( 'field' => 'xray_patient_id', 'label' => 'Patient ID', 'rules' => 'required|is_numeric', ),
        array( 'field' => 'memo', 'label' => 'Memo', 'rules' => 'trim', ),
      ));
      if($this->form_validation->run() == TRUE)
      {
        //attach photo
        if($_FILES['picture']['tmp_name'])//check if any picture is selected to upload
        {
          $this->load->model('xray_patient');
          $this->xray_patient->load($xray_patient_id);
          $path='uploads/patient/'.$this->xray_patient->patient_id.'/xray/';
          $config['upload_path']='./'.$path;
          $config['file_name']=uniqid().uniqid();
          $config['allowed_types']='gif|jpg|jpeg|png';
          $config['max_size']='1024';
          $this->load->library('upload',$config);

          if($this->upload->do_upload('picture'))
          {
            $data['upload_data'] = $this->upload->data();
            $this->xray_files->xray_patient_id=$xray_patient_id;
            $this->xray_files->upload_date=now();
            $this->xray_files->path=$path.$data['upload_data']['file_name'];
            $this->xray_files->memo=$this->input->post('memo');
            $this->xray_files->save();
            redirect('xray/details/'.$xray_patient_id);
          }else{
            $data['error'] = $this->upload->display_errors('<div class="alert alert-danger">','</div>');
          }
        }
      }
    }    
    $data['xray_files'] = $this->xray_files->get_by_fkey('xray_patient_id', $xray_patient_id, 'asc', null);
    $data['xray_patient_id']=$xray_patient_id;
    $data['title']='Xray Details';    
    
    $this->load->view('xray/details', $data);
  }

  public function _no_access()
  {
    $data['title']='Unauthorized Access';
    $path='account/no_access';
    if(isset($_GET['ajax'])&&$_GET['ajax']==true)
    {
      $this->load->view($path, $data);
    }else{
      $data['includes']=array($path);
      $this->load->view('header', $data);
      $this->load->view('index', $data);
      $this->load->view('footer', $data);
    }
  }
}