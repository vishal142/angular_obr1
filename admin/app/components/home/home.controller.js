angular
	.module('mPokket')
	.controller('homeController', ["$scope", "$http", "$window", "$q", 'ajaxService', 'CONFIG', '$location', '$timeout', '$cookies', 'helper', function($scope, $http, $window, $q, ajaxService, CONFIG, $location, $timeout, $cookies, helper){
     //alert(CONFIG.ApiUrl);
    	//$scope.admin_user_id 	= 0;
    	//$scope.loginData 		= {};

		// Perform the login action when the user submits the login form
		$scope.doLogin = function(loginData) { 
			//console.log(loginData);  
			ajaxService.ApiCall(loginData, CONFIG.ApiUrl+'admin/logIn', $scope.loginUserSuccess, $scope.loginUserError, 'post');
		}


		//login success function
		$scope.loginUserSuccess = function(result,status) {
		    if(status == 200) {
		    	// Setting a cookie
		    	$cookies.put('admin_user_id', result.raws.data.admin_user_id,{'path': '/'});
		    	$cookies.put('pass_key', result.raws.data.pass_key,{'path': '/'});
		        $location.path('dashboard/welcome');
		    }		       
		}
		//login error function
		$scope.loginUserError = function(result) {
            $scope.errorMessage = result.raws.error_message;
            $timeout(function() {
        		$scope.successMessage = '';
                $scope.errorMessage = '';
            }, CONFIG.TimeOut);
		}

		
		// Send verification-code to the email id
		$scope.doforgetPassword = function(forgetPasswordData){ 
			//alert(forgetPasswordData.email);
			ajaxService.ApiCall(forgetPasswordData, CONFIG.ApiUrl+'admin/forgetPassword', $scope.forgetPasswordSuccess, $scope.forgetPasswordError, 'post');
		}
		//forgetPassword success function
		$scope.forgetPasswordSuccess = function(result,status) {
		    if(status == 200) {
            	$scope.admin_user_id = result.raws.data.admin_user_id;
            	$scope.successMessage = result.raws.success_message;
            	$scope.errorMessage = result.raws.error_message;
		        $location.path('/home/verifyPasscode');
		    }		       
		}
		//forgetPassword error function
		$scope.forgetPasswordError = function(result) {
            $scope.errorMessage = result.raws.error_message;
            $timeout(function() {
        		$scope.successMessage = '';
                $scope.errorMessage = '';
            }, CONFIG.TimeOut);
		}

		
		// Verify Passcode
		$scope.doverifyPasscode = function(verifyPasscodeData){ 
			//alert($scope.admin_user_id);
			var data = {};
			data.admin_user_id = $scope.admin_user_id;
			data.passcode = verifyPasscodeData.passcode;
			ajaxService.ApiCall(data, CONFIG.ApiUrl+'admin/verifyPasscode', $scope.verifyPasscodeSuccess, $scope.verifyPasscodeError, 'post');
		}
		//Verify Passcode success function
		$scope.verifyPasscodeSuccess = function(result,status) {
		    if(status == 200) {
		    	//alert(result.raws.data.admin_user_id,result.raws.data.passcode);
             	$scope.admin_user_id = result.raws.data.admin_user_id;
             	$scope.passcode 	= result.raws.data.passcode;
           		$scope.successMessage = result.raws.success_message;
            	$scope.errorMessage = result.raws.error_message;
		        $location.path('/home/resetPassword');
		    }		       
		}
		//Verify Passcode error function
		$scope.verifyPasscodeError = function(result) {
            $scope.errorMessage = result.raws.error_message;
        	$scope.successMessage = '';
            $timeout(function() {
        		//$scope.successMessage = '';
                $scope.errorMessage = '';
            }, CONFIG.TimeOut);
		}

		
		// Reset Password
		$scope.doresetPassword = function(resetPasswordData){
		//alert($scope.admin_user_id);
			var data = {};

			resetPasswordData.admin_user_id 	= $scope.admin_user_id;
			resetPasswordData.passcode 			= $scope.passcode;
			/*data.new_password 	= resetPasswordData.new_password;*/
			//console.log(data);			
			ajaxService.ApiCall(resetPasswordData, CONFIG.ApiUrl+'admin/resetPassword', $scope.resetPasswordSuccess, $scope.resetPasswordError, 'post');
		}
		
		//Reset Password success function
		$scope.resetPasswordSuccess = function(result,status) {
		    if(status == 200) {
           		$scope.successMessage = result.raws.success_message;
		        $location.path('/home/login');
		    }		       
		}
		//Reset Password error function
		$scope.resetPasswordError = function(result) {
            $scope.errorMessage = result.raws.error_message;
            $timeout(function() {
        		$scope.successMessage = '';
                $scope.errorMessage = '';
            }, CONFIG.TimeOut);
		}






	}])


