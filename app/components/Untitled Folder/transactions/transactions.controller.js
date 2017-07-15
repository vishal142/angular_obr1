angular
	.module('mPokket')
	.controller('transactionsController', ["$scope", "$rootScope", '$stateParams', '$timeout', "$uibModal", 'helper', 'CONFIG', 'ajaxService', '$location', '$cookies', "blockUI",'$state', '$window', function($scope, $rootScope, $stateParams, $timeout, $uibModal, helper, CONFIG, ajaxService, $location, $cookies, blockUI, $state, $window){
		

		$scope.pageno                   = 1; // initialize page no to 1
        $scope.itemsPerPage             = CONFIG.itemsPerPage; 
        $scope.order_by                 = 'id';
        $scope.order                    = 'desc';
        $scope.filterByStatus           = '';
        $scope.searchBystartDate        = '';
        $scope.searchByEndDate          = '';
        $scope.filterByTenure          	= '';
        $scope.sentSuccessMessage		= '';
        $scope.sentErrorMessage		    = '';		

	$scope.transactionDashboard = function(){			

			

			var transactionDetailsParam = {
		        'user_pass_key'	: $cookies.get('user_pass_key'),
	        	'user_id'		: $cookies.get('user_id')
	        	
	      	};

			ajaxService.ApiCall(transactionDetailsParam, CONFIG.ApiUrl+'lender/transactions/lenderTransacDashboard', $scope.transactionDetailsSuccess, $scope.transactionDetailsError, 'post');
			}
		$scope.transactionDetailsSuccess = function(result,status) {
		    if(status == 200) {
            	$scope.successMessage = result.raws.success_message;
            	$scope.transactionDetails 	= result.raws.data;
            	//$scope.paymentsDetails 	    = result.raws.data.payments;

            	$scope.clearMessage();  
		        //$location.path('dashboard/banks/list');
		    }		       
		}
		$scope.transactionDetailsError = function(result) {
            if(status == 403){
                helper.unAuthenticate();
            } else {
	            $scope.errorMessage = result.raws.error_message;
	            $scope.clearMessage(); 
	        }
	        //$scope.cancel();
		}

	if($state.$current.name == 'transactions.details'){
			$scope.transactionDashboard();
		}





























	$scope.dateValidate = function(){
		if(($scope.searchBystartDate != "") && ($scope.searchByEndDate != "")){
			$scope.paymentDetails();
		}else
		{
			alert('PLEASE SELECT START DATE AND END DATE BOTH');
			return false;
		}
	}


	$scope.paymentDetails = function(pageno){		

			$scope.pageno   = pageno ? pageno : $scope.pageno;

			var transactionDetailsParam = {
		        'user_pass_key'		: $cookies.get('user_pass_key'),
	        	'user_id'			: $cookies.get('user_id')  ,
	        	'search_status' 	: $scope.filterByStatus,
	        	'search_tenure' 	: $scope.filterByTenure,
	        	'search_start_date' : $scope.searchBystartDate,
	        	'search_end_date'	: $scope.searchByEndDate,
                'order_by'      	: $scope.order_by,
                'order'         	: $scope.order,
                'page'          	: $scope.pageno,
                'page_size'     	: $scope.itemsPerPage              
	      	};
			ajaxService.ApiCall(transactionDetailsParam, CONFIG.ApiUrl+'lender/transactions/getAllTransacPayments', $scope.paymentDetailsSuccess, $scope.paymentDetailsError, 'post');
			}
		$scope.paymentDetailsSuccess = function(result,status) {
		    if(status == 200) {
            	$scope.successMessage 		= result.raws.success_message;
            	$scope.transactionDetails 	= result.raws.data.all_cash_token;
            	$scope.total_Count 			= result.raws.data.no_cash_token;
            	$scope.clearMessage();  
		        //$location.path('dashboard/banks/list');
		    }		       
		}
		$scope.paymentDetailsError = function(result) {
            if(status == 403){
                helper.unAuthenticate();
            } else {
	            $scope.errorMessage = result.raws.error_message;
	            $scope.clearMessage(); 
	        }
	        //$scope.cancel();
		}

	if($state.$current.name == 'transactions.payments'){
			$scope.paymentDetails();
		}

/////DOWNLOAD XLS

		$scope.emailTransactionsXLS = function(pageno){		

			$scope.pageno   = pageno ? pageno : $scope.pageno;

			var transactionDetailsParam = {
		        'user_pass_key'		: $cookies.get('user_pass_key'),
	        	'user_id'			: $cookies.get('user_id')  ,
	        	'search_status' 	: $scope.filterByStatus,
	        	'search_tenure' 	: $scope.filterByTenure,
	        	'search_start_date' : $scope.searchBystartDate,
	        	'search_end_date'	: $scope.searchByEndDate,
                'order_by'      	: $scope.order_by,
                'order'         	: $scope.order,
                'page'          	: $scope.pageno,
                'page_size'     	: $scope.itemsPerPage              
	      	};
			ajaxService.ApiCall(transactionDetailsParam, CONFIG.ApiUrl+'lender/transactions/downloadXLSLoan', $scope.sentMailSuccess, $scope.sentMailError, 'post');
			}
		$scope.sentMailSuccess = function(result,status) {
		    if(status == 200) {
            	$scope.sentSuccessMessage 		= result.raws.success_message;
            	$scope.clearMessage();  
		        //$location.path('dashboard/banks/list');
		    }		       
		}
		$scope.sentMailError = function(result) {
            if(status == 403){
                helper.unAuthenticate();
            } else {
	            $scope.sentErrorMessage = result.raws.error_message;
	            $scope.clearMessage(); 
	        }
	        //$scope.cancel();
		}

		$scope.emailTransactionsPDF = function(pageno){		

			$scope.pageno   = pageno ? pageno : $scope.pageno;

			var transactionDetailsParam = {
		        'user_pass_key'		: $cookies.get('user_pass_key'),
	        	'user_id'			: $cookies.get('user_id')  ,
	        	'search_status' 	: $scope.filterByStatus,
	        	'search_tenure' 	: $scope.filterByTenure,
	        	'search_start_date' : $scope.searchBystartDate,
	        	'search_end_date'	: $scope.searchByEndDate,
                'order_by'      	: $scope.order_by,
                'order'         	: $scope.order,
                'page'          	: $scope.pageno,
                'page_size'     	: $scope.itemsPerPage              
	      	};
			ajaxService.ApiCall(transactionDetailsParam, CONFIG.ApiUrl+'lender/transactions/downloadPDFLoan', $scope.sentMailSuccess, $scope.sentMailError, 'post');
			}
		$scope.sentMailSuccess = function(result,status) {
		    if(status == 200) {
            	$scope.sentSuccessMessage 		= result.raws.success_message;
            	$scope.clearMessage();  
		        //$location.path('dashboard/banks/list');
		    }		       
		}
		$scope.sentMailError = function(result) {
            if(status == 403){
                helper.unAuthenticate();
            } else {
	            $scope.sentErrorMessage = result.raws.error_message;
	            $scope.clearMessage(); 
	        }
	        //$scope.cancel();
		}
























		//open SendCodePopup
		$rootScope.loan_id = '';
		$scope.viewTransactionDetailsPopup = function(loan_id){
			$rootScope.loan_id = loan_id ? loan_id : '';

			$uibModal.open({
				animation: true,
				templateUrl: 'app/components/transactions/views/transactions.modal.details.view.html',
				controllerAs : 'vtdc',
				controller: 'viewTransactionDetailsController',
				bindToController : true
			});
		}


		$scope.clearMessage = function(){
			$timeout(function() {
        		$scope.successMessage = '';
                $scope.errorMessage = '';
            }, CONFIG.TimeOut);
		}
	}])



	.controller('viewTransactionDetailsController', ["$scope", 'ajaxService', 'CONFIG', '$location', '$timeout', '$cookies', "$uibModalInstance", "$rootScope", function($scope, ajaxService, CONFIG, $location, $timeout, $cookies, $uibModalInstance, $rootScope){
		//var vbdc = this;

		$scope.cancel = function(){
			$uibModalInstance.dismiss('cancel');
		}

		// Perform the loanDetails action
		$scope.loanDetails = function(){			
			var loanDetailsParam = {
				'loan_id' 		: $rootScope.loan_id,
		        'user_pass_key'	: $cookies.get('user_pass_key'),
	        	'user_id'		: $cookies.get('user_id')         
	      	};
			ajaxService.ApiCall(loanDetailsParam, CONFIG.ApiUrl+'lender/transactions/getloanDetails', $scope.loanDetailsSuccess, $scope.loanDetailsError, 'post');
			}
		$scope.loanDetailsSuccess = function(result,status) {
		    if(status == 200) {
            	$scope.successMessage = result.raws.success_message;
            	$scope.loanDetails 	= result.raws.data.loan_details;
            	$scope.paymentDetails 	= result.raws.data.payment_details;
            	$scope.clearMessage();  
		        //$location.path('dashboard/banks/list');
		    }		       
		}
		$scope.loanDetailsError = function(result) {
            if(status == 403){
                helper.unAuthenticate();
            } else {
	            $scope.errorMessage = result.raws.error_message;
	            $scope.clearMessage(); 
	        }
	        //$scope.cancel();
		}

		// Perform the clearMessage action
		$scope.clearMessage = function() {
			$timeout(function() {
        		$scope.successMessage = '';
                $scope.errorMessage = '';
            }, CONFIG.TimeOut);
		}
		$scope.loanDetails();

	}]);





	