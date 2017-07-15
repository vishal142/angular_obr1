angular
	.module('mPokket')
	.controller('inviteController', ["$scope", 'ajaxService', 'CONFIG', '$location', '$timeout', '$cookies', '$state', "$uibModal", "helper", "$rootScope", "blockUI", '$stateParams', '$window', function($scope, ajaxService, CONFIG, $location, $timeout, $cookies, $state, $uibModal, helper, $rootScope, blockUI, $stateParams, $window){
		
		$scope.successMessage 			= '';
		$scope.errorMessage 			= '';
		$scope.inviteData 				= {};
		$scope.inviteData.inviteeCode 	= $stateParams.userCode;
		$scope.redirect					= ''; 
		$scope.osDetails 				= '';

		// Perform to getInviteUserOSDetails action
		$scope.getInviteUserOSDetails = function(){ 
            blockUI.start();        
	        var inviteDataParam = {};
			ajaxService.ApiCall(inviteDataParam, CONFIG.ApiUrl+'user_referals/getInviteUserOSDetails', $scope.getInviteUserOSDetailsSuccess, $scope.getInviteUserOSDetailsError, 'post');
		}

		//getInviteUserOSDetails success function
		$scope.getInviteUserOSDetailsSuccess = function(result,status) {
		    if(status == 200) {
		    	blockUI.stop();
	            //$scope.successMessage 	= result.raws.success_message;
	            $scope.osDetails 		= result.raws.data.os_info;
	            //console.log($scope.osDetails);	            
		    }
		}

		//getInviteUserOSDetails error function
		$scope.getInviteUserOSDetailsError = function(result) {
			if(status == 403){
                helper.unAuthenticate();
            } else {
	            $scope.errorMessage = result.raws.error_message;
	            $scope.clearMessage(); 
	        }
   			blockUI.stop();
	    }

	    if($state.$current.name == 'invite.user'){
	    	$scope.getInviteUserOSDetails();
	    }
		/********************************* ********************************* *********************************/

	
		// Perform to addInviteUserReferals action
		$scope.doaddInviteUserReferals = function(inviteData){ 
            blockUI.start();	        

	        var inviteDataParam = {
	            'name'			: inviteData.inviteeName,
	            'email'			: inviteData.inviteeEmail,
	            'mobile_number'	: inviteData.inviteeMobile,
	            'user_code'		: inviteData.inviteeCode       
	        };

			ajaxService.ApiCall(inviteDataParam, CONFIG.ApiUrl+'user_referals/userReferals', $scope.addInviteUserReferalsSuccess, $scope.addInviteUserReferalsError, 'post');
		}

		//addInviteUserReferalss success function
		$scope.addInviteUserReferalsSuccess = function(result,status) {
		    if(status == 200) {
		    	blockUI.stop();
	            //$scope.successMessage = result.raws.success_message;

	            if($scope.redirect == 'google_play'){                
                	$window.location.href = 'https://play.google.com/store/apps?hl=en';
            	}
            	if($scope.redirect == 'app_store'){                
                	$window.location.href = ' https://itunes.apple.com/us/app/apple-store/id375380948?mt=8';           		
            	}
		    }
		}

		//addInviteUserReferalss error function
		$scope.addInviteUserReferalsError = function(result) {
			if(status == 403){
                helper.unAuthenticate();
            } else {
	            $scope.errorMessage = result.raws.error_message;
	            $scope.clearMessage(); 
	        }
   			blockUI.stop();
	    }

	    $scope.downloadApp = function(eval){
		    $scope.redirect = eval;
		}			

		/********************************* ********************************* *********************************/

		// Perform to addInviteUserSocial action
		$scope.doaddInviteUserSocial = function(inviteData){ 
            blockUI.start();	        

	        var inviteDataParam = {
	            'inviteeName'		: inviteData.inviteeName,
	            'inviteeEmail'		: inviteData.inviteeEmail,
	            'inviteeMobile'		: inviteData.inviteeMobile       
	        };

			ajaxService.ApiCall(inviteDataParam, CONFIG.ApiUrl+'user_referals/addUsers', $scope.addInviteUserSocialSuccess, $scope.addInviteUserSocialError, 'post');
		}

		//addInviteUserSocial success function
		$scope.addInviteUserSocialSuccess = function(result,status) {
		    if(status == 200) {
		    	blockUI.stop();
	            $scope.successMessage = result.raws.success_message;
                $scope.inviteData 	= {};       
	            $scope.clearMessage(); 
		    }
		}

		//addInviteUserSocial error function
		$scope.addInviteUserSocialError = function(result) {
			if(status == 403){
                helper.unAuthenticate();
            } else {
	            $scope.errorMessage = result.raws.error_message;
	            $scope.clearMessage(); 
	        }
   			blockUI.stop();
	    }
		/********************************* ********************************* *********************************/

		// Perform the clearMessage action
		$scope.clearMessage = function() {
			$timeout(function() {
        		$scope.successMessage = '';
                $scope.errorMessage = '';
            }, 3000);
		}
	}])
