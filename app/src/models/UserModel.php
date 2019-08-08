<?php

namespace App\Model;

final class UserModel extends BaseModel 
{

    public function duplicate_checking($user)
    {
        $sql = "SELECT USN from USERS WHERE EMAIL = ?";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($user['EMAIL']));
        $results = $sth->fetchAll();
        return $results;
    }
    public function insert_user($user)
    {
            $sql = "INSERT INTO USERS (EMAIL,HASHED_PW,FNAME,LNAME,PHONE,NONCE,IS_ACTIVE,IS_SIGNIN) VALUES (?,?,?,?,?,?,?,?)";
            $sth = $this->db->prepare($sql);
            $sth->execute(array( 
                $user['EMAIL'],
                $user['HASHED_PW'],
                $user['FNAME'],
                $user['LNAME'],
                $user['PHONE'],
                $user['NONCE'],
                $user['IS_ACTIVE'],
                $user['IS_SIGNIN']
            ));
            $results = $sth;
            return $results;
       
    }

    public function select_email($nonce)
    {
        $sql = "SELECT EMAIL from USERS WHERE NONCE = ?";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($nonce));
        $results = $sth->fetchAll();
        $results['count'] = $sth->rowCount();
        return $results;
    }

    public function update_active($email)
    {
        $sql = "UPDATE USERS SET IS_ACTIVE=1, NONCE = '' WHERE EMAIL = ?";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($email[0]['EMAIL']));
        $results = $sth !== false ? 0 : 1;
        return $results;
    }



    public function select_USN_PW($user)
    {
        $sql = "SELECT USN,HASHED_PW FROM USERS WHERE EMAIL = ?";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($user['Email']));
        $results = $sth->fetch();
        return $results;
    }


    public function select_USN_PW_by_usn($user)
    {
        $sql = "SELECT HASHED_PW FROM USERS WHERE USN = ?";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($user['USN']));
        $results = $sth->fetch();
        return $results;
    }


    public function delete_is_active_zero($nonce)
    {
        $sql = "DELETE FROM USERS where NONCE = ? AND IS_ACTIVE = 1";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($nonce));
        $results = $sth !== false ? 0 : 1;
        return $results;
    }


    public function update_user_set_IS_ACTIVE($user)
    {
        $sql = "UPDATE USERS SET IS_ACTIVE = ? WHERE USN = ?";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($user['IS_ACTIVE'], $user['USN']));
        $results = $sth !== false ? 0 : 1;
        return $results;
    }

    public function update_user_set_IS_SIGNIN($user)
    {
        $sql = "UPDATE USERS SET IS_SIGNIN = ? WHERE USN = ?";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($user['IS_SIGNIN'], $user['USN']));
        $results = $sth !== false ? 0 : 1;
        return $results;
    }


    public function update_user_set_password($user)
    {
        $sql = "UPDATE USERS SET HASHED_PW = ? WHERE USN = ?";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($user['new_password'], $user['USN']));
        $results = $sth !== false ? 0 : 1;
        return $results;
    }

    public function select_IS_ACTIVE($user)
    {
        $sql = "SELECT IS_ACTIVE FROM USERS WHERE USN=?";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($user['USN']));
        $results = $sth->fetch();
        return $results;
    }
    
    public function select_HASHED_PW($user)
    {
        $sql = "SELECT HASHED_PW FROM USERS WHERE USN = ?";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($user['USN']));
        $results = $sth->fetch();
        return $results;
    }

    public function delete_user($user)
    {
        $sql = "DELETE FROM USERS WHERE USN=? ";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($user['USN']));
        // $sth->fetch();
        $results = $sth !== false ? 0 : 1;
        return $results;
    }

    public function update_user_set_NONCE($user)
    {
        $sql = "UPDATE USERS SET NONCE = ? WHERE USN = ?";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($user['NONCE'], $user['USN']));
        $results = $sth !== false ? 0 : 1;
        return $results;
    }

    public function update_user_set_password_NONCE($user)
    {
        $sql = "UPDATE USERS SET HASHED_PW = ? , NONCE = ? WHERE USN = ?";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($user['HASHED_PW'], $user['NONCE'], $user['USN']));
        $results = $sth !== false ? 0 : 1;
        return $results;
    }

    public function select_USN_by_nonce($nonce)
    {
        $sql = "SELECT USN FROM USERS WHERE NONCE = ?";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($nonce));
        $results = $sth->fetchAll();
        return $results;
    }




}