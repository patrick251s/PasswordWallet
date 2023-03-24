<?php
require_once './classes/Validation.php';
require_once 'Database.php';

class User {
    private $pepper = "dsyg445b3y";
    private $hmacKey = "sj76uygbd2";
    private $valid;
    private $db;
    
    function __construct() {
        $this->valid = new Validation();
        $this->db = new Database();
    }
    
    public function login($login, $pass) {
        $login = htmlentities($login, ENT_QUOTES, "UTF-8"); 
        $time = date('Y-m-d H:i:s');
        
        //Jesli czlowiek wykonal za duzo blednych prob logowania, nawet na nieistniejace konto
        if($this->isBlockedBySessionID(session_id())) {
            header('Location: index.php');
            return false;
        } 
                    
        try {
            $result = $this->db->mysqli->query(
                sprintf("SELECT * FROM user WHERE login='%s'", 
                mysqli_real_escape_string($this->db->mysqli, $login)));
            
            //Jeśli znaleziono login
            if($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $pass_hash = $row['password_hash'];
                $salt = $row['salt'];
                  
                if(!$this->hasUserAccess($row['id'])) {
                    $_SESSION['accountBlockade'] = $this->db->getUserLockout($row['id']);
                    header('Location: index.php');
                    return false;
                }
                
                //Udane logowanie
                if((($salt != null && $salt != '') && $pass_hash == hash("sha512", $salt.$this->pepper.$pass)) ||
                   (($salt == null || $salt == '') && $pass_hash == hash_hmac("sha512", $pass, $this->hmacKey))) {
                    $_SESSION['userID'] = $row['id'];
                    $_SESSION['userLogin'] = $row['login'];
                    $this->db->saveUserLoginActivity($row['id'], $time, true, session_id());
                    header('Location: passwords.php');
                    return true;
                }
                //Nieudane logowanie - login istnieje
                else {
                    $_SESSION['loginFail'] = 'You entered an incorrect login or password!';
                    $this->db->saveUserLoginActivity($row['id'], $time, false, session_id());
                    $this->isBlockedBySessionID(session_id());
                    $_SESSION['loginFailAttempts'] = $this->checkIfUserCanBeDisabled($row['id']);
                    header('Location: index.php');
                    return false;
                }
            }
            //Nieudane logowanie - login nie istnieje
            else {
                $_SESSION['loginFail'] = 'You entered an incorrect login or password!';
                $this->db->saveUserLoginActivity(null, $time, false, session_id());
                $this->isBlockedBySessionID(session_id());
                header('Location: index.php');
                return false;
            }  
        } catch (mysqli_sql_exception $exception) {
            $_SESSION['loginFail'] = 'An unexpected error has occurred. Try again.';
            header('Location: index.php');
            return false;
        }
    }
    
    public function checkIfUserCanBeDisabled($userID) {
        //$data['time] $data['isCorrect']
        $failLoginAttempts = 0;
        $data = $this->db->getUserLoginAttempts($userID);
        for($i=0; $i<count($data['time']); $i++) {
            if($data['isCorrect'][$i] == false) {
                $failLoginAttempts++;
            }
            else {
                break;
            }
        }
        if($failLoginAttempts >= 3) {
            $_SESSION['blockadeDate'] = $this->db->disableUser($userID, $failLoginAttempts);
        }
        return $failLoginAttempts;
    }
    
    public function hasUserAccess($userID) {
        $blockedTime = $this->db->getUserLockout($userID);
        $blockedTimeUNIX = strtotime($blockedTime);
        if(is_null($blockedTimeUNIX) || (int)$blockedTimeUNIX < (int) time()) {
            return true;
        }
        else {
            return false;
        }
    }
    
    public function isBlockedBySessionID($sessionID) {
        $failedLoginAttempts = 0;
        $data = $this->db->getLoginAttemptsBySessionID($sessionID);
        for($i=0; $i<count($data['isCorrect']); $i++) {
            if($data['isCorrect'][$i] == false) {
                $failedLoginAttempts++;
            }
            else {
                break;
            }
        }
        if($failedLoginAttempts >= 6) {
            $_SESSION['loginFail'] = 'You have made too many failed login attempts! Try again later!';
            return true;
        }
        else {
            return false;
        }
    }
    
    public function register($login, $pass, $pass2, $protectionType) {
        $login = htmlentities($login, ENT_QUOTES, "UTF-8"); 
        if($this->valid->loginValidation($login) && $this->valid->masterPasswordValidation($pass, $pass2) && $this->valid->protectionTypeValidation($protectionType)) {
            $this->insertNewUser($login, $pass, $protectionType);
            return true;
        }
        else {
            $_SESSION['errorLoginR'] = $login;
            $_SESSION['errorPassR'] = $pass;
            $_SESSION['errorPassR2'] = $pass2;
            $_SESSION['errorTypeR'] = $protectionType;
            header("Location: register.php");
            return false;
        }
    }
    
