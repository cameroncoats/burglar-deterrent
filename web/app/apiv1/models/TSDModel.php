<?php
class TSDModel extends BaseModel
{
public function addData($chipid,$value){
 $sql = "INSERT INTO `tblTSDB` (`tsTime`,`tsData`,`tsChipID`) VALUES (CURRENT_TIMESTAMP,:data,:chip)";
 $sth = $this->_db->prepare($sql);
 $sth -> bindParam(":data",$value,PDO::PARAM_STR);
 $sth -> bindParam(":chip",$chipid,PDO::PARAM_INT);
 $sth -> execute();
}
public function getLastWeek($userID){
  $plugs = $this->getUsersPlugs($userID);

}
public function getUsersPlugs($userID){
  $plugArray = array();
  $sql = "SELECT chipID FROM `tblUserChip` WHERE `userID` = :uid and `chipType` = 'plug'";
  $sth = $this->_db->prepare($sql);
  $sth->bindParam(':uid',$userID,PDO::PARAM_INT);
  $sth->execute();
  $results = $sth->fetchAll(PDO::FETCH_ASSOC);
  var_dump($results);
  return $plugArray;
}
}
