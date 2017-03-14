//表单
Cute.form = {	//表单
    bindDefault: function(obj) {
        if(!("placeholder" in document.createElement("input"))){
            $(obj || "input[placeholder],textarea[placeholder]").on('focus.bindDefault', function() {
                if (this.value == this.getAttribute("placeholder")) {
                    $(this).removeClass("default");
                    this.value = '';
                }
                if ($(this).attr("ntype") == "password"){
					this.type = "text";
					//隐藏错误提示
                    var err_obj = $(this).next('.Validform_checktip');
                    err_obj.length > 0 && err_obj.hide();
				}
            }).on('blur.bindDefault',function() {
                if (this.value == '') {
                    $(this).addClass("default");
                    var err_obj = $(this).next('.Validform_checktip');
                    err_obj.length > 0 && err_obj.show();
                    this.value = this.getAttribute("placeholder");
                }
            }).val(function(){
                if(this.defaultValue != '') return this.defaultValue;
				if(this.type == "password") this.setAttribute("placeholder","••••••");
				$(this).addClass("default");
                this.defaultValue = this.getAttribute("placeholder");
                return this.getAttribute("placeholder");
            });
            $('form').on('submit',function(){
                $('input:text,textarea,input:password').each(function(){
                    if(this.value == this.getAttribute("placeholder")){
                        this.value = '';
                    }
                });
            });
        }
    },
    bindFocus: function(obj) {
        if(!("placeholder" in document.createElement("input"))){
            $(document.body).on("focus.bindFocus", obj || "input.text,textarea.textarea", function() {
                $(this).addClass("focus");
            }).on("blur.bindFocus", obj || "input.text,textarea.textarea", function() {
                $(this).removeClass("focus");
            });
        }
    },
    isInputNull: function(obj) {
        obj = $(obj);
        if (obj.length == 0) return false;
        var _value = obj.val().trim();
        if (_value == "" || (_value == obj[0].defaultValue && obj.hasClass("default"))) {
            return true;
        }
        return false;
    }
};