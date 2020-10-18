<?php
require_once "init.php";
const FILE_PATH = "./uploads/";

if (!isset($_FILES['file_to_upload']['error']) || is_array($_FILES['file_to_upload']['error'])) {
    echo 'Invalid upload';
    exit();
}

switch ($_FILES['file_to_upload']['error']) {
    case UPLOAD_ERR_OK:
        break;
    case UPLOAD_ERR_NO_FILE:
        echo 'No file has been sent';
        exit();
        break;
    case UPLOAD_ERR_INI_SIZE:
        echo 'Maximum file size exceeded';
        exit();
        break;
    case UPLOAD_ERR_FORM_SIZE:
        echo 'Maximum form file size exceeded';
        exit();
        break;
    default:
        echo 'Unknown error';
        exit();
        break;
}

if ($_FILES['file_to_upload']['size'] > 4000000) {
    echo 'Maximum file size exceeded';
    exit();
}

$fileMime = new finfo(FILEINFO_MIME_TYPE);

$search = array_search($fileMime->file($_FILES['file_to_upload']['tmp_name']), array(
    'jpg'=> 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif'
), true);

if ($search === false) {
    echo 'Invalid format';
    exit();
}

$imageFileType = strtolower(pathinfo(FILE_PATH . basename($_FILES['file_to_upload']['name']),PATHINFO_EXTENSION));

if ($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'gif' && $imageFileType != 'jpeg') {
    echo 'Invalid format';
    exit();
}

$check = getimagesize($_FILES['file_to_upload']['tmp_name']);
if ($check === false) {
    echo "Invalid image";
    exit();
}

if (!move_uploaded_file($_FILES['file_to_upload']['tmp_name'], FILE_PATH . basename($_FILES['file_to_upload']['name']))) {
    echo 'Upload failure on move';
    exit();
} else {
    echo  'Success';
}



exit();