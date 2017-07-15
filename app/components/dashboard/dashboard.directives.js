angular.module('mPokket')
    /*
     * --------------------------------------------------------------------------
     * @ Directive Name           : dashboardHeader()
     * @ Added Date               : 14-04-2016
     * @ Added By                 : Subhankar
     * -----------------------------------------------------------------
     * @ Description              : admin dashboard header is managed from here
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 14-04-2016
     * @ Modified By              : Subhankar
     * 
     */
	.directive('dashboardHeader', function() {
        return {
        	controllerAs : 'dh',
        	controller : function($scope, $timeout, CONFIG, ajaxService, $location, $cookies){
        		var dh = this;
                
                // Retrieving a cookie
                var user_id                 = $cookies.get('user_id');
                var user_pass_key           = $cookies.get('user_pass_key');
                var param                   = {};
                param.user_id               = user_id;
                param.user_pass_key         = user_pass_key;

                dh.logout = function(){
                    ajaxService.ApiCall(param, CONFIG.ApiUrl+'lender/lender/logOut', dh.logoutSuccess, dh.logoutError, 'post');                   
                }
                //login success function
                dh.logoutSuccess = function(result,status){
                    if(status == 200){
                        // Removing a cookie
                        $cookies.remove('user_id');
                        $cookies.remove('user_pass_key');
                        $scope.successMessage = result.raws.success_message;
                        $location.path('/home/sign-in');
                    }
                }                
                //login error function
                dh.logoutError = function(result){
                    $scope.errorMessage = result.raws.error_message;
                    $timeout(function() {
                        $scope.successEmailMessage = '';
                        $scope.successMobileMessage = '';
                    }, 3000); 
                }

				return dh;
        	},
            templateUrl: 'app/components/dashboard/views/dashboard.header.html'
        };
    })

    /*
     * --------------------------------------------------------------------------
     * @ Directive Name           : dashboardBreadcrumb()
     * @ Added Date               : 14-04-2016
     * @ Added By                 : Subhankar
     * -----------------------------------------------------------------
     * @ Description              : admin dashboard header is managed from here
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 14-04-2016
     * @ Modified By              : Subhankar
     * 
     */
    .directive('dashboardBreadcrumb', function() {
        return {
            controllerAs : 'dbc',
            controller : function($scope, $timeout, CONFIG, ajaxService, $location, $cookies){
                var dbc = this;
                return dbc;
            },
            templateUrl: 'app/components/dashboard/views/dashboard.breadcrumb.html'
        };
    })


    .directive('dashboardFooter', function() {
        return {
            controllerAs : 'dpf',
            controller : function($scope, $timeout, CONFIG, ajaxService, $location, $cookies){
                var dpf = this;
                return dpf;
            },
            templateUrl: 'app/components/dashboard/views/dashboard.footer.html'
        };
    });    