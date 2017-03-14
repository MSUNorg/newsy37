/**
 * 后台JS模块声明
 *
 */

if (typeof(RES_BASE_DIR) === 'undefined') {
    alert('RES_BASE_DIR is undefined');
}

In.adds({
    'charset' : 'utf-8',
    'modules' : {
        // jquery-cookie
        'jquery-cookie'                        : {path : RES_BASE_DIR + 'res/js/jquery.cookie.js', type : 'js', charset : 'utf-8'},

        // admin-menu
        'admin-menu-css'                       : {path : RES_BASE_DIR + 'res/css/menu.css', type : 'css', charset : 'utf-8'},
        'admin-menu'                           : {path : RES_BASE_DIR + 'res/js/admin.menu.js', type : 'js', charset : 'utf-8', rely : ['jquery-cookie', 'admin-menu-css']},

        // artDialog
        'art_dialog-core'                      : {path : RES_BASE_DIR + 'res/js/artDialog.min.js', type : 'js', charset : 'utf-8'},
         'art_dialog-extend'                    : {path : RES_BASE_DIR + 'res/js/artDialog.plugins.min.js', type : 'js', charset : 'utf-8', rely : ['art_dialog-core']},
        'art_dialog-style-chrome'              : {path : RES_BASE_DIR + 'res/css/chrome.css', type : 'css', charset : 'utf-8', rely : ['art_dialog-core', 'art_dialog-extend']},
         'art_dialog-style-blue'                : {path : RES_BASE_DIR + 'res/css/blue.css', type : 'css', charset : 'utf-8', rely : ['art_dialog-core', 'art_dialog-extend']},

		//calendar 日期插件
        'calendar-css' 						   : {path : RES_BASE_DIR+ 'res/css/calendar-green.css', type:'css', charset:'utf-8'},
        'calendar'                             : {path : RES_BASE_DIR+ 'res/js/calendar.js', type:'js', charset:'utf-8', rely : ['calendar-css']},
		
		//My97DatePicker
		'My97DatePicker'					   : {path : RES_BASE_DIR+ 'res/js/WdatePicker.js', type:'js', charset:'utf-8'},

		//validator
        'validator' 						   : {path : RES_BASE_DIR+ 'res/js/validator.js', type:'js', charset:'utf-8'},
		
		//ajax-file-upload
        'ajax-file-upload' 						   : {path : RES_BASE_DIR+ 'res/js/ajaxfileupload.js', type:'js', charset:'utf-8'},
		
		//swfupload
        'swfupload' 							: {path : RES_BASE_DIR+ 'res/js/swfupload.js', type:'js', charset:'utf-8'},

        //uploadify
        'uploadify-css' 					 	: {path : RES_BASE_DIR+ 'res/css/uploadify.css', type:'css', charset:'utf-8'},
        'uploadify' 							: {path : RES_BASE_DIR+ 'res/js/jquery.uploadify.min.js', type:'js', charset:'utf-8', rely : ['uploadify-css']},
		
		//game_model
         'game_model' 						   : {path : RES_BASE_DIR+ 'res/js/game_model.js', type:'js', charset:'utf-8'},
		

        //admin
        'admin'                                : {path : RES_BASE_DIR + 'res/js/admin.js', type : 'js', charset : 'utf-8', rely : ['art_dialog-style-blue', 'ajax-file-upload', 'game_model']}
    }
});


