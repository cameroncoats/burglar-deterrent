<?php
class TSDBController extends BaseController
{
  public function getAction($request){
  $tsdb = new TSDModel();
  if($request->url_elements[2]=="week"){
    return $tsdb->getLastWeek($request->url_elements[3]);

  }
  else {
    if($request->url_elements[2]=="listPlugs"){
      return $tsdb->getUsersPlugs($request->url_elements[3]);
    }
    else {
      # code...

    $this->badRequest('Need to specify time period and user id!');
  }}
}


}
