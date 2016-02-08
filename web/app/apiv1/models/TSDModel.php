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
public function getLastWeek($userID, $chipID){

  if(isset($chipID)){$plugs[0]['chipID'] = $chipID;}else{$plugs = $this->getUsersPlugs($userID);}
  foreach($plugs as $plug){
    $this->getPlugEnergyUsage($plug['chipID']);

  }
  //
}
public function getUsersPlugs($userID){
  $plugArray = array();
  $sql = "SELECT chipID,locationName,chipEnabled FROM `tblUserChip` WHERE `userID` = :uid and `chipType` = 'plug'";
  $sth = $this->_db->prepare($sql);
  $sth->bindParam(':uid',$userID,PDO::PARAM_INT);
  $sth->execute();
  $results = $sth->fetchAll(PDO::FETCH_ASSOC);
  return $results;
}
protected function getPlugEnergyUsage($chipID){
  $i = 14;
    while($i>0){
      $i--;
      $tpUsage = $this->getTimePeriodEnergyUse($chipID,$i);
      $result[14-$i] = $tpUsage;

    }
    return $result;
}
protected function getTimePeriodEnergyUse($chipID,$timePeriodsAgo){
  // one time period is 12 hours
  $energyUse = 0;
  $timestampStart = time() - ($timePeriodsAgo * 12 * 60 * 60);
  $timestampEnd = time() - (($timePeriodsAgo-1) * 12 * 60 * 60);
  $sql = "SELECT *,UNIX_TIMESTAMP(`tsTime`) FROM `tblTSDB` WHERE `tsChipID` = :cid AND UNIX_TIMESTAMP(`tsTime`) > :startTime AND UNIX_TIMESTAMP(`tsTime`) < :endTime";
  $sth = $this->_db->prepare($sql);
  $sth->bindParam(':cid',$chipID,PDO::PARAM_INT);
  $sth->bindParam(':startTime',$timestampStart,PDO::PARAM_INT);
  $sth->bindParam(':endTime',$timestampEnd,PDO::PARAM_INT);
  $sth->execute();
  $results = $sth->fetchAll(PDO::FETCH_ASSOC);
  $i = 0;
  foreach($results as $row){
    $power = $row['tsValue'] / 1000;
    $startTime = $row['UNIX_TIMESTAMP(`tsTime`)'];
    if($i < count($results)){
    $endTime = $results[$i+1]['UNIX_TIMESTAMP(`tsTime`)'];
    }
    else {
      $endTime = $timestampEnd;
    }
    $diff = $endTime - $startTime;
    $diffHours = $diff / 3600;
    $energyUse += $diffHours * $power;
  }
  return $energyUse;
}
}
