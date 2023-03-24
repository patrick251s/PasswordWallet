<?php

class Database {
    public $mysqli; //uchwyt do BD
    private $disableTime = 600; //600s = 10min
    private $sessionDisableTime = 600;

    public function __construct() {
        $this->mysqli = new mysqli('localhost', 'root', '', 'passwordWallet');
        
        //Sprawdzenie połączenia
        if($this->mysqli->connect_errno) {
            printf("Nie udało się połączyć z serwerem! %s\n", $this->mysqli->connect_errno);
            exit();
        }
        
        //Zmiana kodowania na UTF8
        if($this->mysqli->set_charset("utf8")){
           //Udało się zmienić kodowanie
        }
    }
    
    function __destruct() {
        $this->mysqli->close();
    }
    
    public function getLoginNumber($login) {
        $result = $this->mysqli->query(
                sprintf("SELECT id FROM user WHERE login='%s'",
                mysqli_real_escape_string($this->mysqli, $login))); 
        $foundedLoginsNumber = $result->num_rows;
        return $foundedLoginsNumber;
    }
    
    public function getAllUserPassword($userID) {
        $result = $this->mysqli->query(
                sprintf("SELECT id FROM password WHERE id_user=%s",
                mysqli_real_escape_string($this->mysqli, $userID))); 
        return $result;
    }
    
    public function saveUserLoginActivity($userID, $time, $isCorrect, $sessionID) {
        if(is_null($userID)) {
            $sql = sprintf("INSERT INTO user_login VALUES(NULL, '%s', '%s', null, '%s')",
                mysqli_real_escape_string($this->mysqli, $time),
                mysqli_real_escape_string($this->mysqli, $isCorrect),
                mysqli_real_escape_string($this->mysqli, $sessionID)); 
        }
        else {
            $sql =  sprintf("INSERT INTO user_login VALUES(NULL, '%s', '%s', %s, '%s')",
                mysqli_real_escape_string($this->mysqli, $time),
                mysqli_real_escape_string($this->mysqli, $isCorrect),
                mysqli_real_escape_string($this->mysqli, $userID),
                mysqli_real_escape_string($this->mysqli, $sessionID)); 
        }
        try {
            $result = $this->mysqli->query($sql);
        } catch (mysqli_sql_exception $exception) {
                
        }
    }
    
    public function getUserLoginActivity($userID) {
        $data["time"] = [];
        $data["isCorrect"] = [];
        try {
            $result = $this->mysqli->query(
                sprintf("SELECT time, isCorrect FROM user_login WHERE id_user = %s ORDER BY time DESC",
                mysqli_real_escape_string($this->mysqli, $userID))); 
            while($row = $result->fetch_assoc()) {
                array_push($data["time"], $row["time"]);
                array_push($data["isCorrect"], $row["isCorrect"]);
            }
        } catch (mysqli_sql_exception $exception) {
            echo null;
            return false;
        }
        echo json_encode($data);
        return true;
    }
    
    public function getUserLoginAttempts($userID) {
        $data["time"] = [];
        $data["isCorrect"] = [];
        try {
            $result = $this->mysqli->query(
                sprintf("SELECT time, isCorrect FROM user_login WHERE id_user = %s ORDER BY time DESC",
                mysqli_real_escape_string($this->mysqli, $userID))); 
            while($row = $result->fetch_assoc()) {
                array_push($data["time"], $row["time"]);
                array_push($data["isCorrect"], $row["isCorrect"]);
            }
        } catch (mysqli_sql_exception $exception) {
            
        }
        return $data;
    }
    
    public function disableUser($userID, $failLoginAttempts) {
        $blockadeTime = time() + $this->disableTime*($failLoginAttempts-2);
        $blockadeDate = date("Y-m-d H:i:s", $blockadeTime);
        try {
            $result = $this->mysqli->query(
                sprintf("UPDATE user SET lockout_time = '%s' WHERE id = %s",
                mysqli_real_escape_string($this->mysqli, $blockadeDate),
                mysqli_real_escape_string($this->mysqli, $userID))); 
            
        } catch (mysqli_sql_exception $exception) {
            return false;
        }
        return $blockadeDate;
    }
    
    public function getUserLockout($userID) {
        try {
            $result = $this->mysqli->query(
                sprintf("SELECT lockout_time FROM user WHERE id = %s",
                mysqli_real_escape_string($this->mysqli, $userID))); 
            $row = $result->fetch_assoc();
            return $row['lockout_time'];
        } catch (mysqli_sql_exception $exception) {
            return null;
        }
    }
    
    public function getLoginAttemptsBySessionID($sessionID) {
        $data['isCorrect'] = [];
        try {
            $result = $this->mysqli->query(
                sprintf("SELECT time, isCorrect FROM user_login WHERE session_id = '%s' ORDER BY time DESC",
                mysqli_real_escape_string($this->mysqli, $sessionID))); 
            while($row = $result->fetch_assoc()) {
                array_push($data["isCorrect"], $row["isCorrect"]);
            }
        } catch (mysqli_sql_exception $exception) {
            return null;
        }
        return $data;
    }
    
