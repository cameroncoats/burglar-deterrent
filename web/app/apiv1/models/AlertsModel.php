<?php
class AlertsModel extends BaseModel
{
public function getAlerts($id){
 $sql = "SELECT * FROM `tblAlerts` WHERE `AlertChipID` = :id";
 $sth = $this->_db->prepare($sql);
 $sth -> bindParam(":id",$id,PDO::PARAM_INT);
 $sth -> execute();
 $results = $sth->fetchAll();
 return $results;
}
public function addAlert($id,$title,$msg,$status){
  $sql = "INSERT INTO `tblAlerts` (`AlertTitle`,`AlertMsg`,`AlertChipID`,`AlertStatus`) VALUES (:title,:msg,:chipID,:status)";
  $sth = $this->_db->prepare($sql);
  $sth -> bindParam(":chipID",$id,PDO::PARAM_INT);
  $sth -> bindParam(":status",$status,PDO::PARAM_STR);
  $sth -> bindParam(":msg",$msg,PDO::PARAM_STR);
  $sth -> bindParam(":title",$title,PDO::PARAM_STR);
  $sth -> execute();
  return true;
}
public function dismissAlert($id){
  $sql = "DELETE FROM `tblAlerts` WHERE `AlertID`= :id";
  $sth = $this->_db->prepare($sql);
  $sth -> bindParam(":id",$id,PDO::PARAM_INT);
  $sth -> execute();
  return true;
}
}
