angular
	.module('mPokket')
	.controller('employeeController', ["$scope", 'ajaxService', 'CONFIG', '$location', '$timeout', '$cookies', '$state', "helper", "$rootScope",'$window', function($scope, ajaxService, CONFIG, $location, $timeout, $cookies, $state, helper, $rootScope,$window)
	{
		//alert($state.$current.name);

		$scope.employeeData 		= {};
	    $scope.pageno 				= 1; // initialize page no to 1
	    $scope.itemsPerPage 		= CONFIG.itemsPerPage; 
	    $scope.order_by 			= 'id';
	    $scope.order 				= 'desc';
	    $scope.searchByName 		= '';
		
		// Perform to getAllDegree action
		$scope.getAllEmployee = function(pageno, order_by, order)
		{ 
	        $scope.pageno 	= pageno ? pageno : 1;
	       	$scope.order_by = order_by ? order_by : 'id';
	        $scope.order 	= order ? order : 'desc';

	        var getemployeeParam = 
	        {
	        	'pass_key' 			: $cookies.get('pass_key'),
	        	'admin_user_id'		: $cookies.get('admin_user_id'),
	            'searchByName'		: $scope.searchByName,
	            'order_by'			: $scope.order_by,
	            'order'				: $scope.order,
	            'page'				: $scope.pageno,
	            'page_size'			: $scope.itemsPerPage
	        };

	        //alert(getDegreeParam.searchByName);
			ajaxService.ApiCall(getemployeeParam, CONFIG.ApiUrl+'employee/getAllEmployee', $scope.getAllEmployeeSuccess, $scope.getAllEmployeeError, 'post');
		}

		//getAllDegree success function
		$scope.getAllEmployeeSuccess = function(result,status) 
		{
			//console.log($status);
		    if(status == 200) 
		    {
                $scope.allEmployee 	= result.raws.data.dataset;
                $scope.total_count 	= result.raws.data.count;	        
		    }		       
		}

		//getAllDegree error function
		$scope.getAllEmployeeError = function(result) 
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

		/****************Search START******************/
		$scope.$watch('searchByName', function(tmpStr) 
		{
		    if (angular.isUndefined(tmpStr))
		    {		    	
		        return 0;
		    }
		    else if(tmpStr=='')
		    {
				$scope.getAllEmployee($scope.pageno, $scope.order_by, $scope.order, $scope.searchByName);
		    }
		    else
		    {
		    	$timeout(function() 
		    	{ 
			        if (tmpStr === $scope.searchByName) 
			        {
						$scope.getAllEmployee($scope.pageno, $scope.order_by, $scope.order, $scope.searchByName);
			        }
			    }, 1000);	
		    }		    
		});
		/**************** Search END ******************/
		
		// Perform the addEmployee action
		$scope.doaddemployee = function(employeeData) 
		{ 

		 ajaxService.ApiCall(employeeData, CONFIG.ApiUrl+'employee/addEmployee', $scope.addEmployeeSuccess, $scope.addEmployeeError, 'post');
		}

		//addDegree success function
		$scope.addEmployeeSuccess = function(result,status) 
		{
		    if(status == 200) 
		    {   $window.scrollTo(0, 100);
		    	$scope.successMessage = result.raws.success_message;
		    	$scope.clearMessage();
		    	$timeout(function() {
		        	$location.path('dashboard/employee/list');
		        }, CONFIG.TimeOut);
		    }		       
		}

		//addDegree error function
		$scope.addEmployeeError = function(result) 
		{
			window.scrollTo(0, 100);
            $scope.errorMessage = result.raws.error_message;
            $scope.clearMessage();
		}

		

		$scope.deleteEmployee = function(employeeeId,index)
		{
			
		    $scope.employeeIndex = index;
			var employeeParam = {
				'employeeID' 	: employeeeId,
			    'pass_key'		: $cookies.get('pass_key'),
	        	'admin_user_id' : $cookies.get('admin_user_id')
			};
			ajaxService.ApiCall(employeeParam, CONFIG.ApiUrl+'employee/deleteEmployee', $scope.deleteEmployeeSuccess, $scope.deleteEmployeeError, 'post');
		}

		$scope.deleteEmployeeSuccess = function(result, status)
		{
			if(status == 200)
			{
				alert($scope.employeeIndex);
				$scope.successMessage = result.raws.success_message;
				$scope.clearMessage();
				$scope.getAllEmployee.splice($scope.employeeIndex,1);
				//window.location.href='#/dashboard/employee/list';
				
			}
		}

		$scope.deleteEmployeeError = function(result, status)
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

		$scope.clearMessage = function()
		{
			$timeout(function() {
        		$scope.successMessage = '';
                $scope.errorMessage = '';
            }, CONFIG.TimeOut);
		}



	}])

.controller('editEmployeeController',["$scope", 'ajaxService', 'CONFIG', '$location', '$timeout', '$cookies', '$state', "helper", "$rootScope",'$stateParams','$window',function($scope,ajaxService,CONFIG,$location,$timeout,$cookies, $state, helper, $rootScope, $stateParams,$window){
	$scope.employeeDetail 	= {};
	$scope.employeeID 		= $stateParams.employeeID;
	$scope.successMessage 	= '';
    $scope.errorMessage 	= '';
 // Perform to getDegreeDetail action
		$scope.getEmployeeDetail = function()
		{ 
			var employeeParam = {'employeeID' : $scope.employeeID};
			ajaxService.ApiCall(employeeParam, CONFIG.ApiUrl+'employee/getEmployeeDetail', $scope.getDegreeDetailSuccess, $scope.getDegreeDetailError, 'post');
		}

 //getDegreeDetail success function
		$scope.getDegreeDetailSuccess = function(result,status) 
		{
		    if(status == 200) 
		    {
                $scope.employeeDetail = result.raws.data.dataset;
		    }
		}

		//getDegreeDetail error function
		$scope.getDegreeDetailError = function(result) 
		{
            $scope.errorMessage = result.raws.error_message;
            $scope.clearMessage();
		}

		if($state.$current.name == 'employee.update-employee')
		{
			$scope.getEmployeeDetail();
		}

        $scope.updateEmployeeDetail= function(employeeDetail){
 ajaxService.ApiCall(employeeDetail, CONFIG.ApiUrl+'employee/updateEmployeeDetail',
 $scope.updateEmployeeDetailSuccess,$scope.updateEmployeeDetailError, 'post');
}

//updateDegreeDetail success function
		$scope.updateEmployeeDetailSuccess = function(result,status) 
		{
		    if(status == 200) 
		    {
		    	$window.scrollTo(0, 100);
                $scope.successMessage = result.raws.success_message;
                $scope.clearMessage();
                $timeout(function() {
		        $location.path('dashboard/employee/list');
		        }, CONFIG.TimeOut);
		    }
		}

		//updateDegreeDetail error function
		$scope.updateEmployeeDetailError = function(result) 
		{
			window.scrollTo(0, 100);
            $scope.errorMessage = result.raws.error_message;
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

