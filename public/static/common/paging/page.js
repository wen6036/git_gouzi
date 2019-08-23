//分页  url 分页请求地址    getHTML回掉方法
    $.fn.page = function (url, callback) {
        var self=$(this);
        var page=new Paging();
        var pagesize = 10; //默认10条
        var cur = 1;   //默认第一页
        var totalpage = 0; //总页数
        // var search_key=''; //搜索关键字
        var get_data = {cur: cur, size: pagesize};
        page.init({
            target: self,
            pagesize: 10,
            toolbar: true,
            current: 1,
            pageSizeList: [10, 30, 50, 100],
            callback: function (curr, size, total) {
                cur = curr;
                pagesize = size;
                totalpage = total;
                get_data= $.extend({}, get_data, {cur: cur, size: pagesize});
                self.ajax()
            },
            changePagesize: function (size, curr) {
                cur = curr;
                pagesize = size;
                get_data= $.extend({}, get_data, {cur: cur, size: pagesize});
                self.ajax()
            }
        });
        self.count = function (e) {
            if(e=='' || typeof e=='undefined' ||e==0){
                e=0;
                var totalpage = 0;
            }else{
                var totalpage = Math.ceil(e / pagesize);
            }
            page.render({count: e, pagesize: pagesize, current: get_data.cur});

            self.find(".count-page").html('当前第 ' + get_data.cur + '页,总计：' + totalpage + '页,每页' + pagesize +'条,总计：'+e+"条");
        };
        self.ajax = function (obj) {
            if (typeof obj=='object')get_data= $.extend({}, get_data, {cur:1},obj);
            $.get(url, get_data, function (res) {
                self.count(res.count);   
                callback({data:res,size:pagesize,cur:cur},self)
            }, 'json');
        };
        self.ajax();//默认加载
    };