/**
 * Load states for application
 * more info on UI-Router states can be found at
 * https://github.com/angular-ui/ui-router/wiki
 */


angular
    .module('mPokket')
    .run(function($rootScope, CONFIG, $state, helper, $confirmModalDefaults, $location){
        $rootScope.CONFIG = CONFIG;
        $rootScope.bodyClass        = '';
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
                if(toState.parent == 'home' && toState.url == '/sign-in'){
                    $rootScope.bodyClass = 'invitation-fm-section';
                   helper.checkUserAuthentication('login');             
                }else{
                    $rootScope.bodyClass = '';  
                }              
            } else { 

                if(toState.parent == 'home' && toState.url == '/sign-in'){
                    $rootScope.bodyClass = 'invitation-fm-section';
                   helper.checkUserAuthentication('login');             
                }else{
                    $rootScope.bodyClass = '';  
                    if(toState.parent == 'home' && toState.url == '/main'){
                    }else{
                      helper.checkUserAuthentication();
                    }
                }
            }


            /*if(toState.parent == 'home' && toState.url == '/email-verified'){
                $rootScope.bodyClass = '';                
            } 
            if ((toState.name == 'invite.user' && toState.url == '/user/:userCode') || toState.url == '/coming-soon'){
                $rootScope.bodyClass = 'invitation-fm-section';                
            }
            if(toState.parent == 'home' && toState.url == '/main'){
                $rootScope.bodyClass = '';              
            }
            if(toState.parent == 'home' && toState.url == '/sign-in'){
                $rootScope.bodyClass = 'invitation-fm-section'; 
                helper.checkUserAuthentication('login');             
            }            
            else{
                $rootScope.bodyClass = '';
                helper.checkUserAuthentication();
            }*/


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
    $urlRouterProvider.when('', '/home/main');

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

        .state('coming-soon', {
            parent: 'home',
            //url: '/home/coming-soon',
            url: '/coming-soon',
            templateUrl: 'app/components/home/views/coming-soon.view.html'
        })

        .state('main', {
            parent: 'home',
            //url: '/home/main',
            url: '/main',
            templateUrl: 'app/components/home/views/main.view.html'
        })

 .state('how-to-get-involved',{
    parent: 'home',
    url:'/how-to-get-involved',
    templateUrl:'app/components/home/views/how-to-get-involved.html'

 })

        .state('about-us', {
            parent: 'home',
            //url: '/home/about-us',
            url: '/about-us',
            templateUrl: 'app/components/page/views/about-us.view.html',
            controller: 'pageController',
            onEnter: function($state,$rootScope){
            $rootScope.carousel         = '';
            $rootScope.parentBreadcrumb = '';
            $rootScope.breadcrumb       = 'About';
            //$rootScope.leftMenu         = '';
                        }
        })

        .state('contact-us', {
            parent: 'home',
            //url: '/home/contact-us',
            url: '/contact-us',
            templateUrl: 'app/components/home/views/contact-us.view.html'
        })

        .state('sign-in', {
            parent: 'home',
            //url: '/home/sign-in',
            url: '/sign-in',
            templateUrl: 'app/components/home/views/sign-in.view.html'
        })

        .state('sign-up', {
            parent: 'home',
            //url: '/home/sign-up',
            url: '/sign-up',
            templateUrl: 'app/components/home/views/sign-up.view.html'
        })
        
        .state('sign-up-verification', {
            parent: 'home',
            //url: '/home/sign-up-verification',
            url: '/sign-up-verification',
            templateUrl: 'app/components/home/views/sign-up-verification.view.html'
        })

        .state('set-mode', {
            parent: 'home',
            //url: '/home/set-mode',
            url: '/set-mode',
            templateUrl: 'app/components/home/views/set-mode.view.html'
        })


        // USER-INVITATION
        .state('email-verified', {
            parent: 'home',
            //url: '/home/email-verified',
            url: '/email-verified',
            templateUrl: 'app/components/home/views/email.verified.view.html'
        })

        //Invite module section
        .state("invite", {
            parent: 'home',
            //url: '/home/invite',
            url: '/invite',
            templateUrl: 'app/components/invite/views/invite.index.view.html'
        })        

        .state("invite.user", {
            //url: '/home/invite/user/:userCode',
            url: '/user/:userCode',
            templateUrl: 'app/components/invite/views/invite.user.view.html',
            controller: 'inviteController',
        })

        .state("invite.user-from-social-media", {
            //url: '/home/invite/user-from-social-media',
            url: '/user-from-social-media',
            templateUrl: 'app/components/invite/views/invite.user-from-social-media.view.html',
            controller: 'inviteController',
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
            controller: 'dashboardController',
            onEnter: function($state,$rootScope){
              $rootScope.carousel         = 'dashboard';
              $rootScope.parentBreadcrumb = '';
              $rootScope.breadcrumb       = 'dashboard';
              $rootScope.leftMenu         = '';
            }
        })

        //Profile module section
        .state("profile", {
            parent: 'dashboard',
            //url: '/dashboard/profile',
            url: '/profile',
            templateUrl: 'app/components/profile/views/profile.index.view.html',
            onEnter: function($state,$rootScope){
              $rootScope.carousel         = 'profile';
              $rootScope.parentBreadcrumb = '';
              $rootScope.breadcrumb       = 'profile';
              $rootScope.leftMenu         = '';
            } 
        })

        .state("profile.details", {            
            //url: '/dashboard/profile/details',
            url: '/details',
            templateUrl: 'app/components/profile/views/profile.details.view.html',
            controller: 'profileController',
            onEnter: function($state,$rootScope){
              $rootScope.carousel         = 'profile';
              $rootScope.parentBreadcrumb = 'profile';
              $rootScope.breadcrumb       = 'details';
              $rootScope.leftMenu         = '';
            } 
        })

        .state("profile.edit", {            
            //url: '/dashboard/profile/edit',
            url: '/edit',
            templateUrl: 'app/components/profile/views/profile.edit.view.html',
            controller: 'profileController',
            onEnter: function($state,$rootScope){
              $rootScope.carousel         = 'profile';
              $rootScope.parentBreadcrumb = 'profile';
              $rootScope.breadcrumb       = 'edit';
              $rootScope.leftMenu         = '';
            } 
        })



        .state("profile.kyc", {            
            //url: '/dashboard/profile/kyc',
            url: '/kyc',
            templateUrl: 'app/components/profile/views/profile.kyc.view.html',
            controller: 'profileController',
            onEnter: function($state,$rootScope){
              $rootScope.carousel         = 'profile';
              $rootScope.parentBreadcrumb = 'profile';
              $rootScope.breadcrumb       = 'kyc';
              $rootScope.leftMenu         = '';
            } 
        })


        .state("profile.kyc-add", {
            //url: '/dashboard/profile/kyc-add',
            url: '/kyc-add',
            templateUrl: 'app/components/profile/views/profile.kyc.edit.view.html',
            controller: 'profileController',
            onEnter: function($state,$rootScope){
              $rootScope.carousel         = 'profile';
              $rootScope.parentBreadcrumb = 'profile';
              $rootScope.breadcrumb       = 'add kyc';
              $rootScope.leftMenu         = '';
            } 
        })


        .state("profile.kyc-edit", {
            //url: '/dashboard/profile/kyc-edit/:kycId/:kycStatus',
            url: '/kyc-edit/:kycId/:kycStatus',
            templateUrl: 'app/components/profile/views/profile.kyc.edit.view.html',
            controller: 'profileController',
            onEnter: function($state,$rootScope){
              $rootScope.carousel         = 'profile';
              $rootScope.parentBreadcrumb = 'profile';
              $rootScope.breadcrumb       = 'edit kyc';
              $rootScope.leftMenu         = '';
            } 
        })


        .state("profile.bank", {            
            //url: '/dashboard/profile/bank',
            url: '/bank',
            templateUrl: 'app/components/profile/views/profile.bank.view.html',
            controller: 'profileController',
            onEnter: function($state,$rootScope){
              $rootScope.carousel         = 'profile';
              $rootScope.parentBreadcrumb = 'profile';
              $rootScope.breadcrumb       = 'bank';
              $rootScope.leftMenu         = '';
            } 
        })
        .state("profile.bank-edit", {
            //url: '/dashboard/profile/bank-edit/:bankId',
            url: '/bank-edit/:bankId',
            templateUrl: 'app/components/profile/views/profile.bank.edit.view.html',
            controller: 'profileController',
            onEnter: function($state,$rootScope){
              $rootScope.carousel         = 'profile';
              $rootScope.parentBreadcrumb = 'profile';
              $rootScope.breadcrumb       = 'edit bank';
              $rootScope.leftMenu         = '';
            } 
        })



        //Profile module section
        .state("settings", {
            parent: 'dashboard',
            //url: '/dashboard/settings',
            url: '/settings',
            templateUrl: 'app/components/settings/views/settings.index.view.html',
            onEnter: function($state,$rootScope){
              $rootScope.carousel         = 'settings';
              $rootScope.parentBreadcrumb = '';
              $rootScope.breadcrumb       = 'settings';
              $rootScope.leftMenu         = '';
            } 
        })

        .state("settings.change-password", {            
            //url: '/dashboard/settings/change-password',
            url: '/change-password',
            templateUrl: 'app/components/settings/views/settings.change-password.view.html',
            controller: 'settingsController',
            onEnter: function($state,$rootScope){
              $rootScope.carousel         = 'settings';
              $rootScope.parentBreadcrumb = 'settings';
              $rootScope.breadcrumb       = 'change-password';
              $rootScope.leftMenu         = '';
            } 
        })
        .state("settings.change-email", {            
            //url: '/dashboard/settings/change-email',
            url: '/change-email',
            templateUrl: 'app/components/settings/views/settings.change-email.view.html',
            controller: 'settingsController',
            onEnter: function($state,$rootScope){
              $rootScope.carousel         = 'settings';
              $rootScope.parentBreadcrumb = 'settings';
              $rootScope.breadcrumb       = 'change-email';
              $rootScope.leftMenu         = '';
            } 
        })


            .state("settings.email-varification", {            
            //url: '/dashboard/settings/email-varification',
            url: '/email-varification',
            templateUrl: 'app/components/settings/views/settings.email-varification.view.html',
            controller: 'settingsController',
            onEnter: function($state,$rootScope){
              $rootScope.carousel         = 'settings';
              $rootScope.parentBreadcrumb = 'settings';
              $rootScope.breadcrumb       = 'email-varification';
              $rootScope.leftMenu         = '';
            } 
        })

        .state("settings.change-mobile", {            
            //url: '/dashboard/settings/change-mobile',
            url: '/change-mobile',
            templateUrl: 'app/components/settings/views/settings.change-mobile.view.html',
            controller: 'settingsController',
            onEnter: function($state,$rootScope){
              $rootScope.carousel         = 'settings';
              $rootScope.parentBreadcrumb = 'settings';
              $rootScope.breadcrumb       = 'change-mobile';
              $rootScope.leftMenu         = '';
            } 
        })





//******** give-cash section***********//

        .state("give-cash", {
            parent: 'dashboard',
            //url: '/dashboard/give-cash',
            url: '/give-cash',
            templateUrl: 'app/components/give-cash/views/give-cash.index.view.html',
            onEnter: function($state,$rootScope){
              $rootScope.carousel         = 'give-cash';
              $rootScope.parentBreadcrumb = '';
              $rootScope.breadcrumb       = 'give cash';
              $rootScope.leftMenu         = '';
            } 
        })

        .state("give-cash.mpokket-wallet", {
            //url: '/dashboard/give-cash/mpokket-wallet',
            url: '/mpokket-wallet',
            templateUrl: 'app/components/give-cash/views/give-cash.mpokket-wallet.view.html',
            controller: 'give-cashController',
            onEnter: function($state,$rootScope){
              $rootScope.carousel         = 'give-cash';
              $rootScope.parentBreadcrumb = 'give cash';
              $rootScope.breadcrumb       = 'mpokket wallet';
              $rootScope.leftMenu         = '';
            } 
        })

        .state("give-cash.select-user", {
            //url: '/dashboard/give-cash/select-user',
            url: '/select-user',
            templateUrl: 'app/components/give-cash/views/give-cash.select-user.view.html',
            controller: 'give-cashController',
            onEnter: function($state,$rootScope){
              $rootScope.carousel         = 'give-cash';
              $rootScope.parentBreadcrumb = 'give cash';
              $rootScope.breadcrumb       = 'select user';
              $rootScope.leftMenu         = '';
            } 
        })

        .state("give-cash.auto-allocation", {

            //url: '/dashboard/give-cash/auto-allocation',
            url: '/auto-allocation',
            templateUrl: 'app/components/give-cash/views/give-cash.auto-allocation.view.html',
            controller: 'give-cashController',
            onEnter: function($state,$rootScope){
              $rootScope.carousel         = 'give-cash';
              $rootScope.parentBreadcrumb = 'give cash';
              $rootScope.breadcrumb       = 'auto allocation';
              $rootScope.leftMenu         = '';
            } 
        })


        .state("give-cash.withdrawal", {

            //url: '/dashboard/give-cash/withdrawal',
            url: '/withdrawal',
            templateUrl: 'app/components/give-cash/views/give-cash.withdrawal.view.html',
            controller: 'give-cashController',
            onEnter: function($state,$rootScope){
              $rootScope.carousel         = 'give-cash';
              $rootScope.parentBreadcrumb = 'give cash';
              $rootScope.breadcrumb       = 'withdrawal';
              $rootScope.leftMenu         = '';
            } 
        })
   

        .state("give-cash.success", {

            //url: '/dashboard/give-cash/success',
            url: '/success',
            templateUrl: 'app/components/give-cash/views/give-cash.success.view.html',
            controller: 'give-cashController',
            onEnter: function($state,$rootScope){
              $rootScope.carousel         = 'give-cash';
              $rootScope.parentBreadcrumb = 'give cash';
              $rootScope.breadcrumb       = 'success';
              $rootScope.leftMenu         = '';
            } 
        })     

//****** conversations module***///////

        .state("conversations", {
            parent: 'dashboard',
            //url: '/dashboard/conversations',
            url: '/conversations',
            templateUrl: 'app/components/conversations/views/conversations.index.view.html',
            onEnter: function($state,$rootScope){
              $rootScope.carousel         = 'conversations';
              $rootScope.parentBreadcrumb = '';
              $rootScope.breadcrumb       = 'conversations';
              $rootScope.leftMenu         = '';
            } 
        })

        .state("conversations.list", {
            //url: '/dashboard/conversations/list',
            url: '/list',
            templateUrl: 'app/components/conversations/views/conversations.list.view.html',
            controller: 'conversationsController',
            onEnter: function($state,$rootScope){
              $rootScope.carousel         = 'conversations';
              $rootScope.parentBreadcrumb = 'conversations';
              $rootScope.breadcrumb       = 'list';
              $rootScope.leftMenu         = '';
            } 
        })

        .state("conversations.add", {
            //url: '/dashboard/conversations/add',
            url: '/add',
            templateUrl: 'app/components/conversations/views/conversations.add.view.html',
            controller: 'conversationsController',
            onEnter: function($state,$rootScope){
              $rootScope.carousel         = 'conversations';
              $rootScope.parentBreadcrumb = 'conversations';
              $rootScope.breadcrumb       = 'add';
              $rootScope.leftMenu         = '';
            } 
        })

        .state("conversations.success", {
            //url: '/dashboard/conversations/success',
            url: '/success',
            templateUrl: 'app/components/conversations/views/conversations.success.view.html',
            controller: 'conversationsController',
            onEnter: function($state,$rootScope){
              $rootScope.carousel         = 'conversations';
              $rootScope.parentBreadcrumb = 'conversations';
              $rootScope.breadcrumb       = 'success';
              $rootScope.leftMenu         = '';
            } 
        }) 

        .state("conversations.details", {
            url: '/details/:conversationsId',
            //url: '/conversations/details/:conversationsId',
            templateUrl: 'app/components/conversations/views/conversations.details.view.html',
            controller: 'conversationsController',
            onEnter: function($state,$rootScope){
              $rootScope.carousel         = 'conversations';
              $rootScope.parentBreadcrumb = 'conversations';
              $rootScope.breadcrumb       = 'details';
              $rootScope.leftMenu         = '';
            } 
        })




        // transactions module
        .state("transactions", {
            parent: 'dashboard',
            //url: '/dashboard/transactions',
            url: '/transactions',
            templateUrl: 'app/components/transactions/views/transactions.index.view.html',
            onEnter: function($state,$rootScope){
                $rootScope.carousel         = 'transactions';
                $rootScope.parentBreadcrumb = '';
                $rootScope.breadcrumb       = 'transactions';
                $rootScope.leftMenu         = '';
            } 
        })
         
         .state("transactions.details", {
             //url: '/dashboard/transactions/details',
            url: '/details',
             templateUrl: 'app/components/transactions/views/transactions.details.view.html',
             controller: 'transactionsController',
             onEnter: function($state,$rootScope){
               $rootScope.carousel         = 'transactions';
               $rootScope.parentBreadcrumb = 'transactions';
               $rootScope.breadcrumb       = 'details';
               $rootScope.leftMenu         = '';
             } 
         })
 
         .state("transactions.payments", {
             //url: '/dashboard/transactions/payments',
             url: '/payments',
             templateUrl: 'app/components/transactions/views/transactions.payments.view.html',
             controller: 'transactionsController',
             onEnter: function($state,$rootScope){
               $rootScope.carousel         = 'transactions';
               $rootScope.parentBreadcrumb = 'transactions';
               $rootScope.breadcrumb       = 'payments';
               $rootScope.leftMenu         = '';
             } 
         })



        // notifications module
        .state("notifications", {
            parent: 'dashboard',
            //url: '/dashboard/notifications',
            url: '/notifications',
            templateUrl: 'app/components/notifications/views/notifications.index.view.html',
            onEnter: function($state,$rootScope){
                $rootScope.carousel         = 'notifications';
                $rootScope.parentBreadcrumb = '';
                $rootScope.breadcrumb       = 'notifications';
                $rootScope.leftMenu         = '';
            } 
        })
         
         .state("notifications.list", {
             //url: '/dashboard/notifications/list',
            url: '/list',
             templateUrl: 'app/components/notifications/views/notifications.list.view.html',
             controller: 'transactionsController',
             onEnter: function($state,$rootScope){
               $rootScope.carousel         = 'notifications';
               $rootScope.parentBreadcrumb = 'notifications';
               $rootScope.breadcrumb       = 'list';
               $rootScope.leftMenu         = '';
             } 
         })


        //$locationProvider.html5Mode(true);
}]);