<?php
class StatusController extends BaseController
{
  public function getAction($request){
  $model = new StatusModel();
  return $model->getStatus($request->url_elements[2]);
  }
  public function putAction($request){
  $model = new StatusModel();
  return $model->setStatus($request->url_elements[2],$request->url_elements[3]);
  }

}
