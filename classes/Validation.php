<?php
require_once 'Database.php';

class Validation {
    private $db;
    public function __construct($db = null) {
        if(is_null($db)) {
            $db = new Database;
        }
        $this->db = $db;
    }
    
    public function loginValidation($login) {
        $login = trim($login);
        if(strlen($login) < 3 || strlen($login) > 40) {
            $_SESSION['registerValidation'] = 'Your login should contain from 3 to 40 characters!';
            return false;
        }
        if(!preg_match('/^[A-Za-z]/', $login)) {
            $_SESSION['registerValidation'] = 'Your first login character should be a letter!';
            return false;
        }
        if($this->myGetLoginNumber($login) > 0) {
            $_SESSION['registerValidation'] = "The given login is already in the database! Please enter another one.";
            return false;
        }
        return true;
    }
    
    public function masterPasswordValidation($pass, $pass2) {
        $pass = trim($pass);
        if($pass != $pass2) {
            $_SESSION['registerValidation'] = 'The given passwords must be identical!';
            return false;
        }
        else if(strlen($pass) < 3) {
            $_SESSION['registerValidation'] = 'Your password should contain at least 3 characters!';
            return false;
        }
        return true;
    }
    
    public function protectionTypeValidation($type) {
        if($type != 'sha' && $type != 'hmac') {
            $_SESSION['registerValidation'] = 'Please select one of the password protection type!';
            return false;
        }
        return true;
    }
    
    public function myGetLoginNumber($login) : int { 
        $foundedLoginsNumber = $this->db->getLoginNumber($login);
        return $foundedLoginsNumber;
    }
    
    public function webAddressValidation($address) {
        if(strlen($address) == 0) {
            $_SESSION['addPasswordValidation'] = "Please enter your web address correctly!";
            return false;
        }
        return true;
    }
    
    public function addPasswordLoginValidation($login) {
        if(strlen($login) < 3) {
            $_SESSION['addPasswordValidation'] = "Please enter your login correctly!";
            return false;
        }
        return true;
    }
    
    public function addPasswordPasswordValidation($password) {
        $password = trim($password);
        if(strlen($password) == 0) {
            $_SESSION['addPasswordValidation'] = "Please enter your password correctly!";
            return false;
        }
        return true;
    }
    
    public function deleteUserPasswordValidation($userID, $passwordID){
        $result = $this->db->mysqli->query(
                sprintf("SELECT id FROM password WHERE id_user=%s",
                mysqli_real_escape_string($this->db->mysqli, $userID))); 
        $ids = [];
        while($row = $result->fetch_assoc()) {
            array_push($ids, $row["id"]);
        }
        if(in_array($passwordID, $ids)){
            return true;
        }
        else {
            $_SESSION['deleteUserPasswordValidation'] = "You cannot delete this password!";
            return false;
        }
    }
    
    public function showPasswordValidation($userID, $passwordID) {
        $result = $this->db->getAllUserPassword($userID);
        $ids = [];
        if(is_array($result)) {
            for($i=0; $i<count($result); $i++) {
                array_push($ids, $result[$i]);
            } 
        }
        else {
           while($row = $result->fetch_assoc()) {
              array_push($ids, $row["id"]);
            } 
        } 
        if(in_array($passwordID, $ids)){
            return true;
        }
        else {
            return false;
        }
    }
    
    public function changeMasterPasswordValidation($userID, $oldPass, $newPass, $newPass2, $pepper, $hmacKey) {
        $result = $this->db->mysqli->query(
                sprintf("SELECT password_hash, salt FROM user WHERE id=%s",
                mysqli_real_escape_string($this->db->mysqli, $userID))); 
        $row = $result->fetch_assoc();
        $encryptedPass = $row["password_hash"];
        $salt = $row["salt"];
        if(( ($salt == null || $salt == "") && $encryptedPass == hash_hmac("sha512", $oldPass, $hmacKey) ) ||
           ( ($salt != null && $salt != '') && $encryptedPass == hash("sha512", $salt.$pepper.$oldPass))) {
            
        }
        else {
            $_SESSION['changeMasterPasswordValidation'] = "Please enter your master passsword correctly!";
            return false;
        }
        $newPass = trim($newPass);
        if($newPass != $newPass2) {
            $_SESSION['changeMasterPasswordValidation'] = 'The given passwords must be identical!';
            return false;
        }
        else if(strlen($newPass) < 3) {
            $_SESSION['changeMasterPasswordValidation'] = 'Your new password should contain at least 3 characters!';
            return false;
        }
        return true;
    }
  
}