    public function insertNewUser($login, $pass, $protectionType) {
        $salt = null;
        $pass_hash = null;
        $isHMAC = null;
        
        if($protectionType == 'sha') {
            $salt = $this->generateSalt();
            $pass_hash = hash("sha512", $salt.$this->pepper.$pass);
            $isHMAC = false;
        }
        else if($protectionType == 'hmac') {
            $pass_hash = hash_hmac("sha512", $pass, $this->hmacKey);
            $isHMAC = true;
        }
        else {
            $_SESSION['registrationSuccess'] = false;
            header('Location: registerPage.php');
            return false;
        }
        try {
            $this->db->mysqli->query(
                sprintf("INSERT INTO user VALUES(NULL, '%s', '%s', '%s', '%s')", 
                mysqli_real_escape_string($this->db->mysqli, $login),
                mysqli_real_escape_string($this->db->mysqli, $pass_hash),
                mysqli_real_escape_string($this->db->mysqli, $salt),
                mysqli_real_escape_string($this->db->mysqli, $isHMAC)));  
            $_SESSION['registrationSuccess'] = true;
            header('Location: index.php');
            return true;
        } catch (mysqli_sql_exception $exception) {
            $_SESSION['registrationSuccess'] = false;
            header('Location: registerPage.php');
            return false;
        }
    }
    
    public function generateSalt($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_!@#$^%&*()';
        $charactersLength = strlen($characters);
        $salt = '';
        for ($i = 0; $i < $length; $i++) {
            $salt .= $characters[rand(0, $charactersLength - 1)];
        }
        return $salt;
    }
    
    public function logout() {
        session_regenerate_id();
        session_unset();
        header('Location: index.php');
        return true;
    }
    
    public static function hasAccess() {
        if(isset($_SESSION['userLogin'])) {
            return true;
        }
        else {
            return false;
        }
    }
    
    public function getUserSaltFromDB($password, $userID) {
        $salt = null;
        try {
            $result = $this->db->mysqli->query(
                sprintf("SELECT salt FROM user WHERE id = %s", 
                mysqli_real_escape_string($this->db->mysqli, $userID)));  
            if($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $salt = $row['salt'];
                return $salt;
            }
            else {
                return 1;
            }
        } catch (mysqli_sql_exception $exception) {
            return 1;
        }
    }
    
    public function getEncryption($password, $userSalt, $userMasterPassHash) {
        $encryption = null;
        if($userSalt == "" || $userSalt == null) {
            $encryption = openssl_encrypt($data = $password, $cipher_algo = "aes-256-cbc-hmac-sha256", $passphrase = $this->hmacKey.$userMasterPassHash);
        }
        else {
            $encryption = openssl_encrypt($data = $password, $cipher_algo = "aes-256-cbc", $passphrase = $userSalt.$this->pepper.$userMasterPassHash);
        }
        return $encryption;
    }
    
    public function addNewPassword($address, $login, $password, $description) {
        if($this->valid->webAddressValidation($address) && $this->valid->addPasswordLoginValidation($login) && $this->valid->addPasswordPasswordValidation($password) && isset($_SESSION['userID'])) {
            $userSalt = $this->getUserSaltFromDB($password, $_SESSION["userID"]);
            $userMasterPassHash = $this->getUserMasterPasswordAsHash($_SESSION["userID"]);
            $passwordHash = $this->getEncryption($password, $userSalt, $userMasterPassHash);
            if($userSalt != 1) {
                try {
                    $this->db->mysqli->query(
                        sprintf("INSERT INTO password VALUES(NULL, '%s', %s, '%s', '%s', '%s')", 
                        mysqli_real_escape_string($this->db->mysqli, $passwordHash),
                        mysqli_real_escape_string($this->db->mysqli, $_SESSION["userID"]),
                        mysqli_real_escape_string($this->db->mysqli, $address),
                        mysqli_real_escape_string($this->db->mysqli, $description),
                        mysqli_real_escape_string($this->db->mysqli, $login)));  
                    $_SESSION['addPasswordSuccess'] = true;
                    header('Location: passwords.php');
                    return true;
                } catch (mysqli_sql_exception $exception) {
                    $_SESSION['addPasswordSuccess'] = false;
                    header('Location: addPassword.php');
                    return false;
                }
            }
            else {
                $_SESSION['addPasswordSuccess'] = false;
                header('Location: addPassword.php');
                return false;
            }  
        }
        else {
            header('Location: addPassword.php');
            return false;
        }
    }
    
