// define the app
// =============================================================================
var app = angular.module('gwa-rm', ['ui.router', 'ui.bootstrap', 'ui.mask', 'ngSanitize'])

// routes
// =============================================================================
.config(function($stateProvider, $urlRouterProvider) {
    //top level states
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
// Main controller
// =============================================================================
  .controller('mainController', function($scope, $http, $q, $window, $location, $interval) {
    /////////////////// Alerts ///////////////////////
    //
    // object to hold alerts
    $scope.alerts = [];
    // function to handle closing alerts
    $scope.closeAlert = function(index) {
      $scope.alerts.splice(index, 1);
    };

  }).directive('capitalize', function() {
    return {
      require: 'ngModel',
      link: function(scope, element, attrs, modelCtrl) {
        var capitalize = function(inputValue) {
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
