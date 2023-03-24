<?php
require_once './classes/Form.php';
require_once './classes/User.php';
session_start();

if(User::hasAccess()) {
    Form::accountPageForm();
}
else {
    header("Location: index.php");
}
