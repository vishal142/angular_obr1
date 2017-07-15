/**
 * Load states for application
 * more info on UI-Router states can be found at
 * https://github.com/angular-ui/ui-router/wiki
 */

angular
    .module('mPokket')
    .run(function($rootScope, CONFIG, $state, helper, $confirmModalDefaults, $location){
      
        $rootScope.CONFIG = CONFIG;
        $rootScope.bodyClass = 'admin-body';
        //$rootScope.bodyClass      = ''; 
        $rootScope.carousel         = '';
        $rootScope.parentBreadcrumb = '';
        $rootScope.breadcrumb       = '';
        $rootScope.leftMenu         = '';

        
        //state change event called here           
        $rootScope.$on('$stateChangeStart', 
        function(event, toState, toParams, fromState, fromParams){ 

            //console.log(fromState);                           
            //console.log(toState);    

            if(fromState.parent == 'home' && toState.parent == 'home'){
                //$rootScope.bodyClass = 'admin-body';
            } else {
                if(toState.parent == 'home' && toState.url == '/login'){
                    $rootScope.bodyClass = 'admin-body';
                    helper.checkUserAuthentication('home');
                }else{
                    $rootScope.bodyClass = 'agent-body';
                    helper.checkUserAuthentication();
                }
            }
        })

        //get current timestamp
        function getTimeStamp(){
            var d = new Date();
            var currentTime = d.getTime();
            $rootScope.currentTime = currentTime;
        }
        getTimeStamp();

        $confirmModalDefaults.defaultLabels.title = 'angular obr';
    })
    .config(['$stateProvider', '$urlRouterProvider', '$locationProvider', function($stateProvider, $urlRouterProvider, $locationProvider){
    // any unknown URLS go to 404
    $urlRouterProvider.otherwise('/404');
    // no route goes to index
    $urlRouterProvider.when('', '/home/login');
    //$urlRouterProvider.when('#', '/home/login');
    //$urlRouterProvider.when('#/home', '/home/login');
    // use a state provider for routing
    //console.log($stateParams);
    $stateProvider
        
        //404 page not found section
        .state('404', {
            url: '/404',
            views: {
                "main": {
                  templateUrl: 'app/shared/404.html'
                },
            }
        })


        //Home module section
        .state('home', {
            url: '/home',
            views: {
                "main": {
                  controller: 'homeController as ctrl',
                  templateUrl: 'app/components/home/views/home.view.html'
                },
            }
        })

        .state('login', {
            parent: 'home',
            //url: '/home/login',
            url: '/login',
            templateUrl: 'app/components/home/views/login.view.html'
        })

        .state('forgetpassword', {
            parent: 'home',
            //url: '/home/forgetpassword',
            url: '/forgetpassword',
            templateUrl: 'app/components/home/views/forgetPassword.view.html'
        })

        .state('verifyPasscode', {
            parent: 'home',
            //url: '/home/verifyPasscode',
            url: '/verifyPasscode',
            templateUrl: 'app/components/home/views/verifyPasscode.view.html'
        })

        .state('resetPassword', {
            parent: 'home',
            //url: '/home/resetPassword',
            url: '/resetPassword',
            templateUrl: 'app/components/home/views/resetPassword.view.html'
        })        



        //Dashboard module section
        .state('dashboard', {
            // we'll add another state soon
            url: '/dashboard',
            views: {
                "main": {
                  controller: 'dashboardController as ctrl',
                  templateUrl: 'app/components/dashboard/views/dashboard.index.view.html'
                },
            }



        })

        .state('welcome', {
            parent: 'dashboard',
            //url: '/dashboard/welcome',
            url: '/welcome',
            templateUrl: 'app/components/dashboard/views/dashboard.welcome.view.html',
            onEnter: function($state,$rootScope){ // define value and load the default variable in our page
              $rootScope.parentBreadcrumb = 'Dashboard';
              $rootScope.breadcrumb       = 'Welcome';
              $rootScope.carousel         = '';
              $rootScope.leftMenu         = '';
            }
        })



  .state("employee",{
  parent:'dashboard',
  url:'/employee',
  templateUrl:'app/components/employee/views/employee.index.view.html'

 })


 .state("employee.list",{
  url: '/list',
  templateUrl:'app/components/employee/views/employee.list.html',
  controller:'employeeController',
  onEnter: function($state,$rootScope){ // define value and load the default variable in our page
              $rootScope.parentBreadcrumb = 'Dashboard';
              $rootScope.breadcrumb       = 'Employee list';
              $rootScope.carousel         = '';
              //$rootScope.leftMenu         = 'list'; 
            }
  

          })




 .state("employee.add-employee",{
  url:'/add-employee',
  templateUrl:'app/components/employee/views/employee.add.html',
  controller: 'employeeController',
  onEnter: function($state,$rootScope){ // define value and load the default variable in our page
    $rootScope.parentBreadcrumb = 'Dashboard';
    $rootScope.breadcrumb       = 'Employee';
    $rootScope.carousel         = 'Add employee';
    //$rootScope.leftMenu         = 'add'; 
  }
 })

.state("employee.update-employee",{
  url:'/update-employee/:employeeID',
  templateUrl:'app/components/employee/views/update.employee.html',
  controller: 'editEmployeeController',
  onEnter: function($state,$rootScope){ // define value and load the default variable in our page
    $rootScope.parentBreadcrumb = 'Dashboard';
    $rootScope.breadcrumb       = 'Employee';
    $rootScope.carousel         = 'Update Employee';
    
    //$rootScope.leftMenu         = 'edit'; 
            }
  
})


.state("profile",{
  parent:'dashboard',
  url:'/profile',
  templateUrl: 'app/components/profile/views/profile.index.html',
  

})

.state("profile.edit",{
//parent:'profile',
url:'/edit',
templateUrl: 'app/components/profile/views/edit.profile.html',
controller: 'profileController',
onEnter: function($state,$rootScope){
$rootScope.carousel         = 'profile';
$rootScope.parentBreadcrumb = 'profile';
$rootScope.breadcrumb       = 'edit';
//$rootScope.leftMenu         = '';
            }

})


.state("user",{
  parent:'dashboard',
  url:'/user',
  templateUrl: 'app/components/user/views/user.index.html',
  

})

.state("user.list",{
  url: '/list',
  templateUrl:'app/components/user/views/user.list.html',
  controller:'userController',
  onEnter: function($state,$rootScope){ // define value and load the default variable in our page
              $rootScope.parentBreadcrumb = 'Dashboard';
              $rootScope.breadcrumb       = 'User list';
              $rootScope.carousel         = '';
              //$rootScope.leftMenu         = 'list'; 
            }
  

          })


.state("manage_page",{
  parent:'dashboard',
  url:'/manage_page',
  templateUrl: 'app/components/page/views/page.index.html',


})

.state("manage_page.about-us",{
  url: '/about-us',
  templateUrl:'app/components/page/views/about-us.page.html',
  controller:'pageController',
  onEnter: function($state,$rootScope){ // define value and load the default variable in our page
              $rootScope.parentBreadcrumb = 'Dashboard';
              $rootScope.breadcrumb       = 'Manage Page';
              $rootScope.carousel         = 'about-us';
              //$rootScope.leftMenu         = 'list'; 
            }


})

        
}])

