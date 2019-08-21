    //图片上传
    $.fn.basePic = function (set, callback) {
        if ($(this).attr("type") != 'file') {
            alert('实例：<input id="abc" type="file">');
            return false;
        }
        var defaults = {maxWidth: 198, maxHeight: 198, size: 2}; //设置最大高度，宽度，大小
        var opts = $.extend({}, defaults, set);
        $(this).change(function () {
            var input_file = this;
            if (typeof (FileReader) === 'undefined') {
                if (typeof callback == 'function') callback({status: false, tip: "浏览器不支持", src: ''});
                return false;
            } else {
                try {
                    var file = input_file.files[0];
                    var size = file.size;
                    if (!/image\/\w+/.test(file.type)) {
                        if (typeof callback == 'function') callback({
                            status: false,
                            tip: "请确保文件为图像类型为gif|jpg|jpeg|png",
                            src: ''
                        });
                        return false;
                    }
                    var reader = new FileReader();
                    reader.onload = function (oFREvent) {
                        var src = oFREvent.target.result;
                        var image = new Image();
                        image.onload = function () {
                            var width = image.width;
                            var height = image.height;
                            if (width <= opts.maxWidth && height <= opts.maxHeight && size <= opts.size * 1024 * 1024) {  //正确处理
                                var status = true, tip = "图片符合要求", base_src = src;
                            } else {
                                var tip = "图片不符合要求:小于" + opts.maxWidth + "*" + opts.maxHeight + ",不超过" + opts.size + "M";  //错误提示
                                var status = false, base_src = '';
                            }
                            if (typeof callback == 'function') callback({status: status, tip: tip, src: base_src});
                        };
                        image.src = src;
                    };
                    reader.readAsDataURL(file);
                } catch (e) {
                    console.log(e.toString);
                }
            }
        });
    };