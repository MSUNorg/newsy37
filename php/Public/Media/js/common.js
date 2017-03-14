$.extend({
	sendAjax: function(option){
        var ajaxOption = option;
        var regUrl= /(\w+):\/\/([^\/:]+)(:\d*)?([^#]*)/;
        var bridge = '/web/bridge.htm';
        var iframeId = '';
        if(option.url.match(regUrl)){
            if(typeof option.bridge != 'undefined'){bridge = option.bridge}
            bridge = RegExp.$1+'://'+RegExp.$2+RegExp.$3+bridge;
            iframeId = (RegExp.$2+RegExp.$3).replace(/\./g, "_").replace(/:/g, "_");
            
            createBridge();
        }else{
            alert('参数 url 不合法');
        }

        function createBridge() {
            if($('#'+iframeId).length == 0){
                var iframe = document.createElement("iframe");
                iframe.id = iframeId;
                iframe.src = bridge;
                iframe.style.display = 'none';
                document.body.appendChild(iframe);
                $(iframe).load(function(){
                    try{
                        var iframeWindow = this.contentWindow || this.contentDocument.parentWindow;
                        iframeWindow.document;
                    }catch(e){
                        alert(bridge+'不存在 或 domain无法识别');
                        $(this).attr('loaded', 'false');
                        return;
                    }
                    $(this).attr('loaded', 'loaded');
                    iframeSendAjax()
                });
            }else{
                iframeSendAjax()
            }
        }

        function iframeSendAjax(){
            if($('#'+iframeId).attr('loaded') == 'loaded'){
                var iframeWindow = document.getElementById(iframeId).contentWindow || docuemnt.getElementById(bridge).contentDocument.parentWindow;
                iframeWindow.$.ajax(ajaxOption);
            }else if($('#'+iframeId).attr('loaded') != 'false'){
                setTimeout(iframeSendAjax, 50);
            }
        }
    }
})