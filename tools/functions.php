<?php 

function validate_field($field) {
  $field = htmlentities($field);
  if(strlen(trim($field)) < 1) {
    return false;
  }
  else {
    return true;
  }
}

function validate_password($password) {
  $password = htmlentities($password);

  if (strlen(trim($password)) < 1) {
    return "Password cannot be empty";
  } elseif (strlen($password) < 8) {
    return "Password must be at least 8 characters long";
  } else {
    return "success"; // Indicates successful validation
  }
}

function validate_email($email){
  // Check if the 'email' key exists in the $_POST array
  if (isset($email)) {
    $email = trim($email); // Trim whitespace

    // Check if the email is not empty
    if (empty($email)) {
      return false;
    } else {
      // Check if the email has a valid format
      if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true; // Email is valid
      } else {
        return false; // Email is not valid
      }
    }
  } else {
    return false; // 'email' key doesn't exist in $_POST
  }
}

  function validate_emp_id($emp_id){
    // Check if the 'emp_id' key exists in the $_POST array
    if (isset($emp_id)) {
      $emp_id = trim($emp_id); // Trim whitespace
  
      // Check if the emp_id is not empty
      if (empty($emp_id)) {
        return false;
      } else {
        // Check if the emp_id has a valid format
        if (filter_var($emp_id, FILTER_VALIDATE_INT)) {
          return true; // emp_id is valid
        } else {
          return false; // emp_id is not valid
        }
      }
    } else {
      return false; // 'emp_id' key doesn't exist in $_POST
    }
}   

?>