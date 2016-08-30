<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comment extends CI_Controller {
  
  /**
   * Comment::__construct()
   *
   */
  public function __construct()
  {
    parent::__construct();

    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
  }
  
  /**
   * Comment::index()
   */
  public function index($patient_id=0,$page = 1, $limit = 15)
  {
    if (!$this->bitauth->logged_in())
    {
      $this->session->set_userdata('redir', current_url());
      redirect('account/login');
    }
  }
  
  /*
   * Comment::add()
   * 
   */
  public function add($patient_doctor_id=0)
  {
    if (!$this->bitauth->logged_in())
    {
      $this->session->set_userdata('redir', current_url());
      redirect('account/login');
    }
    if(!$this->bitauth->has_role('doctor'))
    {
      return;
    }
    $this->load->model('comments');
    $this->load->model('patient_doctor');
    if($this->input->post())
    {
      $this->form_validation->set_rules(array(
        array( 'field' => 'patient_doctor_id', 'label' => 'Patient Doctor ID', 'rules' => 'required|has_no_schar', ),
        array( 'field' => 'comment', 'label' => 'Comment', 'rules' => 'required|trim|has_no_schar', ),
      ));
      if($this->form_validation->run() == TRUE && 
         $this->input->post('patient_doctor_id')== $patient_doctor_id)
      {
        $this->patient_doctor->load($patient_doctor_id);
        if($this->patient_doctor->user_id!=$this->session->userdata('ba_user_id'))
          return;
        $this->comments->patient_doctor_id=$this->input->post('patient_doctor_id');
        $this->comments->comment=$this->input->post('comment');
        $this->comments->create_date=now();
        $this->comments->last_edit_time=now();
        $this->comments->save();
        $this->comments->load($this->comments->comment_id);
        $data['comment']=$this->comments;
        $this->load->view('patient/comment',$data);
      }
    }
  }
  
  /*
   * Comment::edit()
   * 
   */
  public function edit($comment_id=0)
  {
    if (!$this->bitauth->logged_in())
    {
      $this->session->set_userdata('redir', current_url());
      redirect('account/login');
    }
    if(!$this->bitauth->has_role('doctor'))
    {
      return;
    }
    $this->load->model('comments');
    $this->load->model('patient_doctor');
    if($this->input->post())
    {
      $this->form_validation->set_rules(array(
        array( 'field' => 'comment_id', 'label' => 'Comment ID', 'rules' => 'required|is_numeric', ),
        array( 'field' => 'patient_doctor_id', 'label' => 'Patient Doctor ID', 'rules' => 'required|is_numeric', ),
        array( 'field' => 'comment', 'label' => 'Comment', 'rules' => 'trim|required|has_no_schar', ),
      ));
      if($this->form_validation->run() == TRUE && 
         $comment_id==$this->input->post('comment_id'))
      {
        $this->comments->load($comment_id);
        $this->patient_doctor->load($this->comments->patient_doctor_id);
        if($this->patient_doctor->user_id!=$this->session->userdata('ba_user_id'))
          return;
        $this->comments->comment=$this->input->post('comment');
        $this->comments->last_edit_time=now();
        $this->comments->save();
        $data['comment']=$this->comments;
        $this->load->view('patient/comment',$data);
      }
    }
  }
}

/* End of file patient.php */
/* Location: ./application/controllers/patient.php */