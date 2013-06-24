<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Users extends CI_Controller {

  /**
   * Index Page for this controller.
   *
   * Maps to the following URL
   * 		http://example.com/index.php/welcome
   * 	- or -
   * 		http://example.com/index.php/welcome/index
   * 	- or -
   * Since this controller is set as the default controller in
   * config/routes.php, it's displayed at http://example.com/
   *
   * So any other public methods not prefixed with an underscore will
   * map to /index.php/users/<method_name>
   * @see http://codeigniter.com/user_guide/general/urls.html
   */
  public function __construct() {
    parent::__construct();
    $this->load->library('email');
    date_default_timezone_set('Asia/Kolkata');
    $this->load->model('usermodel');
  }

//  Method for Adding and Updating users
  public function persistUser($user_id = "0") {
    if ($user_id === "0") { //Add user
      log_message('DEBUG', 'DEV: persistUser called for Adding User');
      $user_info = $this->usermodel->addUser();
      if ($user_info['status'] === "1") {
        print_r(json_encode($user_info));
      } else {
        unset($user_info['user_info']);
        $user_info['status'] = '0';
        $user_info['message'] = 'cant adduser!';
        log_message('ERROR:', 'email error : ' . $this->db->last_query());
        print_r(json_encode($user_info));
      }
    } else { //Update user
      log_message('DEBUG', 'DEV: persistUser called for Updating user_id = ' . $user_id);
      $user_info = $this->usermodel->updateUser($user_id);
      print_r(json_encode($user_info));
    }
  }

// Method to login users
  public function userLogin() {
    log_message('DEBUG', 'DEV: user_login called');
    $user_info = $this->usermodel->userLogin();
    print_r(json_encode($user_info));
  }

}

?>
