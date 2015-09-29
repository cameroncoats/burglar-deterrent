// define the app
// =============================================================================
var app = angular.module('gwa-rm', ['ui.router', 'ui.bootstrap', 'ui.mask', 'ngSanitize', 'angular-toasty'])

// routes
// =============================================================================
.config(function ($stateProvider, $urlRouterProvider) {
    // top level states
  $stateProvider
      .state('home', {
        url: '/home',
        templateUrl: 'partials/home.html',
        controller: 'mainController'
      })
      .state('settings', {
        url: '/settings',
        templateUrl: 'partials/settings.html',
        controller: 'mainController'
      })
      // catchall state
  $urlRouterProvider.otherwise('/home');
  })
  .config(['toastyConfigProvider', function(toastyConfigProvider) {
    toastyConfigProvider.setConfig({
        limit:1
    });
}])
  // Main controller
  // =============================================================================
  .controller('mainController', function ($scope, $http, $q, $window, $location, $interval, toasty) {
    /////////////////// Buttons ///////////////////////
    //
    //
      $scope.button=[];
      $http.get("apiv1/status/1").then(function(response) {
          $scope.button = response.data;
        })
    $scope.isHome = function () {
      if($scope.button!='home'){
        $http.put("apiv1/status/1/home");
      $scope.button = 'home';
      toasty.success({
        title: 'Welcome Back',
        showClose: false,
        clickToClose: true,
        timeout: 2500,
        sound: true,
        html: false,
        shake: false,
        limit: 1,
        theme: "material"
      });
    }
   };

    $scope.isAway = function () {
      if($scope.button!='away'){
      $http.put("apiv1/status/1/away");
      $scope.button = 'away';
      toasty.success({
        title: "You're Out",
        msg: "We'll keep your home safe",
        showClose: false,
        clickToClose: true,
        timeout: 2500,
        sound: true,
        html: false,
        shake: false,
        limit: 1,
        theme: "material"
      });
    }
  };

    /////////////////// Alerts ///////////////////////
    //
    // object to hold alerts
    $scope.alerts = [];
    // function to handle closing alerts
    $scope.closeAlert = function (index) {
      $scope.alerts.splice(index, 1);
    };

  }).directive('capitalize', function () {
    return {
      require: 'ngModel',
      link: function (scope, element, attrs, modelCtrl) {
        var capitalize = function (inputValue) {
          if (inputValue == undefined) inputValue = '';
          var capitalized = inputValue.toUpperCase();
          if (capitalized !== inputValue) {
            modelCtrl.$setViewValue(capitalized);
            modelCtrl.$render();
          }
          return capitalized;
        }
        modelCtrl.$parsers.push(capitalize);
        capitalize(scope[attrs.ngModel]); // capitalize initial value
      }
    };
  });

// Non Angular functions
