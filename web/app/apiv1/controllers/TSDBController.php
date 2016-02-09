<?php

class TSDBController extends BaseController
{
    public function getAction($request)
    {
      $tsdb = new TSDModel();
      switch ($request->url_elements[2]) {
          case 'week':
            return $tsdb->getLastWeek($request->url_elements[3]);
          break;
          case 'day':
            return $tsdb->getLastDay($request->url_elements[3]);
          break;
          case 'listPlugs':
            return $tsdb->getUsersPlugs($request->url_elements[3]);
          break;
          case 'current':
            return $tsdb->getMostRecentPower($request->url_elements[3]);
          break;
          case 'currentUser':
            return $tsdb->getMostRecentPowerUser($request->url_elements[3]);
          break;
          default:
            $this->badRequest('Need to specify time period and user id!');
          break;
      }
    }
}
