<?php
class StatusController extends BaseController
{
  public function getAction($request){
  $model = new StatusModel();
  $tsdb = new TSDModel();
  if($request->url_elements[2]=="create"){
    $model->createStatusDB();
    return true;
  }
  else {
    if(isset($request->url_elements[3])&& isset($request->url_elements[2])){
    $tsdb->addData($request->url_elements[2],$request->url_elements[3]);}
  return $model->getStatus($request->url_elements[2]);
  }
}
  public function putAction($request){
  $model = new StatusModel();
  $request->url_elements[4] == 'sensor' ? $method = "auto" : $method = "manual";
  return $model->setStatus($request->url_elements[2],$request->url_elements[3],$method);
  }

}
