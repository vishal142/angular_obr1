angular
	.module('mPokket')
	.controller('homeController', ["$scope", 'ajaxService', 'CONFIG', '$location', '$timeout', '$cookies', '$state', "$uibModal", "helper", "$rootScope", "blockUI", '$stateParams', '$window', function($scope, ajaxService, CONFIG, $location, $timeout, $cookies, $state, $uibModal, helper, $rootScope, blockUI, $stateParams, $window){

		$scope.successMessage 	= '';
		$scope.errorMessage 	= '';

		$scope.successEmailMessage = '';
        $scope.successMobileMessage = '';
		$scope.errorEmailMessage = '';
        $scope.errorMobileMessage = '';

		// Perform to doaddContactUs action
		$scope.doaddContactUs = function(contactData){ 
            blockUI.start();       
			ajaxService.ApiCall(contactData, CONFIG.ApiUrl+'lender/contact/contactUs', $scope.addContactUsSuccess, $scope.addContactUsError, 'post');
		}

		//addContactUs success function
		$scope.addContactUsSuccess = function(result,status) {
		    if(status == 200) {
		    	blockUI.stop();
	            $scope.successMessage = result.raws.success_message;
	            $scope.clearMessage(); 
		    }
		}

		//addContactUs error function
		$scope.addContactUsError = function(result) {
			if(status == 403){
                helper.unAuthenticate();
            } else {
	            $scope.errorMessage = result.raws.error_message;
	            $scope.clearMessage(); 
	        }
   			blockUI.stop();
	    }
	    /********************************************************************************************/

	    $scope.inputType 		= 'password';
	    $scope.passCSS 			= 'weak_bar status-p';
	    $scope.passStatus 		= 'weak';
	    $scope.pass_statusCSS 	= 'weak_txt';
	    $scope.bar_show 		= false;
	    var strongRegex 		= new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})");
	    var goodRegex 			= new RegExp("^(((?=.*[a-z])(?=.*[A-Z]))|((?=.*[a-z])(?=.*[0-9]))|((?=.*[A-Z])(?=.*[0-9])))(?=.{6,})");


	    $scope.verifyEmailData = {};
	    $scope.verifyMobileData = {};
        $scope.verifyEmailData.user_id 	= '';
        $scope.verifyEmailData.verify_type 	= 'E';
        $scope.verifyMobileData.user_id = '';
        $scope.verifyMobileData.verify_type = 'M';

	    $scope.viewpassword = function(){
	    	//alert();
	        $scope.inputType = ($scope.inputType == 'password') ? 'text' : 'password';
	    };

		$scope.analyze = function(value){
			//console.log(value);
			$scope.bar_show = true;
			if(value==undefined){
				$scope.bar_show = false;
			}else if(strongRegex.test(value)){
				$scope.passCSS 		= "strong_txt";
				$scope.passStatus 	= 'Strong';
			}else if(goodRegex.test(value)) {
				$scope.passCSS 		= "good_txt";
				$scope.passStatus 	= 'Good';
			}else{
				$scope.passCSS 		= "weak_txt";
				$scope.passStatus 	= 'Weak';
			}
		};


		// Perform to doRegitration action
		$scope.doRegitration = function(regData){ 
			//alert();
            blockUI.start();       
			ajaxService.ApiCall(regData, CONFIG.ApiUrl+'lender/registration/userRegistration', $scope.doRegitrationSuccess, $scope.doRegitrationError, 'post');
		}

		//doRegitration success function
		$scope.doRegitrationSuccess = function(result,status) {
		    if(status == 200) {
		    	blockUI.stop();
	            //$scope.successMessage = result.raws.success_message;
	            $scope.userDetails = result.raws.data;
		    	$cookies.put('user_id', result.raws.data.user_id, {path: '/'});
		    	$cookies.put('user_pass_key', result.raws.data.user_pass_key, {path: '/'});
	            $location.path('/home/sign-up-verification');
		    }
		}

		//doRegitration error function
		$scope.doRegitrationError = function(result) {
			if(status == 403){
                helper.unAuthenticate();
            } else {
	            $scope.errorMessage = result.raws.error_message;
	            $scope.clearMessage(); 
	        }
   			blockUI.stop();
	    }

	    /********************************************************************************************/

        // Retrieving a cookie
        $scope.doLogin = function(loginData){
            ajaxService.ApiCall(loginData, CONFIG.ApiUrl+'lender/lender/logIn', $scope.loginSuccess, $scope.loginError, 'post');                   
        }
        //login success function
        $scope.loginSuccess = function(result,status){
            if(status == 200){

		        $scope.successMessage = result.raws.success_message;
		    	$cookies.put('user_id', result.raws.data.user_id, {path: '/'});
		    	$cookies.put('user_pass_key', result.raws.data.user_pass_key, {path: '/'});
		    	$rootScope.userDetails = result.raws.data;

	        	$scope.clearMessage(); 

	        	if(result.raws.data.is_active == 0){
                	$location.path('/home/sign-up-verification');
	        	} else {
	        		$location.path('/dashboard/welcome');
                    /*if(result.raws.data.user_mode == null){
                        $location.path('/home/set-mode');
                    } else {
                        
                    }*/
	        	}
            }
        }                
        //login error function
        $scope.loginError = function(result){
            $scope.errorMessage = result.raws.error_message;
	        $scope.clearMessage(); 
        } 

	    /********************************************************************************************/

        // Retrieving a cookie
        var user_id     			= $cookies.get('user_id');
        var user_pass_key    		= $cookies.get('user_pass_key');
        var param 					= {};
        param.user_id 				= user_id;
        param.user_pass_key      	= user_pass_key;

        $scope.logout = function(){
            ajaxService.ApiCall(param, CONFIG.ApiUrl+'lender/lender/logOut', $scope.logoutSuccess, $scope.logoutError, 'post');                   
        }
        //login success function
        $scope.logoutSuccess = function(result,status){
            if(status == 200){

                // Removing a cookie
                $cookies.remove('user_id');
                $cookies.remove('user_pass_key');
                $scope.successMessage = result.raws.success_message;
	        	$scope.clearMessage(); 
                $location.path('/home/sign-in');
            }
        }                
        //login error function
        $scope.logoutError = function(result){
            $scope.errorMessage = result.raws.error_message;
	        $scope.clearMessage(); 
        } 

	    /********************************************************************************************/



	    $rootScope.resend_type = '';
		$scope.resendVerifyCode = function(resend_type, value){ 
            blockUI.start();    
            $rootScope.resend_type = resend_type;

            var resendVerificationParam = {
            	'type'			: resend_type,
            	'emailId'		: value,
            	'phone'			: value,

		        'user_pass_key'	: $cookies.get('user_pass_key'),
	        	'user_id' 		: $cookies.get('user_id'),
            }
			ajaxService.ApiCall(resendVerificationParam, CONFIG.ApiUrl+'lender/registration/resendVerificationCode', $scope.resendVerifyCodeSuccess, $scope.resendVerifyCodeError, 'post');
		}

		//resendVerifyCode success function
		$scope.resendVerifyCodeSuccess = function(result,status) {
		    if(status == 200) {
		    	blockUI.stop();
            	if($rootScope.resend_type == 'E'){
	            	$scope.successEmailMessage = result.raws.success_message;
		    	} else {
	            	$scope.successMobileMessage = result.raws.success_message;
	            }
	            //$scope.clearMessage();
				$timeout(function() {
	        		$scope.successEmailMessage = '';
	                $scope.successMobileMessage = '';
	            }, 3000); 
		    }
		}

		//resendVerifyCode error function
		$scope.resendVerifyCodeError = function(result) {
			if(status == 403){
                helper.unAuthenticate();
            } else {
            	if($rootScope.resend_type == 'E'){
	            	$scope.errorEmailMessage = result.raws.error_message;
		    	} else {
	            	$scope.errorMobileMessage = result.raws.error_message;
	            }
	            //$scope.clearMessage();
				$timeout(function() {
	        		$scope.errorEmailMessage = '';
	                $scope.errorMobileMessage = '';
	            }, 3000); 
			}
   			blockUI.stop();
	    }

	    /********************************************************************************************/

	    $rootScope.verify_type = '';
		$scope.doVerify = function(verifyData){ 
            blockUI.start();    
            $rootScope.verify_type 		= verifyData.verify_type;
	        verifyData.user_pass_key	= $cookies.get('user_pass_key');
        	verifyData.user_id 			= $cookies.get('user_id');

			ajaxService.ApiCall(verifyData, CONFIG.ApiUrl+'lender/registration/verifyOtp', $scope.doVerifySuccess, $scope.doVerifyError, 'post');
		}

		//doVerify success function
		$scope.doVerifySuccess = function(result,status) {
		    if(status == 200) {
		    	blockUI.stop();
		    	if($rootScope.verify_type == 'E'){
	            	$scope.userDetails.is_email_id_verified = 1;
		    	} else {
	            	$scope.userDetails.is_mobile_number_verified = 1;
		    	}
		    }
		}

		//doVerify error function
		$scope.doVerifyError = function(result) {
			if(status == 403){
                helper.unAuthenticate();
            } else {
            	if($rootScope.verify_type == 'E'){
	            	$scope.errorEmailMessage = result.raws.error_message;
		    	} else {
	            	$scope.errorMobileMessage = result.raws.error_message;
	            }
	            //$scope.clearMessage();
				$timeout(function() {
	        		$scope.errorEmailMessage = '';
	                $scope.errorMobileMessage = '';
	            }, 3000); 
			}
   			blockUI.stop();
	    }

	    /********************************************************************************************/

		$scope.go = function (path) {
			$location.path(path);
		};

	    /********************************************************************************************/

	    $scope.updateUserType = {};
	    $scope.updateUserType.user_mode = 'L';

		$scope.doUpdateUserType = function(updateUserType){ 
            blockUI.start();    
            //$rootScope.verify_type = updateUserType.verify_type;
			var updateUserTypeParam  = {};
            updateUserTypeParam.user_mode 		= updateUserType.user_mode;
            updateUserTypeParam.referal_code 	= updateUserType.referal_code;
	        updateUserTypeParam.user_pass_key	= $cookies.get('user_pass_key');
        	updateUserTypeParam.user_id 		= $cookies.get('user_id');

			ajaxService.ApiCall(updateUserTypeParam, CONFIG.ApiUrl+'lender/registration/addUserMode', $scope.updateUserTypeSuccess, $scope.updateUserTypeError, 'post');
		}

		//updateUserType success function
		$scope.updateUserTypeSuccess = function(result,status) {
		    if(status == 200) {
		    	blockUI.stop();
		    	$location.path('/dashboard/welcome');
		    }
		}

		//updateUserType error function
		$scope.updateUserTypeError = function(result) {
			if(status == 403){
                helper.unAuthenticate();
            } else {
	            $scope.errorMessage = result.raws.error_message;
	            $scope.clearMessage(); 
	        }
   			blockUI.stop();
	    }


		// Perform the clearMessage action
		$scope.clearMessage = function() {
			$timeout(function() {
        		$scope.successMessage = '';
                $scope.errorMessage = '';
            }, 3000);
		}
	}])


