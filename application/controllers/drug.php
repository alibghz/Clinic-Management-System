<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Drug extends CI_Controller {
  
  /**
   * Drug::__construct()
   *
   */
  public function __construct()
  {
    parent::__construct();

    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
  }

  
  /**
   * Drug::index()
   */
  public function index($limit = 15,$page = 1)
  {
    if (!$this->bitauth->logged_in())
    {
      $this->session->set_userdata('redir', current_url());
      redirect('account/login');
    }
    
    $this->load->model('drugs');
    
    $data['drugs'] = $this->drugs->get();
    $data['title'] = 'Drug List';
    $data['navActiveId']='navbarLiDrug';
    
    $data['page'] = (int)$page;
    $data['per_page'] = (int)$limit;
    $this->load->library('pagination');
    $this->load->library('my_pagination');
    $config['base_url'] = site_url('drug/index/'.$data['per_page']);
    $config['total_rows'] = count($data['drugs']);
    $config['per_page'] = $data['per_page'];
    $this->my_pagination->initialize($config); 
    $data['pagination']=$this->my_pagination->create_links();
    
    $path='drug/list';
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
  
  /**
   * Patient::search()
   */
  public function search(/*$q=''*/)
  {
    if (!$this->bitauth->logged_in())
    {
      $this->session->set_userdata('redir', current_url());
      redirect('account/login');
    }
    //if($q!='')
    if($this->input->post())
    {
        $this->load->model('drugs');
        $q=$this->input->post('q');
        $drugs=$this->drugs->search(array('drug_name_en'=>$q,'drug_name_fa'=>$q));
        $data['drugs']=$drugs;
        $this->load->view('drug/result',$data);
        return TRUE;
    }
    $data['title']='Drug Search';
    $this->load->view('drug/search');
  }
  
  /**
   * Patient::edit()
   */
  public function edit($drug_id=0)
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
    $this->load->model('drugs');
    $this->drugs->load($drug_id);
    
    if($this->input->post())
    {
      $this->form_validation->set_rules(array(
        array( 'field' => 'drug_name_en', 'label' => 'Drug Name in English', 'rules' => 'required|trim|has_no_schar', ),
        array( 'field' => 'drug_name_fa', 'label' => 'Drug Name in Dari', 'rules' => 'required|trim|has_no_schar', ),
        array( 'field' => 'catagory', 'label' => 'Category', 'rules' => 'trim|has_no_schar', ),
        array( 'field' => 'price', 'label' => 'Price', 'rules' => 'required|trim|has_no_schar', ),
        array( 'field' => 'memo', 'label' => 'Memo', 'rules' => 'trim', ),
      ));
      if($this->form_validation->run() == TRUE)
      {
        //check if patient form already loaded from this app -> should be checked with session
        $session_check=$this->session->userdata(current_url());
        $this->session->unset_userdata(current_url());
        if($session_check && $session_check[0]==$drug_id)
        {
            unset($_POST['submit']);
            $drug=$this->input->post();
            $this->load->model('drugs');
            foreach($drug as $key => $value)
              $this->drugs->$key = $value;
            $this->drugs->save();
            unset($_POST);
            $data['script'] = '<script>alert("'. html_escape($this->drugs->drug_name_en). ' has been updated successfuly.");</script>';
            redirect('drug');
        }else{
          //user may have sent the form to a url other than the original
          $data['error'] = '<div class="alert alert-danger">Form URL Error</div>';
        }
      }else{
        $data['error']=validation_errors();
      }
    }
    $this->session->set_userdata(current_url(),array($drug_id));
    $data['title']='Edit Drug';
    $data['drug']=$this->drugs;
    $path='drug/edit';
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

  /**
   * Patient::edit()
   */
  public function delete($drug_id=0)
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
    $this->load->model('drugs');
    $this->drugs->load($drug_id);
    
    if($this->input->post())
    {
      $this->form_validation->set_rules(array(
        array( 'field' => 'drug_id', 'label' => 'ID', 'rules' => 'required|trim|has_no_schar', ),
        array( 'field' => 'del', 'label' => '', 'rules' => 'required|trim|has_no_schar', ),
      ));
      if($this->form_validation->run() == TRUE&&
         $this->input->post('drug_id')==$drug_id)
      {
        //check if patient form already loaded from this app -> should be checked with session
        $session_check=$this->session->userdata(current_url());
        $this->session->unset_userdata(current_url());
        if($session_check && $session_check[0]==$drug_id)
        {
            $this->load->model('drug_patient');
            $this->drug_patient->get_by_fkey('drug_id',$drug_id);
            if(!$this->drug_patient->drug_patient_id){
                $this->drugs->delete();
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
    $this->session->set_userdata(current_url(),array($drug_id));
    $data['drug']=$this->drugs;
    //$data['css'] = "<style>.form-group{margin-bottom:0px;} .form-group .form-control{margin-bottom:10px;}</style>";
    //$data['includes']=array('drug/delete');
    $this->load->view('drug/confirm_delete',$data);
    //$this->load->view('header',$data);
    //$this->load->view('index',$data);
    //$this->load->view('footer',$data);
  }
  
  /*
   * 
   */
  public function new_drug()
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
        array( 'field' => 'drug_name_en', 'label' => 'Drug Name in English', 'rules' => 'required|trim|has_no_schar', ),
        array( 'field' => 'drug_name_fa', 'label' => 'Drug Name in Dari', 'rules' => 'required|trim|has_no_schar', ),
        array( 'field' => 'catagory', 'label' => 'Category', 'rules' => 'trim|has_no_schar', ),
        array( 'field' => 'price', 'label' => 'Price', 'rules' => 'required|trim|has_no_schar', ),
        array( 'field' => 'memo', 'label' => 'Memo', 'rules' => 'trim', ),
      ));
      if($this->form_validation->run() == TRUE)
      {
        
        unset($_POST['submit']);
        $drug=$this->input->post();
        $this->load->model('drugs');
        foreach($drug as $key => $value)
          $this->drugs->$key = $value;
        $this->drugs->save();
        unset($_POST);
        $data['script'] = '<script>alert("'. html_escape($this->drugs->drug_name_en). ' has been registered successfuly.");</script>';
      }else{
        $data['error']=validation_errors();
      }
    }
    $data['title']='New Drug';
    $data['css'] = "<style>.form-group{margin-bottom:0px;} .form-group .form-control{margin-bottom:10px;}</style>";
    $path='drug/new';
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

  /**
   * Patient::assign()
   */
  public function assign()
  {
    if (!$this->bitauth->logged_in())
    {
      $this->session->set_userdata('redir', current_url());
      redirect('account/login');
    }
    if(!($this->bitauth->has_role('doctor')||$this->bitauth->has_role('pharmacy')))
    {
      $this->_no_access();
      return;
    }
    
    if($this->input->post())
    {
      $this->form_validation->set_rules(array(
        array( 'field' => 'drug_id', 'label' => 'Drug ID', 'rules' => 'required|is_numeric', ),
        array( 'field' => 'patient_id', 'label' => 'Patient ID', 'rules' => 'required|is_numeric', ),
        array( 'field' => 'no_of_item', 'label' => 'Number of Item', 'rules' => 'required|is_numeric', ),
        array( 'field' => 'total_cost', 'label' => 'Total Cost', 'rules' => 'required|is_numeric', ),
        array( 'field' => 'memo', 'label' => 'Memo', 'rules' => 'trim', ),
      ));
      if($this->form_validation->run() == TRUE)
      {
        $this->load->model('drug_patient');
        unset($_POST['submit']);
        foreach ($this->input->post() as $key => $value)
          $this->drug_patient->$key = $value;
        $this->drug_patient->user_id_assign=$this->session->userdata('ba_user_id');
        $this->drug_patient->assign_date=now();
        $this->drug_patient->save();
        $this->load->model('drugs');
        $this->drugs->load($this->drug_patient->drug_id);
        
        echo '<tr id="dpi'.$this->drug_patient->drug_patient_id.'"><td class="id"></td>'.
            '<td>'.$this->drugs->drug_name_en.'</td>'.
            '<td>'.$this->drugs->drug_name_fa.'</td>'.
            '<td>'.$this->drugs->price.'</td>'.
            '<td>'.$this->drug_patient->no_of_item.'</td>'.
            '<td>'.$this->drug_patient->total_cost.'</td>'.
            '<td class="actions">'.anchor('#', 'Delete ',array('dpi'=>$this->drug_patient->drug_patient_id,'di'=>$this->drug_patient->drug_id,'pi'=>$this->drug_patient->patient_id,'tc'=>$this->drug_patient->total_cost,'action'=>'delete'));
            if($this->bitauth->has_role('receptionist')) echo anchor('#', 'Pay ',array('dpi'=>$this->drug_patient->drug_patient_id,'di'=>$this->drug_patient->drug_id,'pi'=>$this->drug_patient->patient_id,'tc'=>$this->drug_patient->total_cost,'action'=>'pay'));
            echo '</td></tr>';
        return;
      }
      else{
        
      }
    }
  }
  
  /*
   * 
   */
  public function payment($drug_patient_id)
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
        array( 'field' => 'drug_patient_id', 'label' => 'ID', 'rules' => 'required|trim|is_numeric|has_no_schar', ),
        array( 'field' => 'drug_id', 'label' => 'Drug ID', 'rules' => 'required|trim|is_numeric|has_no_schar', ),
        array( 'field' => 'patient_id', 'label' => 'Patient ID', 'rules' => 'required|trim|is_numeric|has_no_schar', ),
      ));
      if($this->form_validation->run() == TRUE && $this->input->post('drug_patient_id')==$drug_patient_id)
      {
        $this->load->model('drug_patient');
        $this->drug_patient->load($this->input->post('drug_patient_id'));
        if($this->drug_patient->drug_id==$this->input->post('drug_id') &&
           $this->drug_patient->patient_id==$this->input->post('patient_id') &&
           $this->drug_patient->user_id_discharge==NULL &&
           $this->drug_patient->discharge_date==NULL)
        {
          $this->drug_patient->user_id_discharge=$this->session->userdata('ba_user_id');
          $this->drug_patient->discharge_date=now();
          $this->drug_patient->save();
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

  /*
   * 
   */
  public function deletedpi($drug_patient_id)
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
        array( 'field' => 'drug_patient_id', 'label' => 'ID', 'rules' => 'required|trim|is_numeric|has_no_schar', ),
        array( 'field' => 'drug_id', 'label' => 'Drug ID', 'rules' => 'required|trim|is_numeric|has_no_schar', ),
        array( 'field' => 'patient_id', 'label' => 'Patient ID', 'rules' => 'required|trim|is_numeric|has_no_schar', ),
      ));
      if($this->form_validation->run() == TRUE && $this->input->post('drug_patient_id')==$drug_patient_id)
      {
        $this->load->model('drug_patient');
        $this->drug_patient->load($this->input->post('drug_patient_id'));
        if($this->drug_patient->drug_id==$this->input->post('drug_id') &&
           $this->drug_patient->patient_id==$this->input->post('patient_id') &&
           $this->drug_patient->user_id_discharge==NULL &&
           $this->drug_patient->discharge_date==NULL)
        {
          $this->drug_patient->delete();
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

  /*
   * 
   */
  public function purchased_drug_list($drug_id=0,$start_date=NULL,$end_date=NULL)
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
    
    echo 'NOT IMPLEMENTED YET';
  }
  
  public function add_drug()
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
        array( 'field' => 'drug_id', 'label' => 'Drug', 'rules' => 'required|trim|is_numeric', ),
        array( 'field' => 'purchase_date', 'label' => 'Purchased Date', 'rules' => 'required|trim', ),
        array( 'field' => 'purchase_price', 'label' => 'Purchased Price', 'rules' => 'required|trim|is_numeric', ),
        array( 'field' => 'no_of_item', 'label' => 'Number of Items', 'rules' => 'required|trim|is_numeric', ),
        array( 'field' => 'total_cost', 'label' => 'Total Cost', 'rules' => 'required|trim|is_numeric', ),
        array( 'field' => 'memo', 'label' => 'Memo', 'rules' => '', ),
      ));
      if($this->form_validation->run() == TRUE)
      {
          unset($_POST['submit']);
          $purchased_drug = $this->input->post();
          $purchased_drug['purchase_date']=strtotime($purchased_drug['purchase_date']);
          $this->load->model('purchased_drugs');
          foreach ($purchased_drug as $key => $value) {
              $this->purchased_drugs->$key = $value;
          }
          $this->purchased_drugs->user_id = $this->session->userdata('ba_user_id');
          if($this->purchased_drugs->save()){
              foreach($_POST as $key => $value) unset($_POST[$key]);
              $data['script'] = '<script>alert("Items have been added to DB successfuly.");</script>';
          }else{
              $data['error'] = '<div class="alert alert-danger">Some errors happaned. Please try agin later.</div>';
          }
      }else{
        $data['error'] =  validation_errors();
      }
    }
    $data['title']='Add drug to pharmacy stock';
    $data['drugs_list']=$this->_drugs_list();
    $data['today'] =  date('Y-m-d');
    $path='drug/add_drug';
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

  public function return_drug()
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
        array( 'field' => 'drug_id', 'label' => 'Drug', 'rules' => 'required|trim|is_numeric', ),
        array( 'field' => 'return_date', 'label' => 'Return Date', 'rules' => 'required|trim', ),
        array( 'field' => 'unit_price', 'label' => 'Unit Price', 'rules' => 'required|trim|is_numeric', ),
        array( 'field' => 'no_of_item', 'label' => 'Number of Items', 'rules' => 'required|trim|is_numeric', ),
        array( 'field' => 'total_cost', 'label' => 'Total Cost', 'rules' => 'required|trim|is_numeric', ),
        array( 'field' => 'memo', 'label' => 'Memo', 'rules' => 'required', ),
      ));
      if($this->form_validation->run() == TRUE)
      {
          unset($_POST['submit']);
          $return_drug = $this->input->post();
          unset($return_drug['unit_price']);
          $return_drug['return_date']=strtotime($return_drug['return_date']);
          $this->load->model('returned_drugs');
          foreach ($return_drug as $key => $value){
              $this->returned_drugs->$key = $value;
          }
          $this->returned_drugs->user_id = $this->session->userdata('ba_user_id');
          if($this->returned_drugs->save()){
              foreach($_POST as $key => $value) unset($_POST[$key]);
              $data['script'] = '<script>alert("Items have been returned successfuly.");</script>';
          }else{
              $data['error'] = '<div class="alert alert-danger">Some errors happaned. Please try agin later.</div>';
          }
      }else{
        $data['error'] =  validation_errors();
      }
    }
    $data['title']='Add drug to pharmacy stock';
    $data['drugs_list']=$this->_drugs_list();
    $data['today'] =  date('Y-m-d');
    $path='drug/return_drug';
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
  
  public function check($drug_id=0)
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
    //count all purchased
    //count all returned + sold
    $this->load->model('purchased_drugs');
    $all_drugs = $this->purchased_drugs->get_by_fkey('drug_id',(int)$drug_id,'asc',0);
    $all_drugs_count=0;
    foreach($all_drugs as $drug){
        $all_drugs_count += $drug->no_of_item;
    }
    $this->load->model('returned_drugs');
    $returned_drugs = $this->returned_drugs->get_by_fkey('drug_id',(int)$drug_id,'asc',0);
    $returned_drugs_count=0;
    foreach($returned_drugs as $drug){
        $returned_drugs_count += $drug->no_of_item;
    }
    $this->load->model('drug_patient');
    $sold_drugs = $this->drug_patient->get_sold((int)$drug_id);
    $sold_drugs_count=0;
    foreach($sold_drugs as $drug){
        $sold_drugs_count += $drug->no_of_item;
    }
    
    $data['all_drugs_count']=$all_drugs_count;
    $data['returned_drugs_count']=$returned_drugs_count;
    $data['sold_drugs_count']=$sold_drugs_count;
    $data['count']=$all_drugs_count-($returned_drugs_count+$sold_drugs_count);
    $this->load->view('drug/check',$data);
  }
    
  public function _drugs_list()
  {
    $this->load->model('drugs');
    $drugs = $this->drugs->get();
    $drugs_list['']='';
    foreach ($drugs as $drug) 
    {
      $drugs_list[$drug->drug_id]=  html_escape($drug->drug_name_fa.', '.$drug->drug_name_en.', '.$drug->price);
    }
    return $drugs_list;
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