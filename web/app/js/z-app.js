// define the app
// =============================================================================
var app = angular.module('eversafe', ['ui.router', 'ui.bootstrap', 'ui.mask',
'ngSanitize', 'angular-toasty', 'chart.js', 'ui.knob', 'auth0', 'angular-storage',
 'angular-jwt',"frapontillo.bootstrap-switch"])

// routes
// =============================================================================
.config(function($stateProvider, $urlRouterProvider, authProvider, $httpProvider, jwtInterceptorProvider) {
    // top level states
    $stateProvider
      .state('home', {
        url: '/home',
        templateUrl: 'partials/home.html',
        data: {
          requiresLogin: true
        }
      })
      .state('energy', {
        url: '/energy',
        templateUrl: 'partials/energy.html',
        data: {
          requiresLogin: true
        }
      })
      .state('settings', {
        url: '/settings',
        templateUrl: 'partials/settings.html',
        data: {
          requiresLogin: true
        }
      })
      .state('login', {
        url: '/login',
        templateUrl: 'partials/login.html',

      })
      // catchall state
    $urlRouterProvider.otherwise('/home');

    authProvider.init({
      domain: 'eversafe.eu.auth0.com',
      clientID: 'IJCwctuR17Gj2sECKB6NXQsBZ3JiGleT',
      loginState: 'login' // matches login state
    });
    jwtInterceptorProvider.tokenGetter = ['store', function(store) {
      // Return the saved token
      return store.get('token');
    }];

    $httpProvider.interceptors.push('jwtInterceptor');
  })
  .config(['toastyConfigProvider', function(toastyConfigProvider) {
    toastyConfigProvider.setConfig({
      limit: 1
    });
  }]).run(function($rootScope, auth, store, jwtHelper, $location,$state,$interval) {
    // This events gets triggered on refresh or URL change
    $rootScope.$on('$locationChangeStart', function() {
        var token = store.get('token');
        if (token) {
          if (!jwtHelper.isTokenExpired(token)) {
            if (!auth.isAuthenticated) {
              auth.authenticate(store.get('profile'), token);
              $state.go('home');
            }
          } else {
            // Either show the login page or use the refresh token to get a new idToken
            $location.path('/');
          }
        }
      })
      // This hooks al auth events to check everything as soon as the app starts
    auth.hookEvents();
  })
  // Main controller
  // =============================================================================
  .controller('mainController', function($rootScope, $scope, $http, $q, $window, $location, $interval, toasty, auth, store, $state, jwtHelper) {
    $scope.updateInfo = function(){
    $scope.profile = auth.profile;
    $scope.loggedIn = auth.isAuthenticated;
}

    /////////////////// Buttons ///////////////////////
    //
    //
    $rootScope.$on('$stateChangeStart', $scope.updateInfo());
    $scope.energyNow = {
      value: 0,
      options: {
        fgColor: '#66CC66',
        angleOffset: -125,
        angleArc: 250,
        max: 500,
        min:0,
        readOnly: true,
        format: function(v) {
          return v + ' mW';
        },
        'draw': function() {
        $(this.i).css('font-size', '25px');}
      }
    };
    $scope.getEnergyNow = function(){
      $http.get("apiv1/TSDB/currentUser/1").then(function(response) {
        $scope.energyNow.value = (Number(response.data)/3);})
    }
    $scope.currentStatus={};
    $scope.updateStatus = function(){
    $http.get("apiv1/status/1").then(function(response) {
      $scope.currentStatus = angular.fromJson(response.data);
    }).then(function(){$http.get("apiv1/alerts/1").then(function(response) {
      $scope.alerts = angular.fromJson(response.data);
    })})
    }
    $scope.updateStatus();
    $interval($scope.updateStatus,5000);
    $scope.labels = ["January", "February", "March", "April", "May", "June", "July"];
    $scope.series = ['Energy Use'];
    $scope.data = [
      [65, 59, 80, 81, 56, 55, 40]
    ];
    $scope.isHome = function() {
      if ($scope.currentStatus.button != 'home') {
        $http.put("apiv1/status/1/home");
        $scope.currentStatusbutton = 'home';
        $scope.updateStatus();
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

    $scope.isAway = function() {
      if ($scope.currentStatus.button != 'away') {
        $http.put("apiv1/status/1/away");
        $scope.currentStatus.button = 'away';
        $scope.updateStatus();
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
    $scope.authIntervention=function(){
auth.authenticate(store.get('profile'), store.get('token'));

    // This hooks al auth events to check everything as soon as the app starts
  auth.hookEvents();
      $scope.updateInfo;
    }
    $scope.authFuckery=function(){
      $scope.profile = angular.fromJson(store.get('profile'));
      $scope.loggedIn = true;
      $scope.$apply();
    }
    /////////////////// Alerts ///////////////////////
    //
    // object to hold alerts
    $scope.alerts = [];
    // function to handle closing alerts
    $scope.closeAlert = function(index) {
      $http.delete("apiv1/alerts/"+$scope.alerts[index].AlertID).then(function(response) {
        $scope.alerts.splice(index, 1);
      })
    };
    //// settings
    $scope.energyInfo = {
      billingType: 'variable'
    };
    $scope.energyInfo.variableRateTimes = [];
    $scope.personalInfo = {};
    $scope.addToFieldset = function(name, scope) {
      var newItemNo = $scope.energyInfo.variableRateTimes.length + 1;
      $scope.energyInfo.variableRateTimes.push({
        'id': name + newItemNo
      });
    };
    //remove a fieldset
    $scope.removeFromFieldset = function(name, scope, index) {

      index = index || $scope[scope][name].length - 1;
      $scope[scope][name].splice(index);
    };
    $scope.login = function() {
      auth.signin({}, function(profile, token) {
        // Success callback
        store.set('profile', profile);
        store.set('token', token);
        $location.path('/');
        $scope.profile = auth.profile;
        $scope.loggedIn = auth.isAuthenticated;
      }, function() {
        // Error callback
      });


    }
    $scope.updateInfo = function(){
    $scope.profile = auth.profile;
    $scope.loggedIn = auth.isAuthenticated;
}
$scope.updateInfo();
    $scope.logout = function() {
      $state.go('login');
      auth.signout();
      store.remove('profile');
      store.remove('token');
      $scope.profile = null;
      $scope.loggedIn = false;

    }

    $scope.eversafePlugs = [{
      chipName:'Loading...',
      chipEnabled: false
    }];
    $scope.getPlugs = function(){
      $http.get("apiv1/TSDB/listPlugs/1").then(function(response) {
        $scope.eversafePlugs = response.data;
      });
    }
    $scope.eversafeSensors = [{name:'Hallway',enabled:true}];

  })
  .directive('capitalize', function() {
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
