/**
 * Load modules for application
 */

angular
    .module('mPokket', [
        'ui.router',
        'ui.bootstrap',
        'ajaxServices',
        'helperServices',
        'cgBusy',
        'ngCookies', 
        'angular-confirm',       
        'angularUtils.directives.dirPagination',
        'blockUI'
        /*'ui.sortable',
        'ngFileUpload',
        'ngAnimate',
        'monospaced.qrcode',
        'ui.bootstrap.datetimepicker',
        'ngSanitize',
        'ngImageInputWithPreview',
        'multipleSelect',
        'ngDragDrop'*/
    ])

    .constant('CONFIG',{
        module_name     : 'mPokket',
        DebugMode       : true,
        StepCounter     : 0,
        /*AssetsUrl     : 'https://admin.finzo.in/',
        ApiUrl          : 'https://apis.finzo.in/api_1_0_0/admin/',*/
        AssetsUrl       : 'http://'+(location.hostname)+'/angular_obr/admin/',
        ApiUrl          : 'http://'+(location.hostname)+'/angular_obr/apis/api_1_0_0/',
        TimeOut         : '2000',
        itemsPerPage    : '20'
    })

    .constant('oauth',{
        client_id       : '9d911a9a00ef11e48aff0019d114582',
        client_secret   : '463ceaeab4db11e3aa520019d119645',
        //token_url     : 'https://apis.finzo.in/api_1_0_0/user/token',
        token_url       : 'http://'+(location.hostname)+'/angular_obr/apis/api_1_0_0/user/token',
    });
