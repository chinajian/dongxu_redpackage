var size = [
    70, //mini_img
    300, //thumb_img
    750 //big_img
];//此尺寸是上传图片的3中规格，与后台的配置参数要严格一直，否则将取不到图片

if(typeof(editorObj) != 'undefined'){
    UE.registerUI('插入图片',function(editor, uiName){
        //注册按钮执行时的command命令，使用命令默认就会带有回退操作
        editor.registerCommand(uiName,{
            execCommand:function(){
                // alert('execCommand:' + uiName)
                var args = {
                    'url': '/admin/basic/upload-file.gs',
                    'count': 10,
                    'fn': function(res){
                        var ueditorImgs = '';//接受返回的图片
                        res.each(function(index, element){
                            var imgUrl = $(element).find('img').attr('src');
                            if(imgUrl){
                                /*取出最大的图>>>*/
                                var tmpArr = imgUrl.split('!!');
                                if(tmpArr.length > 1){
                                    tmpArr = tmpArr[1].split('_');
                                    if(tmpArr[0] >= size.length){//tmpArr[0] 图片有几种尺寸
                                        var reg = new RegExp(size[0]+'x'+size[0]);
                                        imgUrl = imgUrl.replace(reg, size[tmpArr[0]-1]+'x'+size[tmpArr[0]-1]);
                                    }else{
                                        var reg = new RegExp('!!'+tmpArr[0]+'_'+size[0]+'x'+size[0]);
                                        imgUrl = imgUrl.replace(reg, '');
                                    }
                                }
                                /*取出最大的图<<<*/
                                console.log(imgUrl);
                                ueditorImgs += '<img src="' + imgUrl + '">';
                            }
                        });

                        UE.getEditor(editorObj).execCommand('insertHtml', ueditorImgs);
                    }
                }
                openUploadLayer(args);
            }
        });

        //创建一个button
        var btn = new UE.ui.Button({
            //按钮的名字
            name:uiName,
            //提示
            title:uiName,
            //需要添加的额外样式，指定icon图标，这里默认使用一个重复的icon
            cssRules :'background-position: -726px 403px;',
            //点击时执行的命令
            onclick:function () {
                //这里可以不用执行命令,做你自己的操作也可
               editor.execCommand(uiName);
            }
        });

        //当点到编辑内容上时，按钮要做的状态反射
        editor.addListener('selectionchange', function () {
            var state = editor.queryCommandState(uiName);
            if (state == -1) {
                btn.setDisabled(true);
                btn.setChecked(false);
            } else {
                btn.setDisabled(false);
                btn.setChecked(state);
            }
        });

        //因为你是添加button,所以需要返回这个button
        return btn;
    }, 54/*index 指定添加到工具栏上的那个位置，默认时追加到最后,editorId 指定这个UI是那个编辑器实例上的，默认是页面上所有的编辑器都会添加这个按钮*/
    );
}