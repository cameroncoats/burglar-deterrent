<?php
class TSDModel extends BaseModel
{
  /**
   * Simple function to add time series data to the database
   * @param (int) $chipid The chip ID of the device sending information
   * @param (varchar) $value  The data value to write to the DB
   * @return (bool) true on successful insert
   */
public function addData($chipid,$value){
 $sql = "INSERT INTO `tblTSDB` (`tsTime`,`tsData`,`tsChipID`) VALUES (CURRENT_TIMESTAMP,:data,:chip)";
 $sth = $this->_db->prepare($sql);
 $sth -> bindParam(":data",$value,PDO::PARAM_STR);
 $sth -> bindParam(":chip",$chipid,PDO::PARAM_INT);
 return $sth -> execute();
}
/**
 * Function gets the energy usage of the last week, split into 12 hour blocks
 * @param  (int) $userID The ID of the user requesting power usage
 *                       	OR
 * @param  (int) $chipID The chip ID if power for a specific plug is requested
 * @return array         Returns an array of 14 values for average energy usage over 12 hours
 */
public function getLastWeek($userID, $chipID){
  $i=0;
  if(isset($chipID)){$plugs[0]['chipID'] = $chipID;}else{$plugs = $this->getUsersPlugs($userID);}
  foreach($plugs as $plug){
    $pTotal[$i] = $this->getPlugEnergyUsageWeek($plug['chipID']);
    $i++;
  }
  $totalArray = array();

  foreach ($pTotal as $k=>$plugTotals) {
    foreach ($plugTotals as $timePeriod=>$power) {
      $totalArray[$timePeriod]+=$power;
    }
  }
  return $totalArray;
}
/**
 * Function sums the most recent values for each of the plugs
 * @param  (int) $userID the ID of the user requesting the info
 * @return (float)   Returns a numerical value for power usage
 */
public function getMostRecentPowerUser($userID){
  $plugs = $this->getUsersPlugs($userID);
  $totalPower = 0;
  foreach($plugs as $plug){
    $plugPower = $this->getMostRecentPower($plug['chipID']);
    $totalPower += $plugPower;
  }
  return $totalPower;
}
/**
 * Function returns the most recent power for a single chip ID
 * @param  (int) $chipID The chip to find the power usage for
 * @return (float)         The most recent power usage of the chip
 */
public function getMostRecentPower($chipID){
  $sql = "SELECT `tsData` FROM `tblTSDB` WHERE `tsChipID` = :cid ORDER BY `tsTime` DESC LIMIT 0,1";
  $sth = $this->_db->prepare($sql);
  $sth->bindParam(':cid',$chipID,PDO::PARAM_INT);
  $sth->execute();
  $results = $sth->fetchAll(PDO::FETCH_ASSOC);
  //var_dump($results);
  return $results[0]['tsData'];
}
/**
 * Returns an array of all plugs associated with a user
 * @param  int $userID The id of the user to select by
 * @return array         returns an array of all plugs
 */
public function getUsersPlugs($userID){
  $plugArray = array();
  $sql = "SELECT chipID,locationName,chipEnabled FROM `tblUserChip` WHERE `userID` = :uid and `chipType` = 'plug'";
  $sth = $this->_db->prepare($sql);
  $sth->bindParam(':uid',$userID,PDO::PARAM_INT);
  $sth->execute();
  $results = $sth->fetchAll(PDO::FETCH_ASSOC);
  return $results;
}
protected function getPlugEnergyUsageWeek($chipID){
  $i = 14;
    while($i>0){
      $i--;
      $tpUsage = $this->getTimePeriodEnergyUse($chipID,$i);
      $result[14-$i] = $tpUsage;

    }
    return $result;
}
protected function getPlugEnergyUsageDay($chipID){
  $i = 24;
    while($i>0){
      $i--;
      $tpUsage = $this->getTimePeriodEnergyUse($chipID,$i,3600);
      $result[24-$i] = $tpUsage;

    }
    return $result;
}
protected function getTimePeriodEnergyUse($chipID,$timePeriodsAgo,$timePeriodLength = 43200){
  $energyUse = 0;
  $timestampStart = time() - ($timePeriodsAgo * $timePeriodLength);
  $timestampEnd = time() - (($timePeriodsAgo-1) * $timePeriodLength);
  $sql = "SELECT *,UNIX_TIMESTAMP(`tsTime`) FROM `tblTSDB` WHERE `tsChipID` = :cid AND UNIX_TIMESTAMP(`tsTime`) > :startTime AND UNIX_TIMESTAMP(`tsTime`) < :endTime";
  $sth = $this->_db->prepare($sql);
  $sth->bindParam(':cid',$chipID,PDO::PARAM_INT);
  $sth->bindParam(':startTime',$timestampStart,PDO::PARAM_INT);
  $sth->bindParam(':endTime',$timestampEnd,PDO::PARAM_INT);
  $sth->execute();
  $results = $sth->fetchAll(PDO::FETCH_ASSOC);
  $i = 0;
  foreach($results as $row){
    $power = $row['tsData'] / 1000;
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
    echo "Row $i \n";
    echo "Power: $power kW\n";
    echo "Start $startTime, End $endTime \n";
    echo "Time Period: $diffHours hours \n";
    echo "Energy: $energyUse kWh";
  }
  return $energyUse;
}
}
