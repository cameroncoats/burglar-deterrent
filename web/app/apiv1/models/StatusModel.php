<?php
class StatusModel extends BaseModel
{
public function getStatus($id){
 $sql = "SELECT * FROM `tblStatus` WHERE `id` = :id";
 $sth = $this->_db->prepare($sql);
 $sth -> bindParam(":id",$id,PDO::PARAM_INT);
 $sth -> execute();
 $results = $sth->fetch();
 $results['button'] = $results['status'];
 return $results;
}
public function setStatus($id,$status,$method){
  $sql = "UPDATE `tblStatus` SET `status`=:status,`method`=:method WHERE `id`= :id";
  $sth = $this->_db->prepare($sql);
  $sth -> bindParam(":status",$status,PDO::PARAM_STR);
  $sth -> bindParam(":method",$method,PDO::PARAM_STR);
  $sth -> bindParam(":id",$id,PDO::PARAM_INT);
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
