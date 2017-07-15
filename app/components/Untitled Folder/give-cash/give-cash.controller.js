angular
	.module('mPokket')
	.controller('give-cashController', ["$scope", "$rootScope", '$stateParams', '$timeout', "$uibModal", 'helper', 'CONFIG', 'ajaxService', '$location', '$cookies', "blockUI",'$state', function($scope, $rootScope, $stateParams, $timeout, $uibModal, helper, CONFIG, ajaxService, $location, $cookies, blockUI, $state){
		
		$scope.walletAmount = {};
		$rootScope.account_no = '';

		$scope.filterByAmount           = '';
		$scope.filterByTenure           = '';
		$scope.filterByPaymntType       = '';
        $scope.searchByCity        		= '';
        $scope.searchByState         	= '';
        $scope.searchByColg         	= '';
        $scope.pageno 					= 1; // initialize page no to 1
	    $scope.itemsPerPage 			= CONFIG.itemsPerPage; 
	    $scope.order_by 				= 'id';
	    $scope.order 					= 'desc';
		$scope.updatedWalletAmount		= 0;
		$scope.totalQty 				= 0;
		$scope.totalAmt 				= 0;

	    // *****get user wallet amount***//


		$scope.getWalletBalance = function() { 
            blockUI.start();       
            var param = {
		        'user_pass_key'				: $cookies.get('user_pass_key'),
	        	'user_id'					: $cookies.get('user_id'),
            };          
        	ajaxService.ApiCall(param, CONFIG.ApiUrl+'lender/transactions/getWalletAmount', $scope.getUserWalletSuccess, $scope.getUserWalletError, 'post');
		}
		//getUserProfile success function
		$scope.getUserWalletSuccess = function(result,status) {
		    if(status == 200) {
		    	blockUI.stop();
		    	$scope.walletAmount = result.raws.data;
		    	$rootScope.account_no = $scope.walletAmount.account_no;
		    	//console.log('after calling->>'+$rootScope.account_no);
		    	//$location.path('/dashboard/give-cash/mpokket-wallet');
		    	//console.log($scope.walletAmount);
		    }		       
		}
		//getUserProfile error function
		$scope.getUserWalletError = function(result,status) {
			if(status == 403){
                helper.unAuthenticate();
            } else {
	            $scope.errorMessage = result.raws.error_message;
	            $scope.clearMessage(); 
	        }
   			blockUI.stop();
		}

		if($state.$current.name == 'give-cash.mpokket-wallet'  || $state.$current.name == 'give-cash.withdrawal' ){
			$scope.getWalletBalance();
		}
		/*if($state.$current.name == 'give-cash.withdrawal'){
			$scope.getWalletBalance();
		}*/




//***** get all loan request**//


		$scope.allCashRequest = function(amount,tenure,p_type) { 
            blockUI.start(); 
            /*$scope.pageno 	= pageno ? pageno : 1;
	       	$scope.order_by = order_by ? order_by : 'id';
	        $scope.order 	= order ? order : 'desc';   */    
            var param = {
		        'user_pass_key'	: $cookies.get('user_pass_key'),
	        	'user_id'		: $cookies.get('user_id'),
	        	'search_input_principle' 	: $scope.filterByAmount ,
	        	'search_input_npm'			: $scope.filterByTenure,
	        	'search_fk_payment_type_id'	: $scope.filterByPaymntType ,
	        	'search_city_name'			: $scope.searchByCity ,
	        	'search_state_name'			: $scope.searchByState,
	        	'search_name_of_institution': $scope.searchByColg,
	        	/*'order_by'					: $scope.order_by,
				'order'						: $scope.order,
				'page'						: $scope.pageno,
				'page_size'                 : $scope.itemsPerPage*/

            };            

        	ajaxService.ApiCall(param, CONFIG.ApiUrl+'lender/transactions/allCashRequest', $scope.getCashRequestSuccess, $scope.getCashRequestError, 'post');
		}
		//getUserProfile success function
		$scope.getCashRequestSuccess = function(result,status) {
		    if(status == 200) {
		    	blockUI.stop();
		    	$scope.walletAmount 	  = result.raws.data.wallet_amount;
		    	$scope.updatedWalletAmount= result.raws.data.wallet_amount;

		    	$scope.allUserCashRequest = result.raws.data.dataset;
		    	$scope.total_count 		  = result.raws.data.total_data;
		    	$scope.principle_amount   = result.raws.data.cash_request;
		    	$scope.payment_type 	  = result.raws.data.payment_type;
		    }		       
		}
		//getUserProfile error function
		$scope.getCashRequestError = function(result,status) {
			if(status == 403){
                helper.unAuthenticate();
            } else {
	            $scope.errorMessage = result.raws.error_message;
	            $scope.clearMessage(); 
	        }
   			blockUI.stop();
		}

		if($state.$current.name == 'give-cash.select-user'){
			$scope.allCashRequest();
		}

		

		$scope.loan_ids = [];
		$scope.selectLoanUser = function(a_id,loan_id,loan_amount) {
		    if($.inArray(loan_id, $scope.loan_ids) === -1){
		    	$scope.loan_ids.push(loan_id);	
		    	$('#'+a_id).addClass('selected_by_user');
		    	$scope.updatedWalletAmount =  parseInt($scope.updatedWalletAmount) - parseInt(loan_amount);
		    }else{
				var index = $scope.loan_ids.indexOf(loan_id);
				if(index != -1){
    				$scope.loan_ids.splice( index, 1 );
		    		$('#'+a_id).removeClass('selected_by_user');
		    		$scope.updatedWalletAmount = parseInt($scope.updatedWalletAmount) + parseInt(loan_amount);
				}
		    }
			return false;        
        }



		//  get auto allocate details **///
		$scope.autoAllocate = function() { 
            blockUI.start();  
            var param = {
		        'user_pass_key'	: $cookies.get('user_pass_key'),
	        	'user_id'		: $cookies.get('user_id'),
            };            

        	ajaxService.ApiCall(param, CONFIG.ApiUrl+'lender/transactions/autoAllocate', $scope.autoAllocateSuccess, $scope.autoAllocateError, 'post');
		}
		//getUserProfile success function
		$scope.autoAllocateSuccess = function(result,status) {
		    if(status == 200) {
		    	blockUI.stop();
		    	$scope.autoAllocateData = result.raws.data;
		    	$scope.updatedWalletAmount= result.raws.data.wallet_amount;
		    	$scope.changeAutoAllocation();
		    	//$scope.walletAmount = result.raws.data;
		    }		       
		}
		//getUserProfile error function
		$scope.autoAllocateError = function(result,status) {
			if(status == 403){
                helper.unAuthenticate();
            } else {
	            $scope.errorMessage = result.raws.error_message;
	            $scope.clearMessage(); 
	        }
   			blockUI.stop();
		}

		if($state.$current.name == 'give-cash.auto-allocation'){
			$scope.autoAllocate();
		}



		// give cash to selected users 
		$scope.giveCashSelectedUsers = function() { 
            blockUI.start();  
            var param = {
		        'user_pass_key'	: $cookies.get('user_pass_key'),
	        	'user_id'		: $cookies.get('user_id'),
	        	'loan_ids'		: $scope.loan_ids
            };            

        	ajaxService.ApiCall(param, CONFIG.ApiUrl+'lender/transactions/giveCash', $scope.giveCashSuccess, $scope.giveCashError, 'post');
		}
		$scope.giveCashSuccess = function(result,status) {
		    if(status == 200) {
		    	blockUI.stop();
		    	
		    	//$scope.successMessage = result.raws.success_message;
		    	//$scope.walletAmount = result.raws.data;
		        $location.path('dashboard/give-cash/success');


		    }		       
		}


		$scope.giveCashError = function(result,status) {
			if(status == 403){
                helper.unAuthenticate();
            } else {
	            $scope.errorMessage = result.raws.error_message;
	            $scope.clearMessage(); 
	        }
   			blockUI.stop();
		}


		$scope.changeAutoAllocation = function(){
			var totalQty = 0;
			var totalAmt = 0;

			angular.forEach($scope.autoAllocateData.auto_allocated, function(value, key) {
				var qty = 0;
				var amt = 0;
				//console.log('BEFORE: '+qty+'<=======>'+totalQty+'<=======>'+amt+'<=======>'+totalAmt); 

				qty = (value.total_loan_no == '') ? 0 : (value.total_loan_no);
				amt = parseInt(value.amount) * parseInt(qty);

				totalQty = parseInt(totalQty) + parseInt(qty);				
				totalAmt = parseInt(totalAmt) + parseInt(amt);
				/*console.log('AFTER: '+qty+'<=======>'+totalQty+'<=======>'+amt+'<=======>'+totalAmt); 
				console.log('<=======> <=======> <=======> <=======>');*/ 
			});
			$scope.totalQty = totalQty;
			$scope.totalAmt = totalAmt;

			$scope.updatedWalletAmount =  parseInt($scope.autoAllocateData.wallet_amount) - parseInt(totalAmt);
		}

		// give cash to selected users 
		$scope.giveCashAutoAllocation = function() { 
            blockUI.start();  
            var param = {
		        'user_pass_key'	: $cookies.get('user_pass_key'),
	        	'user_id'		: $cookies.get('user_id'),
	        	'loans'			: JSON.stringify($scope.autoAllocateData.auto_allocated),
            };            

        	ajaxService.ApiCall(param, CONFIG.ApiUrl+'lender/transactions/giveCashAutoAllocated', $scope.giveCashAutoAllocationSuccess, $scope.giveCashAutoAllocationError, 'post');
		}
		$scope.giveCashAutoAllocationSuccess = function(result,status) {
		    if(status == 200) {
		    	blockUI.stop();
		    	
		    	//$scope.successMessage = result.raws.success_message;
		    	//$scope.walletAmount = result.raws.data;
		        $location.path('dashboard/give-cash/success');
		    }		       
		}


		$scope.giveCashAutoAllocationError = function(result,status) {
			if(status == 403){
                helper.unAuthenticate();
            } else {
	            $scope.errorMessage = result.raws.error_message;
	            $scope.clearMessage(); 
	        }
   			blockUI.stop();
		}


		$scope.viewAddCashPopup = function(){
			$uibModal.open({
				animation: true,
				templateUrl: 'app/components/give-cash/views/add-cash.modal.details.view.html',
				controllerAs : 'vacc',
				controller: 'viewAddCashController',
				bindToController : true
			});
			$scope.getWalletBalance();
		}


		$scope.doWithdrawlPopup = function(amount){
			$rootScope.withdrawlMoney =  amount;
			$uibModal.open({
				animation: true,
				templateUrl: 'app/components/give-cash/views/withdrawl.modal.details.view.html',
				controllerAs : 'dwc',
				controller: 'doWithdrawlController',
				bindToController : true
			});
/*			$scope.getWalletBalance();
*/		}


		$scope.clearMessage = function(){
			$timeout(function() {
        		$scope.successMessage = '';
                $scope.errorMessage = '';
            }, CONFIG.TimeOut);
		}
	}])


	.controller('viewAddCashController', ["$scope", 'ajaxService', 'CONFIG', '$location', '$timeout', '$cookies', "$uibModalInstance", "$rootScope", function($scope, ajaxService, CONFIG, $location, $timeout, $cookies, $uibModalInstance, $rootScope){

		$scope.cancel = function(){
			$uibModalInstance.dismiss('cancel');
		}
	}])


	.controller('doWithdrawlController', ["$scope", 'ajaxService', 'CONFIG', '$location', '$timeout', '$cookies', "$uibModalInstance", "$rootScope", function($scope, ajaxService, CONFIG, $location, $timeout, $cookies, $uibModalInstance, $rootScope){
		//$rootScope.withdrawlMoney;

		$scope.cancel = function(){
			$uibModalInstance.dismiss('cancel');
		}


		$scope.withdrawlDetails = function() { 
/*            blockUI.start(); 
*/ 
            var param = {
		        'user_pass_key'	: $cookies.get('user_pass_key'),
	        	'user_id'		: $cookies.get('user_id'),

            };            

        	ajaxService.ApiCall(param, CONFIG.ApiUrl+'lender/profile/getPrimaryBank', $scope.withdrawlDetailsSuccess, $scope.withdrawlDetailsError, 'post');
		}
		$scope.withdrawlDetailsSuccess = function(result,status) {
		    if(status == 200) {
/*		    	blockUI.stop();
*/		    	$scope.bankDetails = result.raws.data;
/*				$scope.withdrawlAmount = 500;
*/
		    	//$scope.walletAmount = result.raws.data;


		    }		       
		}


		$scope.withdrawlDetailsError = function(result,status) {
			if(status == 403){
                helper.unAuthenticate();
            } else {
	            $scope.errorMessage = result.raws.error_message;
	            $scope.clearMessage(); 
	        }
   			blockUI.stop();
		}
		$scope.withdrawlDetails();



		$scope.doWithdrawlfinal = function(amount) { 
/*            blockUI.start(); 
*/ 
            var param = {
		        'user_pass_key'	: $cookies.get('user_pass_key'),
	        	'user_id'		: $cookies.get('user_id'),
	        	'amount'		: amount
            };            

        	ajaxService.ApiCall(param, CONFIG.ApiUrl+'lender/transactions/withdrawAmount', $scope.doWithdrawlSuccess, $scope.doWithdrawlError, 'post');
		}
		$scope.doWithdrawlSuccess = function(result,status) {
		    if(status == 200) {
/*		    	blockUI.stop();
*/		    	$scope.successMessage = result.raws.success_message;
		    	$scope.clearMessage();
				$scope.cancel();
				$location.path('dashboard/give-cash/mpokket-wallet');


		    }		       
		}


		$scope.giveCashError = function(result,status) {
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