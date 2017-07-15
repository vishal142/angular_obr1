angular
	.module('mPokket')
	.controller('profileController', ["$scope", "$rootScope", '$stateParams', '$timeout', "$uibModal", 'helper', 'CONFIG', 'ajaxService', '$location', '$cookies', "blockUI",'$state', '$window', function($scope, $rootScope, $stateParams, $timeout, $uibModal, helper, CONFIG, ajaxService, $location, $cookies, blockUI, $state, $window){
	
		$scope.type = '';
		$scope.userProfile = {};
		$rootScope.userBank = {};

		$scope.getUserProfile = function(type) { 
            blockUI.start();
 			$scope.type = type || 'details';
                   
            var param = {
		        'user_pass_key'	: $cookies.get('user_pass_key'),
	        	'user_id'		: $cookies.get('user_id')         
            };            

        	ajaxService.ApiCall(param, CONFIG.ApiUrl+'lender/profile/getProfileDetails', $scope.getUserProfileSuccess, $scope.getUserProfileError, 'post');
		}
		//getUserProfile success function
		$scope.getUserProfileSuccess = function(result,status) {
		    if(status == 200) {
		    	blockUI.stop();

			    $scope.userProfile = result.raws.data.main_data;		    	
		    	if($scope.type == 'details'){
			    	$location.path('/dashboard/profile/details');
		    	} else {
			    	$scope.editFormDetails = result.raws.data.extraDetails;		    		
		    	}
		    }		       
		}
		//getUserProfile error function
		$scope.getUserProfileError = function(result,status) {
			if(status == 403){
                helper.unAuthenticate();
            } else {
	            $scope.errorMessage = result.raws.error_message;
	            $scope.clearMessage(); 
	        }
   			blockUI.stop();
		}

		if($state.$current.name == 'profile.details'){
			$scope.getUserProfile();
		}
		if($state.$current.name == 'profile.edit'){
			$scope.getUserProfile('type');
		}


	    $scope.pageno 				= 1; // initialize page no to 1
	    $scope.itemsPerPage 		= CONFIG.itemsPerPage; 
	    $scope.order_by 			= 'id';
	    $scope.order 				= 'desc';

		$scope.getUserKYC = function(pageno, order_by, order) { 
            blockUI.start(); 

	        $scope.pageno 	= pageno ? pageno : $scope.pageno;
	       	$scope.order_by = order_by ? order_by : $scope.order_by;
	        $scope.order 	= order ? order : $scope.order;

            var param = {
            	'order_by'			: $scope.order_by,
	            'order'				: $scope.order,
	            'page'				: $scope.pageno,
	            'page_size'			: $scope.itemsPerPage,

		        'user_pass_key'	: $cookies.get('user_pass_key'),
	        	'user_id'		: $cookies.get('user_id')         
            };            

        	ajaxService.ApiCall(param, CONFIG.ApiUrl+'lender/profile/getAllKYC', $scope.getUserKYCSuccess, $scope.getUserKYCError, 'post');
		}
		//getUserKYC success function
		$scope.getUserKYCSuccess = function(result,status) {
		    if(status == 200) {
		    	$location.path('/dashboard/profile/kyc');
		    	blockUI.stop();
		    	$scope.userKYC = result.raws.data.dataset;
		    	$scope.total_count = result.raws.data.count;
		    }		       
		}
		//getUserKYC error function
		$scope.getUserKYCError = function(result,status) {
			if(status == 403){
                helper.unAuthenticate();
            } else {
	            $scope.errorMessage = result.raws.error_message;
	            $scope.clearMessage(); 
	        }
   			blockUI.stop();
		}
		if($state.$current.name == 'profile.kyc'){
			$scope.getUserKYC();
		}


		$scope.getUserBank = function(pageno, order_by, order) { 
            blockUI.start();        
	        $scope.pageno 	= pageno ? pageno : $scope.pageno;
	       	$scope.order_by = order_by ? order_by : $scope.order_by;
	        $scope.order 	= order ? order : $scope.order;

            var param = {
            	'order_by'			: $scope.order_by,
	            'order'				: $scope.order,
	            'page'				: $scope.pageno,
	            'page_size'			: $scope.itemsPerPage,

		        'user_pass_key'	: $cookies.get('user_pass_key'),
	        	'user_id'		: $cookies.get('user_id')         
            };            

        	ajaxService.ApiCall(param, CONFIG.ApiUrl+'lender/profile/getAllBank', $scope.getUserBankSuccess, $scope.getUserBankError, 'post');
		}
		//getUserBank success function
		$scope.getUserBankSuccess = function(result,status) {
		    if(status == 200) {
		    	blockUI.stop();
		    	$rootScope.userBank = result.raws.data.dataset.bank_details;
		    	$scope.total_count = result.raws.data.dataset.bank_count;
		    	$location.path('/dashboard/profile/bank');
		    }		       
		}
		//getUserBank error function
		$scope.getUserBankError = function(result,status) {
			if(status == 403){
                helper.unAuthenticate();
            } else {
	            $scope.errorMessage = result.raws.error_message;
	            $scope.clearMessage(); 
	        }
   			blockUI.stop();
		}

		if($state.$current.name == 'profile.bank'){
			$scope.getUserBank();
		}

	
		$scope.uploadImg = function (element) {
			$scope.profile_img = element.files[0];
		}
		$scope.uploadFrontImg = function (element) {
			$scope.front_img = element.files[0];
		}
		$scope.uploadBankImg = function (element) {
			$scope.back_img = element.files[0];
		}

		$scope.doProfileEdit = function(userProfile){
			var formdata = new FormData();
			formdata.append('file', $scope.profile_img);
			formdata.append('user_pass_key', $cookies.get('user_pass_key'));
			formdata.append('user_id', $cookies.get('user_id'));

			angular.forEach(userProfile, function(value, key) {
				formdata.append(key, value);
			});

        	ajaxService.ApiCallImagePost(formdata, CONFIG.ApiUrl+'lender/profile/editProfileDetails', $scope.profileEditSuccess, $scope.profileEditError, 'post');
		}
		//profileEdit success function
		$scope.profileEditSuccess = function(result,status) {
		    if(status == 200) {
		    	$scope.file_img = '';
		    	blockUI.stop();
            	$scope.successMessage = result.raws.success_message;
		    	$location.path('/dashboard/profile/details');
		    }		       
		}
		//profileEdit error function
		$scope.profileEditError = function(result,status) {
		   	$scope.file_img = '';
			if(status == 403){
                helper.unAuthenticate();
            } else {
		    	$window.scrollTo(0, 100);
	            $scope.errorMessage = result.raws.error_message;
	            $scope.clearMessage(); 
	        }
   			blockUI.stop();
		}

		$rootScope.type = '';
		$scope.doSearchPincode = function(type){
			$rootScope.type = type;
			var pin_code = (type == 'residence') ? $scope.userProfile.residence_zipcode : $scope.userProfile.permanent_zipcode;

	        var pincodeDataParam = {			
		        'user_pass_key'	: $cookies.get('user_pass_key'),
	        	'user_id'		: $cookies.get('user_id'),  
	        	'pin_code'      : pin_code,
	        }

        	ajaxService.ApiCall(pincodeDataParam, CONFIG.ApiUrl+'lender/profile/getPincodeData', $scope.searchPincodeSuccess, $scope.searchPincodeError, 'post');
		}
		//searchPincode success function
		$scope.searchPincodeSuccess = function(result,status) {
		    if(status == 200) {
		    	blockUI.stop();
		    	if($rootScope.type == 'residence'){
		    		$scope.userProfile.residence_city = result.raws.data.city_name;
		    		$scope.userProfile.residence_state = result.raws.data.state_name;
		    	} else {
		    		$scope.userProfile.permanent_city = result.raws.data.city_name;
		    		$scope.userProfile.permanent_state = result.raws.data.state_name;
		    	}

		    }		       
		}
		//searchPincode error function
		$scope.searchPincodeError = function(result,status) {
			if(status == 403){
                helper.unAuthenticate();
            } else {
	            alert(result.raws.error_message);
		    	if($rootScope.type == 'residence'){
		    		$scope.userProfile.residence_city = '';
		    		$scope.userProfile.residence_state = '';
		    	} else {
		    		$scope.userProfile.permanent_city = '';
		    		$scope.userProfile.permanent_state = '';
		    	}
	        }
   			blockUI.stop();
		}


		$scope.docopyResidenceAddress = function(){
			if($scope.copy_residence){ 
				$scope.userProfile.permanent_street1 	= $scope.userProfile.residence_street1;
				$scope.userProfile.permanent_street2 	= $scope.userProfile.residence_street2;
				$scope.userProfile.permanent_street3 	= $scope.userProfile.residence_street3;
				$scope.userProfile.permanent_zipcode 	= $scope.userProfile.residence_zipcode;
				$scope.userProfile.permanent_city 		= $scope.userProfile.residence_city;
				$scope.userProfile.permanent_state 		= $scope.userProfile.residence_state;

			} else {
				$scope.userProfile.permanent_street1 	= '';
				$scope.userProfile.permanent_street2 	= '';
				$scope.userProfile.permanent_street3 	= '';
				$scope.userProfile.permanent_zipcode 	= '';
				$scope.userProfile.permanent_city 		= '';
				$scope.userProfile.permanent_state 		= '';
			}
		}


		$scope.userKYCDetails = {};
		$scope.getKYCDetails = function() { 
            blockUI.start(); 
            var param = {
	            'kyc_id'		: $stateParams.kycId,
	            'kyc_status'	: $stateParams.kycStatus,
		        'user_pass_key'	: $cookies.get('user_pass_key'),
	        	'user_id'		: $cookies.get('user_id')         
            };            

        	ajaxService.ApiCall(param, CONFIG.ApiUrl+'lender/profile/getKYCDetails', $scope.getKYCDetailsSuccess, $scope.getKYCDetailsError, 'post');
		}
		//getKYCDetails success function
		$scope.getKYCDetailsSuccess = function(result,status) {
		    if(status == 200) {
		    	blockUI.stop();
		    	if($stateParams.kycId == ''){
		    		$scope.userKYCDetails.document_type = 'I';
		    	} else {
		    		$scope.userKYCDetails = result.raws.data.kyc_details;
		    	}
		    	$scope.userKYCDocName = result.raws.data.kyc_doc_name;
		    	$scope.userKYCDocType = result.raws.data.kyc_doc_type;
		    	//$scope.total_count = result.raws.data.dataset.count;
		    }		       
		}
		//getKYCDetails error function
		$scope.getKYCDetailsError = function(result,status) {
			if(status == 403){
                helper.unAuthenticate();
            } else {
	            $scope.errorMessage = result.raws.error_message;

				$timeout(function() {
	            	$scope.clearMessage(); 
		    		$location.path('/dashboard/profile/kyc');
	            }, CONFIG.TimeOut);

	        }
   			blockUI.stop();
		}
		
		if($state.$current.name == 'profile.kyc-edit'){
			$scope.getKYCDetails();
			//console.log($stateParams.kycId);
		}

		$scope.doKYCEdit = function(userKYCDetails){
	       /* var userKYCDetailsParam = {			
		        'user_pass_key'	: $cookies.get('user_pass_key'),
	        	'user_id'		: $cookies.get('user_id')         
	        }
	        angular.extend(userKYCDetailsParam, userKYCDetails);

        	ajaxService.ApiCall(userKYCDetailsParam, CONFIG.ApiUrl+'lender/profile/addEditKYC', $scope.kycEditSuccess, $scope.kycEditError, 'post');*/

			var formdata = new FormData();
			formdata.append('front_file', $scope.front_img);
			formdata.append('back_file', $scope.back_img);
			formdata.append('user_pass_key', $cookies.get('user_pass_key'));
			formdata.append('user_id', $cookies.get('user_id'));

			angular.forEach(userKYCDetails, function(value, key) {
				formdata.append(key, value);
			});

        	ajaxService.ApiCallImagePost(formdata, CONFIG.ApiUrl+'lender/profile/addEditKYC', $scope.kycEditSuccess, $scope.kycEditError, 'post');
		}
		//kycEdit success function
		$scope.kycEditSuccess = function(result,status) {
		    if(status == 200) {
		    	$scope.front_img = '';
		    	$scope.back_img = '';
		    	blockUI.stop();
            	$scope.successMessage = result.raws.success_message;
		    	$location.path('/dashboard/profile/kyc');
		    }		       
		}
		//kycEdit error function
		$scope.kycEditError = function(result,status) {
	    	$scope.front_img = '';
	    	$scope.back_img = '';
			if(status == 403){
                helper.unAuthenticate();
            } else {
		    	//$window.scrollTo(0, 100);
	            $scope.errorMessage = result.raws.error_message;
	            $scope.clearMessage(); 
	        }
   			blockUI.stop();
		}



		$scope.userBankDetails = {};
		$scope.getBankDetails = function() { 
            blockUI.start(); 
            var param = {
	            'bank_id'		: $stateParams.bankId,
		        'user_pass_key'	: $cookies.get('user_pass_key'),
	        	'user_id'		: $cookies.get('user_id')         
            };            

        	ajaxService.ApiCall(param, CONFIG.ApiUrl+'lender/profile/getBankDetails', $scope.getBankDetailsSuccess, $scope.getBankDetailsError, 'post');
		}
		//getBankDetails success function
		$scope.getBankDetailsSuccess = function(result,status) {
		    if(status == 200) {
		    	blockUI.stop();
		    	$scope.userBankDetails = result.raws.data;
		    }		       
		}
		//getBankDetails error function
		$scope.getBankDetailsError = function(result,status) {
			if(status == 403){
                helper.unAuthenticate();
            } else {
	            $scope.errorMessage = result.raws.error_message;

				$timeout(function() {
	            	$scope.clearMessage(); 
		    		$location.path('/dashboard/profile/bank');
	            }, CONFIG.TimeOut);

	        }
   			blockUI.stop();
		}
		
		if($state.$current.name == 'profile.bank-edit'){
			if($stateParams.bankId != '')
				$scope.getBankDetails();
			//console.log($stateParams.bankId);
		}


		$scope.doSearchIFSCCode = function(){
	        var ifsccodeDataParam = {			
		        'user_pass_key'	: $cookies.get('user_pass_key'),
	        	'user_id'		: $cookies.get('user_id'),  
	        	'ifsc_code'      : $scope.userBankDetails.ifsc_code,
	        }

        	ajaxService.ApiCall(ifsccodeDataParam, CONFIG.ApiUrl+'lender/profile/getIFSCDetails', $scope.searchIFSCCodeSuccess, $scope.searchIFSCCodeError, 'post');
		}
		//searchPincode success function
		$scope.searchIFSCCodeSuccess = function(result,status) {
		    if(status == 200) {
		    	blockUI.stop();
	    		$scope.userBankDetails.fk_bank_id = result.raws.data.id;
	    		$scope.userBankDetails.bank_name = result.raws.data.bank_name;
	    		$scope.userBankDetails.bank_branch = result.raws.data.bank_branch;
	    		$scope.userBankDetails.bank_city = result.raws.data.bank_city;
	    		$scope.userBankDetails.bank_state = result.raws.data.bank_state;
		    }		       
		}
		//searchPincode error function
		$scope.searchIFSCCodeError = function(result,status) {
			if(status == 403){
                helper.unAuthenticate();
            } else {
	            alert(result.raws.error_message);
	    		$scope.userBankDetails.fk_bank_id = 0;
	    		$scope.userBankDetails.bank_name = '';
	    		$scope.userBankDetails.bank_branch = '';
	    		$scope.userBankDetails.bank_city = '';
	    		$scope.userBankDetails.bank_state = '';
	        }
   			blockUI.stop();
		}



		$scope.doBankEdit = function(userBankDetails){
	        var userBankDetailsParam = {			
		        'user_pass_key'	: $cookies.get('user_pass_key'),
	        	'user_id'		: $cookies.get('user_id')         
	        }
	        angular.extend(userBankDetailsParam, userBankDetails);

        	ajaxService.ApiCall(userBankDetailsParam, CONFIG.ApiUrl+'lender/profile/addEditBank', $scope.bankEditSuccess, $scope.bankEditError, 'post');
		}
		//bankEdit success function
		$scope.bankEditSuccess = function(result,status) {
		    if(status == 200) {
		    	blockUI.stop();
            	$scope.successMessage = result.raws.success_message;
		    	$location.path('/dashboard/profile/bank');
		    }		       
		}
		//bankEdit error function
		$scope.bankEditError = function(result,status) {
			if(status == 403){
                helper.unAuthenticate();
            } else {
		    	//$window.scrollTo(0, 100);
	            $scope.errorMessage = result.raws.error_message;
	            $scope.clearMessage(); 
	        }
   			blockUI.stop();
		}
		$scope.viewKYCInfo = function(){
			$uibModal.open({
				animation: true,
				templateUrl: 'app/components/profile/views/kyc-info.modal.view.html',
				controllerAs : 'vkycic',
				controller: 'viewKYCInfoController',
				bindToController : true				
			});
		}

		$rootScope.setAsPrimaryIndex 	= '';
		$scope.setAsPrimary = function(bank_id, setAsPrimaryIndex){
			$rootScope.setAsPrimaryIndex 	= setAsPrimaryIndex;
	        var param = {
	        	'bank_id'		: bank_id,	
		        'user_pass_key'	: $cookies.get('user_pass_key'),
	        	'user_id'		: $cookies.get('user_id')         
	        }
        	ajaxService.ApiCall(param, CONFIG.ApiUrl+'lender/profile/setAsPrimary', $scope.setAsPrimarySuccess, $scope.setAsPrimaryError, 'post');
		}
		//setAsPrimary success function
		$scope.setAsPrimarySuccess = function(result,status) {
		    if(status == 200) {
	            $rootScope.userBank[$rootScope.setAsPrimaryIndex].is_primary = 'Y' ;

		    	blockUI.stop();
            	$scope.successMessage = result.raws.success_message;
            	$scope.clearMessage();
		    	//$location.path('/dashboard/profile/bank');

		    }		       
		}
		//setAsPrimary error function
		$scope.setAsPrimaryError = function(result,status) {
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


	