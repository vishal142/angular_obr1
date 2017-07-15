angular.module('mPokket')
    .directive('adminStatusInHover', function () {
        return function(scope, element, attrs) {
            element.hover(function(e) {
                if($(this).hasClass('approve')){return};
                // userinfo details
                var x = $(this).position();
                var $userinfodetails = $(this).parent().next(".user-info-main");
                var $toppo = x.top - 27;
                var $leftpo = x.left - 285;
                $userinfodetails.css({
                    display: "block",
                    top: $toppo,
                    left: $leftpo
                })
                e.stopPropagation();
                e.preventDefault();
            },
            function() {
                var $userinfodetails = $(this).parent().next(".user-info-main");
                $userinfodetails.css({
                    display: "none"
                })
            });
        }
    });