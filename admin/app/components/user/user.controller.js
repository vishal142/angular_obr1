angular
 .module('mPokket')
 .controller('userController', ["$scope", "$rootScope", '$stateParams', '$timeout', "$uibModal", 'helper', 'CONFIG', 'ajaxService', '$location', '$cookies', "blockUI",'$state', '$window', function($scope, $rootScope, $stateParams, $timeout, $uibModal, helper, CONFIG, ajaxService, $location, $cookies, blockUI, $state, $window){
 //$scope.doUpdateProfile = {};
 $scope.getAlluser = function(pageno,order_by,order){

 		$scope.employeeData 		= {};
	    $scope.pageno 				= 1; // initialize page no to 1
	    $scope.itemsPerPage 		= CONFIG.itemsPerPage; 
	    $scope.order_by 			= 'id';
	    $scope.order 				= 'desc';
	    $scope.searchByName 		= '';

 	$scope.pageno = pageno ? pageno : 1;
 	$scope.order_by = order_by ? order_by : 'id';
 	$scope.order = order ? order : 'desc';

 	var getuserParam = 
 	{

 		'pass_key': $cookies.get('pass_key'),
 		'admin_user_id' : $cookies.get('admin_user_id'),
 		'searchByName' : $scope.searchByName,
 		'order_by' : $scope.order_by,
 		'order' : $scope.order,
 		'page'  : $scope.page,
 		'page_size' : $scope.page_size
 	};

  ajaxService.ApiCall(getuserParam, CONFIG.ApiUrl+'user/getAlluser',$scope.getAlluserSuccess,$scope.getAlluserError,'post');


 }

 //getAllDegree success function
		$scope.getAlluserSuccess = function(result,status) 
		{
			//console.log($status);
		    if(status == 200) 
		    {
                $scope.alluser 	= result.raws.data.dataset;
                $scope.total_count 	= result.raws.data.count;	        
		    }		       
		}

		//getAllDegree error function
		$scope.getAlluserError = function(result) 
		{
            if(status == 403)
            {
                helper.unAuthenticate();
            }
            else
            {
                $scope.errorMessage = result.raws.error_message;
                $scope.clearMessage();
            }
		}


 $scope.getAlluser($scope.pageno,$scope.order_by, $scope.order, $scope.searchByName);


 $scope.viewUserDetailsPopup = function(user_id){
 	$rootScope.user_id = user_id ? user_id : '';
 	$uibModal.open({
   	animation : true,
   	templateUrl: 'app/components/user/views/user.modal.details.view.html',
   	controllerAs : 'vbdc',
	controller: 'viewUserDetailsController',
	bindToController : true

   });
 }

 }])


.controller('viewUserDetailsController',["$scope", 'ajaxService', 'CONFIG', '$location', '$timeout', '$cookies', "$uibModalInstance", "$rootScope", function($scope, ajaxService, CONFIG, $location, $timeout, $cookies, $uibModalInstance, $rootScope){
    
    $scope.userDeatil = function(){
	var userDetailparam = {

	  'user_id' : $rootScope.user_id,
	  'pass_key' : $cookies.get('pass_key'),
	  'admin_user_id' : $cookies.get('admin_user_id')
	};

	ajaxService.ApiCall(userDetailparam,CONFIG.ApiUrl+'user/userDeatil',$scope.userDeailSuccess,$scope.userDeailError,'post');

   }

   $scope.userDeailSuccess = function(result,status) {
   	 if(status == 200) {
            	$scope.successMessage = result.raws.success_message;
            	$scope.userDetails 	= result.raws.data.dataset;
            	$scope.clearMessage();  
		        //$location.path('dashboard/banks/list');
		    }		       
		}
		$scope.userDeailError = function(result) {
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


	$scope.cancel = function(){
	 $uibModalInstance.dismiss('cancel');
		}

	$scope.userDeatil();


 $scope.changeStatus = function(em){

  var userDetailparam = {

  	'user_id' : $rootScope.user_id,
  	'pass_key' : $cookies.get('pass_key'),
  	'admin_user_id' : $cookies.get('admin_user_id'),
  	'is_verify' : em
   }
  
  ajaxService.ApiCall(userDetailparam,CONFIG.ApiUrl+'user/emailVerify',$scope.userStatusChanegeSuccess,$scope.userStatusChanegeError,'post');

 	
 }

 $scope.verify_phone = function(em){
 	var userDetailparam = {
 		'user_id' : $rootScope.user_id,
 		'pass_key' : $cookies.get('pass_key'),
 		'admin_user_id' : $cookies.get('admin_user_id'),
 		'is_verify' : em
 	}

  ajaxService.ApiCall(userDetailparam,CONFIG.ApiUrl+'user/verifyPhone',$scope.verifyPhoneSuccess,$scope.verifyPhoneError,'post');
 }

}]);