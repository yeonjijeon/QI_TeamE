<?php

namespace App\Model;

final class DataModel extends BaseModel
{
    public function select_SSN($data) {
        $sql = "SELECT SSN FROM SENSOR WHERE SSN = ?";
        $sth = $this->db->prepare($sql);
        $sth->execute(array( $data['SSN']));
        $results = $sth->fetchAll();
        return $results;


    }
    public function insert_into_AIR_QUALITY($data)
    {
                                       
        $sql = "INSERT INTO AIR_QUALITY (
        SSN, TIMESTAMP, CO, SO2, NO2, O3,
        TEMPERATURE, PM2.5, LAT, LNG ) VALUES
        (?, ?, ?, ?, ?, ?,
        ?, ?, ?, ? )";
        $sth = $this->db->prepare($sql);
        $sth->execute(array(
            $data['SSN'], 
            $data['TIMESTAMP'], 
            $data['CO'], 
            $data['SO2'], 
            $data['NO2'], 
            $data['O3'], 
            $data['TEMPERATURE'],
            $data['PM2.5'],
            $data['LAT'], 
            $data['LNG'], 
        ));
        $results = $sth->rowCount();
        return $results;

    }

    public function insert_into_HEART_SENSOR($data)
    {                      
        $sql = "INSERT INTO HEART_SENSOR (
        USN, TIMESTAMP, HEART_RATE,
        RR_RATE, LAT, LNG )
        values
        (?, ?, ?,
        ?, ?,? )";
        $sth = $this->db->prepare($sql);
        $sth->execute(array(
            $data['USN'], 
            $data['TIMESTAMP'], 
            $data['HEART_RATE'], 
            $data['RR_RATE'], 
            $data['LAT'], 
            $data['LNG'],  
        )); 
        $results = $sth->rowCount();
        return $results;

    }

    public function select_from_AIR_QUALITY($user)
    {   
        if($user['value']==="All") 
         $sql = "SELECT * FROM SENSOR AS S JOIN AIR_QUALITY AS A ON S.SSN = A.SSN 
         WHERE ? > LAT AND LAT > ? AND ? > LNG AND LNG > ?";
        else if ($user['value']==="CO")  $sql = "SELECT CO,LAT,LNG,TIMESTAMP,DEVICE,TEMPERATURE FROM SENSOR AS S JOIN AIR_QUALITY AS A on S.SSN = A.SSN WHERE ? > LAT AND LAT > ? AND ? > LNG AND LNG > ?" ;
        else if ($user['value']==="CO_AQI")  $sql = "SELECT CO_AQI,LAT,LNG,TIMESTAMP,DEVICE,TEMPERATURE FROM SENSOR AS S JOIN AIR_QUALITY AS A on S.SSN = A.SSN WHERE ? > LAT AND LAT > ? AND ? > LNG AND LNG > ?";
        else if ($user['value']==="NO2")  $sql = "SELECT NO2,LAT,LNG,TIMESTAMP,DEVICE,TEMPERATURE FROM SENSOR AS S JOIN AIR_QUALITY AS A on S.SSN = A.SSN WHERE ? > LAT AND LAT > ? AND ? > LNG AND LNG > ?";
        else if ($user['value']==="NO2_AQI")  $sql = "SELECT NO2_AQI,LAT,LNG,TIMESTAMP,DEVICE,TEMPERATURE FROM SENSOR AS S JOIN AIR_QUALITY AS A on S.SSN = A.SSN WHERE ? > LAT AND LAT > ? AND ? > LNG AND LNG > ?";
        else if ($user['value']==="O3")  $sql = "SELECT O3,LAT,LNG,TIMESTAMP,DEVICE,TEMPERATURE FROM SENSOR AS S JOIN AIR_QUALITY AS A on S.SSN = A.SSN WHERE ? > LAT AND LAT > ? AND ? > LNG AND LNG > ?";
        else if ($user['value']==="O3_AQI")  $sql = "SELECT O3_AQI,LAT,LNG,TIMESTAMP,DEVICE,TEMPERATURE FROM SENSOR AS S JOIN AIR_QUALITY AS A on S.SSN = A.SSN WHERE ? > LAT AND LAT > ? AND ? > LNG AND LNG > ?";
        else if ($user['value']==="SO2")  $sql = "SELECT SO2,LAT,LNG,TIMESTAMP,DEVICE,TEMPERATURE FROM SENSOR AS S JOIN AIR_QUALITY AS A on S.SSN = A.SSN WHERE ? > LAT AND LAT > ? AND ? > LNG AND LNG > ?";
        else if ($user['value']==="SO2_AQI")  $sql = "SELECT SO2_AQI,LAT,LNG,TIMESTAMP,DEVICE,TEMPERATURE FROM SENSOR AS S JOIN AIR_QUALITY AS A on S.SSN = A.SSN WHERE ? > LAT AND LAT > ? AND ? > LNG AND LNG > ?";
        else if ($user['value']==="PM2.5")  $sql = "SELECT PM2.5,LAT,LNG,TIMESTAMP,DEVICE,TEMPERATURE FROM SENSOR AS S JOIN AIR_QUALITY AS A on S.SSN = A.SSN WHERE ? > LAT AND LAT > ? AND ? > LNG AND LNG > ?";
        else if ($user['value']==="PM2.5_AQI")  $sql = "SELECT PM2.5_AQI,LAT,LNG,TIMESTAMP,DEVICE,TEMPERATURE FROM SENSOR AS S JOIN AIR_QUALITY AS A on S.SSN = A.SSN WHERE ? > LAT AND LAT > ? AND ? > LNG AND LNG > ?";
        else $sql = null;
        
        $sth = $this->db->prepare($sql);
        $sth->execute(array($user['north'],$user['south'],$user['east'],$user['west'])); 
        $results = $sth->fetchAll();
        return $results;
    }

    public function select_from_HEART_SENSOR($user)
    {     
         $sql = "SELECT HEART_RATE, TIMESTAMP, RR_RATE,LAT,LNG FROM HEART_SENSOR WHERE USN = ? ";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($user['USN'])); 
        $results = $sth->fetchAll();
        return $results;
    }

}