    public function getLoginNumberWhereUserIsLogged($login, $loggedUserLogin) {
        $result = $this->mysqli->query(
                sprintf("SELECT id FROM user WHERE login='%s' AND login<>'%s'",
                mysqli_real_escape_string($this->mysqli, $login),
                mysqli_real_escape_string($this->mysqli, $loggedUserLogin))); 
        return $result->num_rows;
    }
    
    public function saveSharingPassword($loggedUserID, $passID, $userLoginsToShare) {
        $this->mysqli->begin_transaction();
        try {
            for($i=0; $i<count($userLoginsToShare); $i++) {
                $result = $this->mysqli->query(
                    sprintf("SELECT id FROM user WHERE login='%s'",
                    mysqli_real_escape_string($this->mysqli, $userLoginsToShare[$i])));
                $row = $result->fetch_assoc();
                $idToShare = $row["id"];
                $this->mysqli->query(
                    sprintf("INSERT INTO shared_password VALUES (NULL, %s, %s, %s)",
                    mysqli_real_escape_string($this->mysqli, $loggedUserID),
                    mysqli_real_escape_string($this->mysqli, $idToShare),
                    mysqli_real_escape_string($this->mysqli, $passID))); 
                $idToShare = null;
            }
            $this->mysqli->commit(); 
            $_SESSION["sharePasswordStatus"] = true;;
            echo true;
        } catch (Exception $ex) {
            $_SESSION["sharePasswordStatus"] = false;
            echo false;
        }
    }
    
    public function getLoginsFromSharedPassword($passID) {
        $data = [];
        $result = $this->mysqli->query(
            sprintf("SELECT u.login AS userLogin FROM shared_password s INNER JOIN user u ON s.id_guest=u.id WHERE id_password=%s",
            mysqli_real_escape_string($this->mysqli, $passID))); 
        while($row = $result->fetch_assoc()) {
            array_push($data, $row["userLogin"]);
        }
        return $data;
    }
    
    public function removeUserFromSharing($loggedUserID, $removedUserLogin, $passID) {
        try {
            $removedUserID = $this->getUserID($removedUserLogin);
            $this->mysqli->query(
                sprintf("DELETE FROM shared_password WHERE id_owner=%s AND id_guest=%s AND id_password=%s",
                mysqli_real_escape_string($this->mysqli, $loggedUserID),
                mysqli_real_escape_string($this->mysqli, $removedUserID),
                mysqli_real_escape_string($this->mysqli, $passID))); 
            $_SESSION["removeUserSharingStatus"] = true;
        } catch (mysqli_sql_exception $exception) {
            $_SESSION["removeUserSharingStatus"] = false;
            return false;
        }
    }
    
    public function getUserID($login) {
        $result = $this->mysqli->query(
            sprintf("SELECT id FROM user WHERE login='%s'",
            mysqli_real_escape_string($this->mysqli, $login))); 
        $row = $result->fetch_assoc();
        return $row["id"];
    }
    
    public function getUserSharedPasswords($loggedUserID) {
        $data["passwordID"] = [];
        $data["address"] = [];
        $data["login"] = [];
        $data["description"] = [];
        $data["passwordOwner"] = [];
        $data["isOwnPassword"] = [];
        $data["sharedPassLogin"][] = [];
        $i = 0;
        try {
            $result = $this->mysqli->query(
                sprintf("SELECT s.id AS sharedID, p.web_address AS addr, p.login AS log, s.id_password AS idP, p.description AS descr, u.login AS ownL FROM shared_password s INNER JOIN password p ON s.id_password=p.id INNER JOIN user u ON s.id_guest=u.id WHERE u.id = %s", 
                mysqli_real_escape_string($this->mysqli, $loggedUserID))); 
            while($row = $result->fetch_assoc()) {
                array_push($data["passwordID"], $row["idP"]);
                array_push($data["address"], $row["addr"]);
                array_push($data["login"], $row["log"]);
                array_push($data["description"], $row["descr"]); 
                array_push($data["passwordOwner"], $this->getOwner($row["sharedID"])); 
                array_push($data["isOwnPassword"], false); 
                $data["sharedPassLogin"][$i] = [];
                $i++;
            }
        } catch (mysqli_sql_exception $exception) {

        }
        return $data;
    }
    
    public function getOwner($sharedID) {
        $result = $this->mysqli->query(
            sprintf("SELECT u.login AS log FROM user u INNER JOIN shared_password s ON u.id=s.id_owner WHERE s.id=%s",
            mysqli_real_escape_string($this->mysqli, $sharedID))); 
        $row = $result->fetch_assoc();
        return $row["log"];
    }
}
    