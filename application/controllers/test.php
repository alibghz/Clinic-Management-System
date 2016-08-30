<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {
  
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
    
    $this->load->model('lab');
    
    $data['tests'] = $this->lab->get();
    $data['title'] = 'Tests List';
    $data['navActiveId']='navbarLiLab';
    
    $data['page'] = (int)$page;
    $data['per_page'] = (int)$limit;
    $this->load->library('pagination');
    $this->load->library('my_pagination');
    $config['base_url'] = site_url('test/index/'.$data['per_page']);
    $config['total_rows'] = count($data['tests']);
    $config['per_page'] = $data['per_page'];
    $this->my_pagination->initialize($config); 
    $data['pagination']=$this->my_pagination->create_links();
    
    $path='lab/list';
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
        $this->load->model('lab');
        $q=$this->input->post('q');
        $lab=$this->lab->search(array('lab_name_en'=>$q,'lab_name_fa'=>$q));
        $data['lab']=$lab;
        $this->load->view('lab/result',$data);
        return TRUE;
    }
    $data['title']='Test Search';
    $this->load->view('lab/search');
  }
  
  public function edit($test_id=0)
  {
    if (!$this->bitauth->logged_in())
    {
      $this->session->set_userdata('redir', current_url());
      redirect('account/login');
    }
    if(!$this->bitauth->has_role('lab'))
    {
      $this->_no_access();
      return;
    }
    $this->load->model('lab');
    $this->lab->load($test_id);
    
    if($this->input->post())
    {
      $this->form_validation->set_rules(array(
        array( 'field' => 'test_name_en', 'label' => 'Test Name in English', 'rules' => 'required|trim|has_no_schar', ),
        array( 'field' => 'test_name_fa', 'label' => 'Test Name in Dari', 'rules' => 'required|trim|has_no_schar', ),
        array( 'field' => 'catagory', 'label' => 'Category', 'rules' => 'trim|has_no_schar', ),
        array( 'field' => 'price', 'label' => 'Price', 'rules' => 'required|trim|has_no_schar', ),
        array( 'field' => 'memo', 'label' => 'Memo', 'rules' => 'trim', ),
      ));
      if($this->form_validation->run() == TRUE)
      {
        //check if patient form already loaded from this app -> should be checked with session
        $session_check=$this->session->userdata(current_url());
        $this->session->unset_userdata(current_url());
        if($session_check && $session_check[0]==$test_id)
        {
            unset($_POST['submit']);
            $test=$this->input->post();
            $this->load->model('lab');
            foreach($test as $key => $value)
              $this->lab->$key = $value;
            $this->lab->save();
            unset($_POST);
            $data['script'] = '<script>alert("'. html_escape($this->lab->test_name_en). ' has been updated successfuly.");</script>';
            redirect('test');
        }else{
          //user may have sent the form to a url other than the original
          $data['error'] = '<div class="alert alert-danger">Form URL Error</div>';
        }
      }else{
        $data['error']=validation_errors();
      }
    }
    $this->session->set_userdata(current_url(),array($test_id));
    $data['title']='Edit Test';
    $data['test']=$this->lab;
    $path='lab/edit';
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

  public function delete($test_id=0)
  {
    if (!$this->bitauth->logged_in())
    {
      $this->session->set_userdata('redir', current_url());
      redirect('account/login');
    }
    if(!$this->bitauth->has_role('lab'))
    {
      $this->_no_access();
      return;
    }
    $this->load->model('lab');
    $this->lab->load($test_id);
    
    if($this->input->post())
    {
      $this->form_validation->set_rules(array(
        array( 'field' => 'test_id', 'label' => 'ID', 'rules' => 'required|trim|has_no_schar', ),
        array( 'field' => 'del', 'label' => '', 'rules' => 'required|trim|has_no_schar', ),
      ));
      if($this->form_validation->run() == TRUE&&
         $this->input->post('test_id')==$test_id)
      {
        //check if patient form already loaded from this app -> should be checked with session
        $session_check=$this->session->userdata(current_url());
        $this->session->unset_userdata(current_url());
        if($session_check && $session_check[0]==$test_id)
        {
            $this->load->model('lab_patient');
            $this->lab_patient->get_by_fkey('test_id',$test_id);
            if(!$this->lab_patient->lab_patient_id){
                $this->lab->delete();
                echo 'ok';
                return;
            }else{
                echo 'nok';
                return;
            }
        }else{
          //user may have sent the form to a url other than the original
          $data['error'] = 'mismatch';
          return;
        }
      }else{
        $data['error']='invalid';
        return;
      }
    }
    $this->session->set_userdata(current_url(),array($test_id));
    $data['test']=$this->lab;
    $this->load->view('lab/confirm_delete',$data);
  }
  
  public function new_test()
  {
    if (!$this->bitauth->logged_in())
    {
      $this->session->set_userdata('redir', current_url());
      redirect('account/login');
    }
    if(!$this->bitauth->has_role('lab'))
    {
      $this->_no_access();
      return;
    }
    
    if($this->input->post())
    {
      $this->form_validation->set_rules(array(
        array( 'field' => 'test_name_en', 'label' => 'Test Name in English', 'rules' => 'required|trim|has_no_schar', ),
        array( 'field' => 'test_name_fa', 'label' => 'Test Name in Dari', 'rules' => 'required|trim|has_no_schar', ),
        array( 'field' => 'catagory', 'label' => 'Category', 'rules' => 'trim|has_no_schar', ),
        array( 'field' => 'price', 'label' => 'Price', 'rules' => 'required|trim|has_no_schar', ),
        array( 'field' => 'memo', 'label' => 'Memo', 'rules' => 'trim', ),
      ));
      if($this->form_validation->run() == TRUE)
      {
        
        unset($_POST['submit']);
        $test=$this->input->post();
        $this->load->model('lab');
        foreach($test as $key => $value)
          $this->lab->$key = $value;
        $this->lab->save();
        unset($_POST);
        $data['script'] = '<script>alert("'. html_escape($this->lab->test_name_en). ' has been registered successfuly.");</script>';
      }else{
        $data['error']=validation_errors();
      }
    }
    $data['title']='New Test';
    $path='lab/new';
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
    if(!($this->bitauth->has_role('doctor')||$this->bitauth->has_role('lab')))
    {
      $this->_no_access();
      return;
    }
    
    if($this->input->post())
    {
      $this->form_validation->set_rules(array(
        array( 'field' => 'test_id', 'label' => 'Test ID', 'rules' => 'required|is_numeric', ),
        array( 'field' => 'patient_id', 'label' => 'Patient ID', 'rules' => 'required|is_numeric', ),
        array( 'field' => 'no_of_item', 'label' => 'Number of Item', 'rules' => 'required|is_numeric', ),
        array( 'field' => 'total_cost', 'label' => 'Total Cost', 'rules' => 'required|is_numeric', ),
        array( 'field' => 'memo', 'label' => 'Memo', 'rules' => 'trim', ),
      ));
      if($this->form_validation->run() == TRUE)
      {
        $this->load->model('lab_patient');
        unset($_POST['submit']);
        foreach ($this->input->post() as $key => $value)
          $this->lab_patient->$key = $value;
        $this->lab_patient->user_id_assign=$this->session->userdata('ba_user_id');
        $this->lab_patient->assign_date=now();
        $this->lab_patient->save();
        $this->load->model('lab');
        $this->lab->load($this->lab_patient->test_id);
        
        echo '<tr id="dpi'.$this->lab_patient->lab_patient_id.'"><td class="id"></td>'.
            '<td>'.$this->lab->test_name_en.'</td>'.
            '<td>'.$this->lab->test_name_fa.'</td>'.
            '<td>'.$this->lab->price.'</td>'.
            '<td>'.$this->lab_patient->no_of_item.'</td>'.
            '<td>'.$this->lab_patient->total_cost.'</td>'.
            '<td class="actions">'.anchor('#', 'Delete ',array('dpi'=>$this->lab_patient->lab_patient_id,'di'=>$this->lab_patient->test_id,'pi'=>$this->lab_patient->patient_id,'tc'=>$this->lab_patient->total_cost,'action'=>'delete'));
            if($this->bitauth->has_role('receptionist')) echo anchor('#', 'Pay ',array('dpi'=>$this->lab_patient->lab_patient_id,'di'=>$this->lab_patient->test_id,'pi'=>$this->lab_patient->patient_id,'tc'=>$this->lab_patient->total_cost,'action'=>'pay'));
            echo '</td></tr>';
        return;
      }
      else{
        
      }
    }
  }
  
  public function payment($lab_patient_id)
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
        array( 'field' => 'lab_patient_id', 'label' => 'ID', 'rules' => 'required|trim|is_numeric|has_no_schar', ),
        array( 'field' => 'test_id', 'label' => 'Drug ID', 'rules' => 'required|trim|is_numeric|has_no_schar', ),
        array( 'field' => 'patient_id', 'label' => 'Patient ID', 'rules' => 'required|trim|is_numeric|has_no_schar', ),
      ));
      if($this->form_validation->run() == TRUE && $this->input->post('lab_patient_id')==$lab_patient_id)
      {
        $this->load->model('lab_patient');
        $this->lab_patient->load($this->input->post('lab_patient_id'));
        if($this->lab_patient->test_id==$this->input->post('test_id') &&
           $this->lab_patient->patient_id==$this->input->post('patient_id') &&
           $this->lab_patient->user_id_discharge==NULL &&
           $this->lab_patient->discharge_date==NULL)
        {
          $this->lab_patient->user_id_discharge=$this->session->userdata('ba_user_id');
          $this->lab_patient->discharge_date=now();
          $this->lab_patient->save();
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

  /*
   * 
   */
  public function deletedpi($lab_patient_id)
  {
    if (!$this->bitauth->logged_in())
    {
      $this->session->set_userdata('redir', current_url());
      redirect('account/login');
    }
    if(!($this->bitauth->has_role('receptionist')||$this->bitauth->has_role('doctor')||$this->bitauth->has_role('lab')))
    {
      $this->_no_access();
      return;
    }
    
    if($this->input->post())
    {
      $this->form_validation->set_rules(array(
        array( 'field' => 'lab_patient_id', 'label' => 'ID', 'rules' => 'required|trim|is_numeric|has_no_schar', ),
        array( 'field' => 'test_id', 'label' => 'Drug ID', 'rules' => 'required|trim|is_numeric|has_no_schar', ),
        array( 'field' => 'patient_id', 'label' => 'Patient ID', 'rules' => 'required|trim|is_numeric|has_no_schar', ),
      ));
      if($this->form_validation->run() == TRUE && $this->input->post('lab_patient_id')==$lab_patient_id)
      {
        $this->load->model('lab_patient');
        $this->lab_patient->load($this->input->post('lab_patient_id'));
        if($this->lab_patient->test_id==$this->input->post('test_id') &&
           $this->lab_patient->patient_id==$this->input->post('patient_id') &&
           $this->lab_patient->user_id_discharge==NULL &&
           $this->lab_patient->discharge_date==NULL)
        {
          $this->lab_patient->delete();
          unset($_POST);
          echo 'ok';
        }else{
          echo 'mismatch';
          //$data['error']='<div class="alert alert-danger">Payment Data Mismatch<div>';
        }
      }else{
          echo 'invalid';
        //$data['error']=validation_errors();
      }
    }
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