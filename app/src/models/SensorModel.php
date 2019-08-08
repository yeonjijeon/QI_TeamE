<?php

namespace App\Model;

final class SensorModel extends BaseModel
{
    public function select_sensor($user)
    {
        $sql = "SELECT SSN,MAC_ADD,DEVICE FROM SENSOR WHERE USN = ? AND REG_ACTIVE = 1 ORDER BY SSN";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($user['USN']));
        $results = $sth->fetchAll();
        return $results;
    }

    public function update_REG_ACTIVE ($user)
    {
        $sql = "UPDATE SENSOR SET REG_ACTIVE = ? WHERE SSN = ? AND USN = ?";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($user['REG_ACTIVE'],$user['SSN'],$user['USN']));
        $results = $sth->rowCount();
        return $results;
    }

    // public function update_sensor_info_set_regAtive_by_USN ($user)
    // {
    //     $sql = "UPDATE Sensor_info SET regActive = ? where USN = ?";
    //     $sth = $this->db->prepare($sql);
    //     $sth->execute(array($user['regActive'],$user['USN']));
    //     $results = $sth->rowCount();
    //     return $results;
    // }

    public function select_sensor_by_USN_AND_MAC ($user)
    {
        $sql = "SELECT * FROM SENSOR WHERE USN = ? AND MAC_ADD = ?";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($user['USN'],$user['MAC_ADD']));
        $results = $sth->fetchAll();
        return $results;
    }
    public function select_USN_by_MAC ($user)
    {
       
       $sql = "SELECT * FROM SENSOR WHERE MAC_ADD = ?";
       $sth = $this->db->prepare($sql);
       $sth->execute(array($user['MAC_ADD']));
       $results = $sth->fetchAll();
       return $results;
    }
    public function select_SSN_by_MAC ($user)
    {
        $sql = "SELECT SSN FROM SENSOR WHERE MAC_ADD = ?";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($user['MAC_ADD']));
        $results = $sth->fetchAll();
        return $results;
    }
    public function insert_sensor ($user)
    {
        $sql = "INSERT INTO SENSOR VALUES(NULL, ?, ?, ?, ?)";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($user['USN'],$user['MAC_ADD'],$user['DEVICE'],$user['REG_ACTIVE']));
        $results = $sth->rowCount();
        return $results;
    }

}
