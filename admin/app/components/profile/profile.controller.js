angular
	.module('mPokket')
	//.controller('profileController',["$scope", "$rootScope", '$stateParams', '$timeout', "$uibModal", 'helper', 'CONFIG', 'ajaxService', '$location', '$cookies','$window', function($scope, $rootScope, $stateParams, $timeout, $uibModal, helper, CONFIG, ajaxService, $location, $cookies,$window){
.controller('profileController', ["$scope", "$rootScope", '$stateParams', '$timeout', "$uibModal", 'helper', 'CONFIG', 'ajaxService', '$location', '$cookies', "blockUI",'$state', '$window', function($scope, $rootScope, $stateParams, $timeout, $uibModal, helper, CONFIG, ajaxService, $location, $cookies, blockUI, $state, $window){
	

		$scope.doUpdateProfile = {};
		//$scope.profileDetail = {};

    $scope.getAdminProfile = function(){ 

            var param = {
		        'pass_key'			: $cookies.get('pass_key'),
	        	'admin_user_id'		: $cookies.get('admin_user_id'),         
            };            

   ajaxService.ApiCall(param, CONFIG.ApiUrl+'admin/getAdminProfile',$scope.getAdminProfileSuccess,$scope.getAdminProfileError,'post');
   // ajaxService.ApiCall(getemployeeParam, CONFIG.ApiUrl+'employee/getAdminProfile', $scope.getAllEmployeeSuccess, $scope.getAllEmployeeError, 'post');
		}

 $scope.getAdminProfileSuccess = function(result,status) {
		    if(status == 200) {
		    	$scope.profileDetail = result.raws.data;
		    	//console.log($scope.editProfile);
		    }		       
		}
		//getUser error function
		$scope.getAdminProfileError = function(result) {
            $scope.errorMessage = result.raws.error_message;
            //helper.showErrorMessage(result.raws.error_message);
            $scope.clearMessage();
		}

  $scope.uploadImg = function (element) {
	$scope.profile_img = element.files[0];
		}


  $scope.getAdminProfile();


$scope.doUpdateProfile =function(profileDetail){

 //alert(JSON.stringify(profileDetail));
//alert($scope.profile_img);
 
	var formdata = new FormData();
	formdata.append('file', $scope.profile_img);
	formdata.append('user_pass_key', $cookies.get('user_pass_key'));
	formdata.append('user_id', $cookies.get('user_id'));

	angular.forEach(profileDetail, function(value, key) {
		formdata.append(key, value);
			});


 ajaxService.ApiCallImagePost(formdata, CONFIG.ApiUrl+'admin/doUpdateProfile',
 $scope.doUpdateProfileSuccess, $scope.doUpdateProfileError, 'post');
}


 $scope.doUpdateProfileSuccess = function(result,status) {
 if(status == 200) {
 window.scrollTo(0, 100);
 $scope.successMessage = result.raws.success_message;
 $scope.profileDetail = result.raws.data;

 $scope.clearMessage();
 /*$timeout(function() {
 $location.path('dashboard/profile/edit');
 },CONFIG.TimeOut);*/
 //console.log($scope.editProfile);
  }		       
 }

$scope.doUpdateProfileError = function(result) {
	        window.scrollTo(0, 100);
            $scope.errorMessage = result.raws.error_message;
            //helper.showErrorMessage(result.raws.error_message);
            $scope.clearMessage();
		}
		
$scope.clearMessage = function()
		{
			$timeout(function() 
			{
        		$scope.successMessage = '';
                $scope.errorMessage = '';
            }, CONFIG.TimeOut);
		}


	}]);