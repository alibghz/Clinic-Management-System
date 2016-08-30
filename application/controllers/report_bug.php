<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_bug extends CI_Controller {
  
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
  public function add()
  {
    if (!$this->bitauth->logged_in())
    {
      $this->session->set_userdata('redir', current_url());
      redirect('account/login');
    }
    $data=array();
    $this->load->model('reports');
    if($this->input->post())
    {
      $this->form_validation->set_rules(array(
        array( 'field' => 'subject', 'label' => 'Subject', 'rules' => '', ),
        array( 'field' => 'url', 'label' => 'URL', 'rules' => '', ),
        array( 'field' => 'description', 'label' => 'Description', 'rules' => '', ),
      ));
      if($this->form_validation->run() == TRUE)
      {
        $this->reports->user_id=$this->session->userdata('ba_user_id');
        $this->reports->subject=$this->input->post('subject');
        $this->reports->url=$this->input->post('subject');
        $this->reports->description=$this->input->post('description');
        $this->reports->create_date=now();
        $this->reports->save();
      }
    }
    if($this->reports->report_id){
        $data['report']=$this->reports;
    }
    $this->load->view('report/add', $data);
    
  }
}

/* End of file patient.php */
/* Location: ./application/controllers/patient.php */