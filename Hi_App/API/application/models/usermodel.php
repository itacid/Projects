<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * Description of userModel
 *
 * @author appbinder
 */
class UserModel extends CI_Model {

//Method to add user
  public function addUser() {
    $query = $this->db->get_where('tbl_user', array('email_id' => $_POST['email_id']));
    if (!$query->num_rows() > 0) {
      $extra = array('create_date' => date('y-m-d H:i:s'));
      $_POST['password'] = md5($_POST['password']);
      $data = array_merge($extra, $_POST);              //adding current date and time to post array
      $result = $this->db->insert('tbl_user', $data);   //inserting into database using codeigniter active records classes
      log_message('ERROR', 'DEV: last query ', $this->db->last_query());
      $user_id = $this->db->insert_id();                //fetching user_id
      if ($user_id > 0) {
        log_message('DEBUG', 'DEV: db insertion done with id = ' . $user_id);
        if (!empty($_FILES)) {
          if ($data = $this->do_upload($user_id)) { //check if file uploaded
            $url['profile_picture_url'] = "/uploads/profile_photos/" . $data['file_name'];
            $this->db->where('user_id', $user_id);
            $rs = $this->db->update('tbl_user', $url);                    //update user_tbl with profile_picture_url
            if ($rs) {
              log_message('DEBUG', 'DEV: user added succesfully with profile picture');
              $this->db->select();
              $query = $this->db->get_where('tbl_user', array('user_id' => $user_id));
              foreach ($query->result_array() as $row) {
                $user_info['user_info'] = $row;
              }
              $user_info['status'] = "1";
              return $user_info;
            } else {
              log_message('ERROR', 'DEV: tbl_user cant be updated with profile url.' . $this->db->last_query());
            }
          } else {
            log_message('ERROR', 'DEV: Image error: ' . $this->upload->display_errors());
            return false;
          }
        } else {
          log_message('DEBUG', 'DEV: user added with no profile picture');
          $this->db->select();
          return $this->db->get_where('tbl_user', array('user_id' => $user_id));
        }
      } else {
        log_message('ERROR', 'DEV: user cant be added' . $this->db->last_query());
        $user_info['status'] = "0";
        $user_info['message'] = "username already exists";
        return $user_info;
      }
    } else {
      log_message('ERROR', 'DEV: user cant be added' . $this->db->last_query());
      $user_info['status'] = "0";
      $user_info['message'] = "username already exists";
      return $user_info;
    }
  }

// function to upload image
  function do_upload($user_id) {
    log_message('DEBUG ', 'DEV:do_upload called with user_id :' . $user_id);
    $config['upload_path'] = './uploads/profile_photos/';
    $config['allowed_types'] = 'gif|jpg|png';
    $config['max_size'] = '10000';
    $config['max_width'] = '1000000';
    $config['max_height'] = '10000000';
    $config['file_name'] = $user_id . "_" . $_POST['full_name'] . "_" . date("Y/m/d");
    $this->load->library('upload', $config);

    if (!$this->upload->do_upload()) {

      return false;
    } else {
      log_message('DEBUG ', 'DEV: Image uploaded succesfully at :' . $this->upload->data());
      return $this->upload->data();
    }
  }

//method to login users
  public function userLogin() {
    $email_id = $this->input->post('email_id');
    $password = md5($this->input->post('password'));
    log_message('DEBUG', 'DEV: userLogin called by ' . $email_id . ' and password given is :' . $password);

    $query = $this->db->query("SELECT *
    FROM (`tbl_reporter`)
    WHERE binary `user_name` = '$email_id'
    AND `password` = '$password'");
    log_message('DEBUG', 'DEV: userLogin called by ' . $this->db->last_query());

    if ($query->num_rows() > 0) {

      foreach ($query->result_array()as $row) {
        $user_info['user_info'] = $row;
      }
      $user_info['status'] = '1';
      return $user_info;
    } else {
      $user_info['status'] = "0";
      $user_info['message'] = "Invalid credentials!";
      return $user_info;
    }
  }

}

?>
