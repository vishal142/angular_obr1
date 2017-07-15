angular
    .module('mPokket')
    .controller('conversationsController', ["$scope", "$rootScope", '$stateParams', '$timeout', "$uibModal", 'helper', 'CONFIG', 'ajaxService', '$location', '$cookies', "blockUI", '$state', function($scope, $rootScope, $stateParams, $timeout, $uibModal, helper, CONFIG, ajaxService, $location, $cookies, blockUI, $state) {

        $scope.pageno                   = 1; // initialize page no to 1
        $scope.itemsPerPage             = CONFIG.itemsPerPage; 
        $scope.order_by                 = 'id';
        $scope.order                    = 'desc';
        $scope.filterByStatus           = '';
        $scope.allTicket                = {};


        $scope.getAllTicket = function(pageno) {
            blockUI.start();
            
            $scope.pageno   = pageno ? pageno : 1;
            var param = {
                'user_pass_key'         : $cookies.get('user_pass_key'),
                'user_id'               : $cookies.get('user_id'),
                'filterByStatus'        : $scope.filterByStatus,
                'order_by'              : $scope.order_by,
                'order'                 : $scope.order,
                'page'                  : $scope.pageno,
                'page_size'             : $scope.itemsPerPage
            };

            ajaxService.ApiCall(param, CONFIG.ApiUrl + 'lender/conversations/getAllTicket', $scope.getUserTicketSuccess, $scope.getUserTicketError, 'post');
        }

        //getAllTicket success function
        $scope.getUserTicketSuccess = function(result, status) {
                if (status == 200) {
                    blockUI.stop();
                    $scope.allTicket     = result.raws.data.dataset;
                    $scope.total_count   = result.raws.data.count;
                }
            }


            //getAllTicket error function
        $scope.getUserTicketError = function(result, status) {
            if (status == 403) {
                helper.unAuthenticate();
            } else {
                $scope.errorMessage = result.raws.error_message;
                $scope.clearMessage();
            }
            blockUI.stop();
        }

        if ($state.$current.name == 'conversations.list') {
            $scope.getAllTicket();
        }


        // add new Ticket functions
        $scope.addTickit = function(ticketData) {
            blockUI.start();
            var param = {
                'user_pass_key': $cookies.get('user_pass_key'),
                'user_id': $cookies.get('user_id'),
            };

            angular.extend(param, ticketData);    
            ajaxService.ApiCall(param, CONFIG.ApiUrl + 'lender/conversations/addTicket', $scope.addTicketSuccess, $scope.addTicketError, 'post');
        }

        //addTickit success function

        $scope.addTicketSuccess = function(result, status) {
            if (status == 200) {
                blockUI.stop();
                $rootScope.ticket_id = result.raws.data.ticket_no;
                //console.log($rootScope.ticket_id);
                $location.path('/dashboard/conversations/success');
            }
        }
        
        //addTickit error function
        $scope.addTicketError = function(result, status) {
            if (status == 403) {
                helper.unAuthenticate();
            } else {
                $scope.errorMessage = result.raws.error_message;
                $scope.clearMessage();
            }
            blockUI.stop();
        }

        //  getEachTickitDetails functions

        $scope.getEachTickitDetails = function() {
            blockUI.start();
            var param = {
                'user_pass_key': $cookies.get('user_pass_key'),
                'user_id': $cookies.get('user_id'),
                'ticket_id': $stateParams.conversationsId 
            };

            ajaxService.ApiCall(param, CONFIG.ApiUrl + 'lender/conversations/fetchTickitDetails', $scope.getTicketDetailsSuccess, $scope.getTicketDetailsError, 'post');
        }

        //getEachTickitDetails success function
        $scope.ticketDetails = {};
        $scope.getTicketDetailsSuccess = function(result, status) {
            if (status == 200) {
                blockUI.stop();
                $scope.ticketDetails = result.raws.data.dataset;
                //$location.path('/dashboard/give-cash/mpokket-wallet');
            }
        }
            //getEachTickitDetails error function
        $scope.getTicketDetailsError = function(result, status) {
            if (status == 403) {
                helper.unAuthenticate();
            } else {
                $scope.errorMessage = result.raws.error_message;
                $scope.clearMessage();
            }
            blockUI.stop();
        }

        if ($state.$current.name == 'conversations.details') {
            $scope.getEachTickitDetails();
        }

        $scope.conversationThreads      = {};
        $scope.addTickitThreads = function(conversationThreads) {
            blockUI.start();
            //console.log(conversationThreads);
            var param = {
                'user_pass_key': $cookies.get('user_pass_key'),
                'user_id': $cookies.get('user_id'),
                'ticket_id'  : $scope.ticketDetails.id,                              //  manually given
                'description': conversationThreads.description
            };

            //angular.extend(param, conversationThreads);   

            ajaxService.ApiCall(param, CONFIG.ApiUrl + 'lender/conversations/addTickitThreads', $scope.addTicketThreadsSuccess, $scope.addTicketThreadsError, 'post');
        }

        //addTickit success function
        $scope.addTicketThreadsSuccess = function(result, status) {
            if (status == 200) {
                blockUI.stop();
                $scope.ticketDetails.all_conversation_threads.push({ 
                    'id'                    : result.raws.data.id,
                    'fk_support_ticket_id'  : result.raws.data.fk_support_ticket_id,
                    'fk_user_id'            : result.raws.data.fk_user_id,
                    'fk_admin_id'           : result.raws.data.fk_admin_id,
                    'description'           : result.raws.data.description,
                    'added_timestamp'       : result.raws.data.added_timestamp,
                    'is_unread'             : result.raws.data.is_unread,
                });

                $scope.clearMessage();
                $location.hash('bottom');

                $scope.conversationThreads.description = '';


                }
            }
            //addTickit error function
        $scope.addTicketThreadsError = function(result, status) {
            if (status == 403) {
                helper.unAuthenticate();
            } else {
                $scope.errorMessage = result.raws.error_message;
                $scope.clearMessage();
            }
            blockUI.stop();
        }




        $scope.clearMessage = function() {
            $timeout(function() {
                $scope.successMessage = '';
                $scope.errorMessage = '';
            }, CONFIG.TimeOut);
        }
    }])