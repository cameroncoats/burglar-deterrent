<?php
class AlertsController extends BaseController
{
  public function getAction($request){
  $model = new AlertsModel();
    if(isset($request->url_elements[2])){
      return $model->getAlerts($request->url_elements[2]);
    }
  }
  public function deleteAction($request){
    $model = new AlertsModel();
    return $model->dismissAlert($request->url_elements[2]);
  }

}
