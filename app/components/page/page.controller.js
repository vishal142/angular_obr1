angular
	.module('mPokket')
	.controller('pageController',["$scope", "$rootScope", '$stateParams', '$timeout', "$uibModal", 'helper', 'CONFIG', 'ajaxService', '$location', '$cookies', "blockUI",'$state', '$window', function($scope, $rootScope, $stateParams, $timeout, $uibModal, helper, CONFIG, ajaxService, $location, $cookies, blockUI, $state, $window){
  
   $scope.getAboutdetail = function(){
   var param = {};
   //alert(); 
   ajaxService.ApiCall(param,CONFIG.ApiUrl+'page/page/getAboutdetail',$scope.getAboutdetailSuccess,$scope.getAboutdetailError,'post');
   
    }

    $scope.getAboutdetailSuccess = function(result,status) {
		    if(status == 200) {
		    	$scope.aboutDetail = result.raws.data;
        //  alert($scope.aboutDetail);
		    	//console.log($scope.editProfile);
		    }		       
		}
		//getUser error function
	$scope.getAboutdetailError = function(result) {
            $scope.errorMessage = result.raws.error_message;
            //helper.showErrorMessage(result.raws.error_message);
            $scope.clearMessage();
		}

    if($state.$current.name == 'about-us'){
    $scope.getAboutdetail();

  }

  }])