    public function getUserMasterPasswordAsHash($userID) {
        if($userID == null) {
            return null;
        }
        try {
            $result = $this->db->mysqli->query(
                sprintf("SELECT password_hash FROM user WHERE id = %s", 
                mysqli_real_escape_string($this->db->mysqli, $userID))); 
            $row = $result->fetch_assoc();
            return $row["password_hash"];
        } catch (mysqli_sql_exception $exception) {;
            return null;
        }
    }
    
    public function getUserPasswords($userID) {
        $data["passwordID"] = [];
        $data["address"] = [];
        $data["login"] = [];
        $data["description"] = [];
        $data["passwordOwner"] = [];
        $data["isOwnPassword"] = [];
        $data["sharedPassLogin"][] = [];
        $i = 0;
        try {
            $result = $this->db->mysqli->query(
                sprintf("SELECT * FROM password WHERE id_user = %s", 
                mysqli_real_escape_string($this->db->mysqli, $userID))); 
            while($row = $result->fetch_assoc()) {
                array_push($data["passwordID"], $row["id"]);
                array_push($data["address"], $row["web_address"]);
                array_push($data["login"], $row["login"]);
                array_push($data["description"], $row["description"]); 
                array_push($data["passwordOwner"], $_SESSION["userLogin"]); 
                array_push($data["isOwnPassword"], true); 
                $data["sharedPassLogin"][$i] = $this->db->getLoginsFromSharedPassword($row["id"]);
                $i++;
            }
            $sharedData = $this->db->getUserSharedPasswords($userID);
            $newData = $this->joinTwoArrays($data, $sharedData);
        } catch (mysqli_sql_exception $exception) {
            echo null;
            return false;
        }
        echo json_encode($newData);
        return true;
    }
    
    public function joinTwoArrays($data, $newData) {
        for($i=0; $i<count($newData["passwordID"]); $i++) {
            array_push($data["passwordID"], $newData["passwordID"][$i]);
            array_push($data["address"], $newData["address"][$i]);
            array_push($data["login"], $newData["login"][$i]);
            array_push($data["description"], $newData["description"][$i]); 
            array_push($data["passwordOwner"], $newData["passwordOwner"][$i]); 
            array_push($data["isOwnPassword"], $newData["isOwnPassword"][$i]);
            array_push($data["sharedPassLogin"], []);
        }
        return $data;
    }
    
    public function deleteUserPassword($userID, $passwordID) {
        if($this->valid->deleteUserPasswordValidation($userID, $passwordID)) { 
            try {
                $result = $this->db->mysqli->query(
                    sprintf("DELETE FROM password WHERE id = %s AND id_user = %s", 
                    mysqli_real_escape_string($this->db->mysqli, $passwordID),
                    mysqli_real_escape_string($this->db->mysqli, $userID))); 
                $_SESSION['deleteUserPasswordSuccess'] = true;
                header('Location: passwords.php');
                return true;
            } catch (mysqli_sql_exception $exception) {
                $_SESSION['deleteUserPasswordSuccess'] = false;
                header('Location: passwords.php');
                return false;
            }
        }
        else {
            header('Location: passwords.php');
            return false;
        }
    }
    
    public function showPassword($userID, $passwordID) {
        $decryptedPassword = null;
        if($this->valid->showPasswordValidation($userID, $passwordID)) {
            $masterUserPassHash = $this->getUserMasterPasswordAsHash($userID);
            try {
                $result = $this->db->mysqli->query(
                    sprintf("SELECT p.password, u.salt FROM password p INNER JOIN user u ON p.id_user = u.id WHERE p.id = %s AND p.id_user = %s", 
                    mysqli_real_escape_string($this->db->mysqli, $passwordID),
                    mysqli_real_escape_string($this->db->mysqli, $userID)));
                $row = $result->fetch_assoc();
                $encryptedPass = $row["password"];
                $salt = $row["salt"];
                if($salt == null || $salt == "") {
                    $decryptedPassword = openssl_decrypt($data = $encryptedPass, $cipher_algo = "aes-256-cbc-hmac-sha256", $passphrase = $this->hmacKey.$masterUserPassHash);
                }
                else {
                    $decryptedPassword = openssl_decrypt($data = $encryptedPass, $cipher_algo = "aes-256-cbc", $passphrase = $salt.$this->pepper.$masterUserPassHash);
                }
                echo $decryptedPassword;
                return true;
            } catch (mysqli_sql_exception $exception) {
                echo null;
                return false;
            }
        }
        else {
            echo null;
            return false;
        }
    }
    
