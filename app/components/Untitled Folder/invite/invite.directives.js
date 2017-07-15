angular.module('mPokket')
    /*
    * --------------------------------------------------------------------------
    * @ Directive Name           : agenstLeftMenu()
    * @ Added Date               : 14-04-2016
    * @ Added By                 : Subhankar
    * -----------------------------------------------------------------
    * @ Description              : admin left side menu is managed from here
    * -----------------------------------------------------------------
    * @ return                   : array
    * -----------------------------------------------------------------
    * @ Modified Date            : 14-04-2016
    * @ Modified By              : Subhankar
    * 
    */
    .directive('agentsLeftMenu', function() {
        return {
            controllerAs : 'alm',
            controller : function($timeout, CONFIG, ajaxService, $location){
                var alm = this;
                return alm;
            },
            //templateUrl: 'app/components/agents/views/agents.list.left.menu.html'
        };
    });


