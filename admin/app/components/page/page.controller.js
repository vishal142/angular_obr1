angular
	.module('mPokket')
	.directive('ckEditor', function () {
        return {
                require: '?ngModel',
                link: function (scope, elm, attr, ngModel) {
                    var ck = CKEDITOR.replace(elm[0]);
                    if (!ngModel) return;
                    ck.on('instanceReady', function () {
                        ck.setData(ngModel.$viewValue);
                    });
                    function updateModel() {
                        scope.$apply(function () {
                        ngModel.$setViewValue(ck.getData());
                    });
                }
                ck.on('change', updateModel);
                ck.on('key', updateModel);
                ck.on('dataReady', updateModel);

                ngModel.$render = function (value) {
                    ck.setData(ngModel.$viewValue);
                };
            }
        };
    })
    
	.controller('pageController', ["$scope", 'ajaxService', 'CONFIG', '$location', '$timeout', '$cookies', '$state', "helper", "$rootScope",'$window', function($scope, ajaxService, CONFIG, $location, $timeout, $cookies, $state, helper, $rootScope,$window)
	{

	 
	 //alert($state.$current.name);
      //$scope.getAboutdetails = {};

   $scope.getAboutdetails = function(){
   	var param = {
   		'pass_key' : $cookies.get('pass_key'),
   		'admin_user_id' : $cookies.get('admin_user_id'),
   		'menu_page_name' : 'about',

   	};
   //console.log('inside get about');
   //alert(param.menu_page_name);

   ajaxService.ApiCall(param, CONFIG.ApiUrl+'Page/getAboutdetails',$scope.getAboutdetailsSuccess,$scope.getAboutdetailsError,'post');
   
   }

   $scope.getAboutdetailsSuccess = function(result,status) {
		    if(status == 200) {
		    	$scope.aboutDetails = result.raws.data;
		    	console.log($scope.aboutDetails);
		    }		       
		}
		//getUser error function
		$scope.getAboutdetailsError = function(result) {
            $scope.errorMessage = result.raws.error_message;
            //helper.showErrorMessage(result.raws.error_message);
            $scope.clearMessage();
		}

	 if($state.$current.name == 'manage_page.about-us'){

     $scope.getAboutdetails();
    }

    //alert($state.$current.name);
    $scope.uploadImg = function (element){
	$scope.about_img = element.files[0];
	}



  $scope.doAboutupdate = function(aboutDetails){
  	//alert($scope.about_img);

  	var formdata = new FormData();
	formdata.append('file', $scope.about_img);
	formdata.append('user_pass_key', $cookies.get('user_pass_key'));
	formdata.append('user_id', $cookies.get('user_id'));
	formdata.append('helpDescription',aboutDetails.page_body);

	angular.forEach(aboutDetails, function(value, key) {
		formdata.append(key, value);
			});

	    ajaxService.ApiCallImagePost(formdata, CONFIG.ApiUrl+'page/doAboutupdate',
 		$scope.doAboutupdateSuccess, $scope.doAboutupdateeError, 'post');

  }

 $scope.doAboutupdateSuccess = function(result,status) {
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

$scope.doAboutupdateeError = function(result) {
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






 }])