    public function changeMasterPassword($userID, $oldPass, $newPass, $newPass2) {
        if($this->valid->changeMasterPasswordValidation($userID, $oldPass, $newPass, $newPass2, $this->pepper, $this->hmacKey)) { 
            $newSalt = null;
            $this->db->mysqli->begin_transaction();
            try {
                $result = $this->db->mysqli->query(
                    sprintf("SELECT salt FROM user WHERE id = %s", 
                    mysqli_real_escape_string($this->db->mysqli, $userID)));
                $row = $result->fetch_assoc();
                $oldSalt = $row["salt"];
                $oldUserMasterPassHash = $this->getUserMasterPasswordAsHash($userID);
                if($oldSalt == null || $oldSalt == "") {
                    $newPasswordHash = hash_hmac("sha512", $newPass, $this->hmacKey);
                    $this->changeAllPasswordsAfterMPChanging($db, $userID, null, null, $oldUserMasterPassHash, $newPasswordHash);
                }
                else {
                    $newSalt = $this->generateSalt();
                    $newPasswordHash = hash("sha512", $newSalt.$this->pepper.$newPass);
                    $this->changeAllPasswordsAfterMPChanging($db, $userID, $oldSalt, $newSalt, $oldUserMasterPassHash, $newPasswordHash);
                }
                $this->db->mysqli->query(
                    sprintf("UPDATE user SET password_hash = '%s', salt = '%s' WHERE id = %s", 
                    mysqli_real_escape_string($this->db->mysqli, $newPasswordHash),
                    mysqli_real_escape_string($this->db->mysqli, $newSalt),
                    mysqli_real_escape_string($this->db->mysqli, $userID)));
                $this->db->mysqli->commit(); 
                $_SESSION["changeMasterPasswordSuccess"] = true;
                header("Location: account.php");
                return true;
            } catch (mysqli_sql_exception $exception) {
                $this->db->mysqli->rollback();
                $_SESSION["changeMasterPasswordSuccess"] = false;
                header("Location: account.php");
                return false;
            }
        }
        else {
            header("Location: account.php");
            return false;
        }
    }
    
    public function changeAllPasswordsAfterMPChanging($db, $userID, $oldSalt, $newSalt, $oldMasterPassHash, $newMasterPassHash) {
        $ids = [];
        $encryptedpasswords = [];
        $result = $this->db->mysqli->query(
            sprintf("SELECT id, password FROM password WHERE id_user = %s", 
            mysqli_real_escape_string($this->db->mysqli, $userID)));
        while($row = $result->fetch_assoc()) {
            array_push($ids, $row["id"]);
            array_push($encryptedpasswords, $row["password"]);
        }
        
        if($oldSalt != null) { //For passwords with salt
            for($i=0; $i<count($ids); $i++) {
                $decryptedPassword = openssl_decrypt($data = $encryptedpasswords[$i], $cipher_algo = "aes-256-cbc", $passphrase = $oldSalt.$this->pepper.$oldMasterPassHash);
                $newEncryptedPassword = openssl_encrypt($data = $decryptedPassword, $cipher_algo = "aes-256-cbc", $passphrase = $newSalt.$this->pepper.$newMasterPassHash);
                $this->db->mysqli->query(
                    sprintf("UPDATE password SET password = '%s' WHERE id = %s", 
                    mysqli_real_escape_string($this->db->mysqli, $newEncryptedPassword),
                    mysqli_real_escape_string($this->db->mysqli, $ids[$i])));
            }
        }
        else { // For HMAC passwords
            for($i=0; $i<count($ids); $i++) {
                $decryptedPassword = openssl_decrypt($data = $encryptedpasswords[$i], $cipher_algo = "aes-256-cbc", $passphrase = $this->hmacKey.$oldMasterPassHash);
                $newEncryptedPassword = openssl_encrypt($data = $decryptedPassword, $cipher_algo = "aes-256-cbc", $passphrase = $this->hmacKey.$newMasterPassHash);
                $this->db->mysqli->query(
                    sprintf("UPDATE password SET password = '%s' WHERE id = %s", 
                    mysqli_real_escape_string($this->db->mysqli, $newEncryptedPassword),
                    mysqli_real_escape_string($this->db->mysqli, $ids[$i])));
            }
        }       
    }  
    
    public function isUserExistAndIsNotLogged($login, $loggedUserLogin) {
        echo $this->db->getLoginNumberWhereUserIsLogged($login, $loggedUserLogin);
    }
    
    
}