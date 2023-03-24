<?php
require_once './classes/User.php';
require_once './classes/Database.php';
session_start();
$user = new User();
$db = new Database();

if(filter_input(INPUT_POST, "submit")) {
    $action = filter_input(INPUT_POST, "submit");
    
    switch($action) {
        case "login": $user->login(filter_input(INPUT_POST, 'loginL'), filter_input(INPUT_POST, 'passwordL')); break;
        case "register" :$user->register(filter_input(INPUT_POST, 'loginR'), filter_input(INPUT_POST, 'passwordR'), filter_input(INPUT_POST, 'passwordR2'), filter_input(INPUT_POST, 'passwordProtection')); break;
        case "addPassword" : $user->addNewPassword(filter_input(INPUT_POST, 'addressAdd'), filter_input(INPUT_POST, 'loginAdd'), filter_input(INPUT_POST, 'passwordAdd'), filter_input(INPUT_POST, 'descriptionAdd')); break;
        case "logout": $user->logout(); break;
        case "changePassword": 
            if(isset($_SESSION["userID"])) {
                $user->changeMasterPassword($_SESSION["userID"], filter_input(INPUT_POST, 'oldPassword'), filter_input(INPUT_POST, 'newPassword'), filter_input(INPUT_POST, 'newPassword2'));
            } 
        break;
        case "getUserPasswords": 
            if(isset($_SESSION["userID"])) {
                $user->getUserPasswords($_SESSION["userID"]); 
            }  
        break;
        case "deleteUserPassword": 
            if(isset($_SESSION["userID"])) {
                $user->deleteUserPassword($_SESSION["userID"], filter_input(INPUT_POST, 'passwordID'));
            } 
        break;
        case "showUserPassword": 
            if(isset($_SESSION["userID"])) {
                $user->showPassword($_SESSION["userID"], filter_input(INPUT_POST, 'passwordID'));
            }
        break;
        case "getUserLoginActivity": 
            if(isset($_SESSION["userID"])) {
                $db->getUserLoginActivity($_SESSION["userID"]);
            }
        break;
        case "isUserExist":
            if(isset($_SESSION["userID"])) {
                $user->isUserExistAndIsNotLogged(filter_input(INPUT_POST, 'login'), $_SESSION["userLogin"]); 
            }
        break;  
        case "saveSharingPassword":
            if(isset($_SESSION["userID"])) {
                $db->saveSharingPassword($_SESSION["userID"], filter_input(INPUT_POST, 'idPass'), json_decode(filter_input(INPUT_POST, 'usersToShare'))); 
            }
        break;
        case "removeUserFromSharing":
            if(isset($_SESSION["userID"])) {
                $db->removeUserFromSharing($_SESSION["userID"], filter_input(INPUT_POST, 'userLogin'), filter_input(INPUT_POST, 'passID'));
            }
        break;
    }
}
else {
    header('Location: index.php');
}
