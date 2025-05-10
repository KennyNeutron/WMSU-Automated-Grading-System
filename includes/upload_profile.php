<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (isset($_FILES["profile"]) && $_FILES["profile"]["error"] === UPLOAD_ERR_OK) {
    $uploadDir = '../img/profile-img/';
    $fileName = uniqid() . '_' . basename($_FILES["profile"]["name"]);
    $uploadPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES["profile"]["tmp_name"], $uploadPath)) {
      echo "File uploaded successfully.";
    } else {
      echo "Error occurred while uploading the file.";
    }
  } else {
    echo "Error occurred during file upload.";
  }
} else {
  echo "Invalid request method.";
}
?>