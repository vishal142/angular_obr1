angular.module('helperServices', [])
    .service('helper', ['$http', '$rootScope', 'CONFIG', 'oauth', 'ajaxService', '$location', '$timeout', '$cookies', function ($http, $rootScope, CONFIG, oauth, ajaxService, $location, $timeout, $cookies) {

	    /** --------------------------------------------------------------------------
	     * @ Service Name             : checkUserAuthentication()
	     * @ Added Date               : 14-04-2016
	     * @ Added By                 : Subhankar
	     * -----------------------------------------------------------------
	     * @ Description              : user authentication is checked from here
	     * -----------------------------------------------------------------
	     * @ return                   : array
	     * -----------------------------------------------------------------
	     * @ Modified Date            : 14-04-2016
	     * @ Modified By              : Subhankar
	     * 
	     */
		this.checkUserAuthentication = function(type){
            // Retrieving a cookie
            var user_id     = $cookies.get('user_id');
            var user_pass_key    = $cookies.get('user_pass_key');
            
            var param = {};
            param.user_id   = user_id;
            param.user_pass_key  = user_pass_key;


            //Call API check_user_authentication
            ajaxService.ApiCall(param, CONFIG.ApiUrl+'lender/lender/checkUserAuthentication', userAuthenticationSuccess, userAuthenticationError, 'post'); 
            
            //User Authentication success function
    		function userAuthenticationSuccess(result){
                $rootScope.userDetails = result.raws.data;
                if(type == 'login'){
                    if(result.raws.data.is_active == 0){
                        $location.path('/home/sign-up-verification');
                    } else {                        
                        if(result.raws.data.user_mode == null){
                            $location.path('/home/set-mode');
                        } else {
                            $location.path('/dashboard/welcome');
                        }

                    }
                }
    		}
            
            //User Authentication error function
            function userAuthenticationError(result){
                $rootScope.errorMessage = 'You are not logged in. Please log in and try again.';
                $cookies.remove('user_id');
                $cookies.remove('user_pass_key');
                $timeout(function() {
                    $rootScope.errorMessage = '';
                }, CONFIG.TimeOut);
                $location.path('/home/sign-in');
            }

    	}

        /** --------------------------------------------------------------------------
         * @ Service Name             : unAuthenticate()
         * @ Added Date               : 16-09-2016
         * @ Added By                 : Piyalee
         * -----------------------------------------------------------------
         * @ Description              : 
         * -----------------------------------------------------------------
         * @ return                   : array
         * -----------------------------------------------------------------
         * @ Modified Date            : 16-09-2016
         * @ Modified By              : Piyalee
         * 
        */
        this.unAuthenticate = function()
        {
            // Removing a cookie
            $cookies.remove('user_id');
            $cookies.remove('user_pass_key');
            $rootScope.errorMessage = 'You are not logged in. Please log in and try again.';
            $location.path('/home/sign-in');
        }
    }])

    /*
    * --------------------------------------------------------------------------
    * @ Directive Name           : compareTo()
    * @ Added Date               : 14-04-2016
    * @ Added By                 : Subhankar
    * -----------------------------------------------------------------
    * @ Description              : Custom directive for adding custom validation for matching password and confirm paasword
    * -----------------------------------------------------------------
    * @ return                   : array
    * -----------------------------------------------------------------
    * @ Modified Date            : 14-04-2016
    * @ Modified By              : Subhankar
    * 
    */  
    .directive('compareTo', function(){
        return {
            restrict: 'A',
            require: "ngModel",
            scope: {
                otherModelValue: "=compareTo"
            },
            link: function (scope, element, attributes, ngModel) {
                ngModel.$validators.compare = function (modelValue) {
                    //alert(modelValue);
                    return modelValue == scope.otherModelValue;
                };
                scope.$watch("otherModelValue", function () {
                    ngModel.$validate();
                });
            }
        };
    })

    /*
    * --------------------------------------------------------------------------
    * @ Directive Name           : numberMask()
    * @ Added Date               : 14-04-2016
    * @ Added By                 : Subhankar
    * -----------------------------------------------------------------
    * @ Description              : admin left side menu is managed from here
    * -----------------------------------------------------------------
    * @ return                   : array
    * -----------------------------------------------------------------
    * @ Modified Date            : 14-04-2016
    * @ Modified By              : Subhankar
    * 
    */
    .directive('positiveDecimalNumberMask', function() {
        return {
            restrict: 'A',
            link: function(scope, element, attrs) {
                var config = {
                    'negative'  : false,
                };                
                $(element).numeric(config);            
            }
        }
    })

    .directive('positiveNumberMask', function() {
        return {
            restrict: 'A',
            link: function(scope, element, attrs) {
                var config = {
                    'negative'      : false,
                    'decimal'       : false,
                    'decimalPlaces' : 0,
                };                
                $(element).numeric(config);
            }
        }
    })

    .directive('negetiveNumberMask', function() {
        return {
            restrict: 'A',
            link: function(scope, element, attrs) {
                var config = {
                    'negative'      : true,
                    'decimal'       : false,
                    'decimalPlaces' : 0,
                };                
                $(element).numeric(config);
            }
        }
    })











    /*
     * --------------------------------------------------------------------------
     * @ Directive Name           : dynamicUrl
     * @ Added Date               : 15-12-2015
     * @ Added By                 : Subhankar
     * -----------------------------------------------------------------
     * @ Description              : dynamic url for html5 video src
     * -----------------------------------------------------------------
     * @ Modified Date            : 15-12-2015
     * @ Modified By              : Subhankar
     * 
     */
    /*.directive('dynamicUrl', function () {
        return {
            restrict: 'A',
            link: function postLink(scope, element, attr) {
                //console.log(attr.dynamicUrlSrc);
                element.attr('src', attr.dynamicUrlSrc);
            }
        };
    })*/

    /*
     * --------------------------------------------------------------------------
     * @ Directive Name           : scrollOnClick
     * @ Added Date               : 17-05-2016
     * @ Added By                 : Subhankar
     * -----------------------------------------------------------------
     * @ Description              : scroll On Click
     * -----------------------------------------------------------------
     * @ Modified Date            : 17-05-2015
     * @ Modified By              : Subhankar
     * 
     */
    /*.directive('scrollOnClick', function() {
      return {
        restrict: 'A',
        link: function(scope, $elm) {
          $elm.on('click', function() {
            $("body").animate({scrollTop: $elm.offset().top}, "slow");
          });
        }
      }
    })*/

    /*
     * --------------------------------------------------------------------------
     * @ Filter Name              : customDateFormat
     * @ Added Date               : 15-12-2015
     * @ Added By                 : Subhankar
     * -----------------------------------------------------------------
     * @ Description              : custom Date Format filter
     * -----------------------------------------------------------------
     * @ Modified Date            : 15-12-2015
     * @ Modified By              : Subhankar
     * 
     */
    /*.filter('customDateFormat', function ($filter) {
         return function (dateString, dateFormat) {
             var dateString = dateString.replace(/-/g, "/");
             var dateObject = new Date(dateString);
             var convertedDate = $filter('date')(new Date(dateObject), dateFormat);
             return convertedDate;
         };
    })*/
