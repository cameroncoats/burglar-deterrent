<?php
class StatusModel extends BaseModel
{
public function getStatus($id){
 $sql = "SELECT * FROM `tblStatus` WHERE `id` = :id";
 $sth = $this->_db->prepare($sql);
 $sth -> bindParam(":id",$id,PDO::PARAM_INT);
 $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
 return $results;
}
public function setStatus($id,$status){
  $sql = "UPDATE `tblStatus` SET `status`=:status WHERE `id`= :id";
  $sth = $this->_db->prepare($sql);
  $sth -> bindParam(":status",$status,PDO::PARAM_STR);
  $sth -> bindParam(":id",$is,PDO::PARAM_INT);
  $sth -> execute();
  return true;
}
public function createStatusDB(){
  $this->_db->exec("CREATE TABLE tblStatus (
                      id INTEGER PRIMARY KEY,
                      status TEXT,
                      method TEXT)");
}
}
