<?php
class TSDModel extends BaseModel
{
  /**
   * Simple function to add time series data to the database
   * @param (int) $chipid The chip ID of the device sending information
   * @param (varchar) $value  The data value to write to the DB
   * @return (bool) true on successful insert
   */
public function addData($chipid,$value,$newStatus,$currentStatus){
  ($currentStatus>0)?$currentStatus = "away":$currentStatus = "home";
  if($currentStatus == "away"){
 $sql = "INSERT INTO `tblTSDB` (`tsTime`,`tsData`,`tsChipID`) VALUES (CURRENT_TIMESTAMP,:data,:chip)";
 $sth = $this->_db->prepare($sql);
 $sth -> bindParam(":data",$value,PDO::PARAM_STR);
 $sth -> bindParam(":chip",$chipid,PDO::PARAM_INT);
 if($value < 30){
   $alert = new AlertsModel();
   $alert->addAlert($chipid,"Low Power Draw","Your Eversafe plug was activated but no power is being drawn. Please check the connected appliance is working properly.","danger");
 }
 return $sth -> execute();
 }
 else{
   return true;
 }
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
 * Function gets the energy usage of the last day, split into 1 hour blocks
 * @param  (int) $userID The ID of the user requesting power usage
 *                       	OR
 * @param  (int) $chipID The chip ID if power for a specific plug is requested
 * @return array         Returns an array of 14 values for average energy usage over 12 hours
 */
public function getLastDay($userID, $chipID){
  $i=0;
  if(isset($chipID)){$plugs[0]['chipID'] = $chipID;}else{$plugs = $this->getUsersPlugs($userID);}
  foreach($plugs as $plug){
    $pTotal[$i] = $this->getPlugEnergyUsageDay($plug['chipID']);
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
  $status = new StatusModel();
  if($status->getStatus($chipID)=="home"){ return 0;}
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
      $tpUsage = $this->getTimePeriodEnergyUse($chipID,$i,12*60*60);
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
  $sql = "SELECT *,UNIX_TIMESTAMP(`tsTime`) FROM `tblTSDB` WHERE `tsChipID` = :cid AND UNIX_TIMESTAMP(`tsTime`) > :startTime AND UNIX_TIMESTAMP(`tsTime`) < :endTime ORDER BY UNIX_TIMESTAMP(`tsTime`) DESC";
  $sth = $this->_db->prepare($sql);
  $sth->bindParam(':cid',$chipID,PDO::PARAM_INT);
  $sth->bindParam(':startTime',$timestampStart,PDO::PARAM_INT);
  $sth->bindParam(':endTime',$timestampEnd,PDO::PARAM_INT);
  $sth->execute();
  $results = $sth->fetchAll(PDO::FETCH_ASSOC);
  $i = 0;
  foreach($results as $row){
    $power = 0;
    // convert power to kW
    $power = $row['tsData'] / 1000;
    // define start time as unix stamp
    $startTime = $row['UNIX_TIMESTAMP(`tsTime`)'];
    // if there are more results to come
    if(isset($results[$i+1])){
      // use the start of the next result as the end time
    $endTime = $results[$i+1]['UNIX_TIMESTAMP(`tsTime`)'];
    }
    else {
      // or if there are no more results,
      // use the end of the time period as the end time
      echo "Using end of TP as end time \n";
      $endTime = $timestampEnd;
    }
    // the difference is the end time - the start time
    //
    $diff = $endTime - $startTime;
    $diffHours = $diff / 3600;
    $energyUseThis += ($diffHours * $power);
    $energyUse += $energyUseThis;
    $pTotal += $power;
    echo "\t Data point power: $power \n\t energy this iteration: $energyUseThis \n\t energy so far: $energyUse \n\t time diff: $diff s, $diffHours hours \n\t Timestamp: $startTime \n\t Next Timestamp: $endTime";

  }// end of foreach
  echo "$timePeriodsAgo time periods ago \n";
      echo "Row $i \n";
  $powerAvg = $pTotal / $i;
  echo "Power: $powerAvg kW\n";
  echo "Start $timestampStart, End $timestampEnd \n";
  $diffEcho = $timestampEnd - $timestampStart;
  $diffEH = $diffEcho/3600;;
  echo "Time Period: $diffEH hours, $diffEcho seconds \n";
  echo "Energy: $energyUse kWh \n";
  return $energyUse;
}
}
