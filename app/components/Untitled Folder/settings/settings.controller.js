angular
	.module('mPokket')
	.controller('settingsController', ["$scope", "$rootScope", '$stateParams', '$timeout', "$uibModal", 'helper', 'CONFIG', 'ajaxService', '$location', '$cookies', "blockUI",'$state', '$window', function($scope, $rootScope, $stateParams, $timeout, $uibModal, helper, CONFIG, ajaxService, $location, $cookies, blockUI, $state, $window){
		
		$scope.changePassword ={};
		$scope.changePass = function(changePassword) { 
            blockUI.start();
                   
            var param = {
		        'user_pass_key'		: $cookies.get('user_pass_key'),
	        	'user_id'			: $cookies.get('user_id'),
	        	'password'  		: changePassword.password,
	        	'confirm_password'  : changePassword.conPassword,    
            };            

        	ajaxService.ApiCall(param, CONFIG.ApiUrl+'lender/settings/changePassword', $scope.changePassSuccess, $scope.changePassError, 'post');
		}
		//changePass success function
		$scope.changePassSuccess = function(result,status) {
		    if(status == 200) {
		    	blockUI.stop();
            	$scope.successMessage = result.raws.success_message;
            	$scope.data = result.raws.data;
		    	$scope.clearMessage();
		    }		       
		}
		//changePass error function
		$scope.changePassError = function(result,status) {
			if(status == 403){
                helper.unAuthenticate();
            } else {
	            $scope.errorMessage = result.raws.error_message;
	            $scope.clearMessage(); 
	        }
   			blockUI.stop();
		}

	

		$scope.changeEmail = function(changePassword) { 
            blockUI.start();
                   
            var param = {
		        'user_pass_key'		: $cookies.get('user_pass_key'),
	        	'user_id'			: $cookies.get('user_id')
            };            

        	ajaxService.ApiCall(param, CONFIG.ApiUrl+'lender/settings/changePassword', $scope.changeEmailSuccess, $scope.changeEmailError, 'post');
		}
		//changeEmail success function
		$scope.changeEmailSuccess = function(result,status) {
		    if(status == 200) {
		    	blockUI.stop();
            	$scope.successMessage = result.raws.success_message;
            	$scope.data = result.raws.data;
		    	$scope.clearMessage();
		    }		       
		}
		//changeEmail error function
		$scope.changeEmailError = function(result,status) {
			if(status == 403){
                helper.unAuthenticate();
            } else {
	            $scope.errorMessage = result.raws.error_message;
	            $scope.clearMessage(); 
	        }
   			blockUI.stop();
		}


	    




		$scope.clearMessage = function(){
			$timeout(function() {
        		$scope.successMessage = '';
                $scope.errorMessage = '';
            }, CONFIG.TimeOut);
		}
	}])


	.controller('viewKYCInfoController', ["$scope", 'ajaxService', 'CONFIG', '$location', '$timeout', '$cookies', "$uibModalInstance", "$rootScope", function($scope, ajaxService, CONFIG, $location, $timeout, $cookies, $uibModalInstance, $rootScope){

		$scope.cancel = function(){
			$uibModalInstance.dismiss('cancel');
		}
	}]);


	