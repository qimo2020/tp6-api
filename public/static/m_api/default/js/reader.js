layui.use(['element','layer'], function() {
    var $ = layui.jquery, element = layui.element, layer = layui.layer;
    loaderShow();
    localforage.clear();
    var defid = 0, identPrev = 'api';
    $(function () {
        sidebarsInit();
        $(document).on('click', 'li i', function () {
            if ($(this).parent().hasClass('open')) {
                $(this).parent().removeClass('open');
                $(this).removeClass('down').addClass('right');
            } else {
                $(this).parent().addClass('open');
                $(this).removeClass('right').addClass('down');
            }
        });
        var sildBtn = !isMobile() ? '.tools' : '.wholerow,.tools';
        $(document).on('click', sildBtn, function () {
            var wb = $('.window-body');
            if (wb.hasClass('with-sidebar')) {
                $('.window-body').removeClass('with-sidebar');
            } else {
                $('.window-body').addClass('with-sidebar');
            }
        });
        $(document).on('click', '.wholerow', function (e) {
            let that = $(this).parent().children('.text');
            let iid = defid = that.attr('href');
            if (!iid) {
                return false;
            }
            localforage.ready(function () {
                try {
                    localforage.setItem(identPrev + '_defid', defid);
                } catch (e) {
                    console.log('可能超出大小限制');
                    localforage.clear();
                    localforage.setItem(identPrev + '_defid', defid);
                }
            })
            that.parent().addClass('active');
            $('li').not(that.parent()).removeClass('active');
            detial(iid, function (res) {
                if (window.history && window.history.pushState && window.history.replaceState) {
                    window.history.pushState({status: 0}, '', iid);
                }
                if (res.content) {
                    contentRender(res.content);
                }
                $('.article-body').closest('.article').find('h1').text(that.text());
                $('.article-body').removeClass('loadding');
                loaderHide();
            });
        });
        $(document).on('click', '#test-btn,#sign-btn,#token-btn', function (e) {
            let that = $(this), _form = $('.test-form'), data = _form.serializeArray(), publics = _form.attr('public').split(','),headers={"Content-Type": "application/x-www-form-urlencoded", "Authorizationr":"Authorizationr"},post=[], uri = '';
            $.each(data, function (i,v) {
                if(v.name == 'timestamp'){
                    data[i]['value'] = v.value = timeFormat(v.value);
                }
                if(publics.length > 0 && $.inArray(v.name, publics) >= 0){
                    headers[v.name] = v.value;
                }else{
                    if(!(that.attr('id') == 'test-btn' && v.name == 'app_id')){
                        post[i] = {};
                        post[i]['name'] = v.name, post[i]['value'] = v.value;
                    }
                }
            })
            if(uuid != ''){
                headers.uuid = uuid;
            }
            let idBtn = that.attr('id');
            switch (idBtn) {
                case 'test-btn':
                    uri = _form.attr('action');
                    break;
                case 'sign-btn':
                    uri = '/api/info/getsign';
                    break;
                case 'token-btn':
                    uri = '/api/info/gettoken';
                    break;
                default:
                    uri = '';
            }
            $.ajax({
                type: _form.attr('method'),
                url: uri,
                headers:headers,
                data: post.filter(function(item){return item}),
                success: function(res) {
                    if(res.code == 0){
                        layer.msg(res.msg);
                        return false;
                    }
                    if(that.attr('id') == 'test-btn'){
                        layer.msg(res.msg);
                    }else{
                        if(res.sign){
                            $('input[name="sign"]').val(res.sign);
                        }else if(res.token){
                            $('input[name="token"]').val(res.token);
                        }
                    }
                }
            });
            return false;
        })

    })

    function sidebarsInit() {
        let params = {'app_id': appid};
        try {
            localforage.ready(function () {
                localforage.getItem(identPrev + '_defid', function (err, res) {
                    if (res !== null) {
                        params.iid = res;
                    }
                    sidebars(params);
                })
            })
        } catch (e) {
            console.log('可能超出大小限制');
            localforage.clear();
            sidebarsInit();
        }
    }

    function sidebars(params) {
        localforage.getItem(identPrev + '_sidebars').then(function (res) {
            if (res !== null) {
                if (typeof version !== undefined && res.version && res.version == version) {
                    let curiid = params ? params.iid : res.iid;
                    sideResp(res.data, curiid);
                    return false;
                }else{
                    localforage.clear();
                }
            }
            http("/api/info", params).then(function (result) {
                let setObj = {'data': result.data, 'iid': result.iid, 'version': result.version};
                sideResp(result.data, result.iid);
                localforage.setItem(identPrev + '_sidebars', setObj);
            }).catch(function (result) {
                let msg = '服务端异常';
                if (result.msg) msg = result.msg;
            })
        }).catch(function (err) {
            console.log('可能超出大小限制');
            localforage.clear();
            sidebars(params);
        });
    }

    function sideResp(data, respid) {
        sildbars = tree(data, respid);
        if ($('.catalog-body').html(sildbars)) {
            $('.active').parents('li').addClass('open');
            $('.active').parents('ul').siblings('i').addClass('down');
        }
        let host = window.location.href;
        let index = host.lastIndexOf("\/"), prevUrl = host.substring(0, index + 1), currid = respid,
            hostid = host.substring(index + 1, host.length);
        if (hostid && !isNaN(Number(hostid))) {
            currid = hostid;
        }
        try {
            if (window.history && window.history.pushState && window.history.replaceState) {
                window.history.pushState({status: 0}, '', currid);
                detial(currid, function (res) {
                    if (res.code && res.code == 1 && res.iid && res.iid != currid) {
                        window.history.pushState({status: 0}, '', res.iid);
                    } else {
                        if (hostid != respid) {
                            window.location.href = prevUrl + respid;
                        }
                    }
                    if (res.content) {
                        contentRender(res.content);
                    }
                    let title = $('.catalog-body').find('.active').children('.text').text();
                    $('.article-body').closest('.article').find('h1').text(title);
                    setTimeout(function () {
                        loaderHide();
                    }, 200);
                })
            } else if (hostid != respid) {
                window.location.href = prevUrl + respid;
            }
        } catch (e) {
        }
    }

    function detial(iid, callback) {
        var indetifier = identPrev + '_' + iid;
        localforage.ready(function () {
            try {
                let time = 900;
                localforage.keys().then(function (keys) {
                    for (let i = 0; i < keys.length; i++) {
                        localforage.getItem(keys[i], function (err, obj) {
                            if (obj && obj.expire && obj.expire < new Date().getTime()) {
                                localforage.removeItem(keys[i]);
                            }
                        })
                    }
                })
                localforage.getItem(indetifier, function (err, obj) {
                    if (obj !== null && obj.content) {
                        if (typeof obj.expire != "undefined" && typeof obj.content != "undefined" && obj.expire > new Date().getTime()) {
                            //console.log(indetifier + '：有缓存，无过期，则直接返回');
                            return callback(obj);
                        }
                        //console.log(indetifier + '：有缓存，但已过期，清除该条缓存');
                        localforage.removeItem(indetifier);
                    }
                    $('.article-body').addClass('loadding');
                    loaderShow('.article-body');
                    //console.log(indetifier + '请求服务端，并存缓存到本地');
                    let setObj = new Object();
                    setObj.expire = parseInt(new Date().getTime()) + parseInt(time * 1000);
                    request(iid, function (res) {
                        if (res.content) {
                            setObj.content = res.content;
                            localforage.setItem(indetifier, setObj);
                        }
                        return callback(res);
                    })
                });
            } catch (e) {
                console.log('可能超出大小限制');
                localforage.clear();
                return detial(iid);
            }
        })
    }

    function request(iid, callback) {
        data = {'iid': iid}
        http("/api/info/detail", data).then(function (res) {
            if (res && res.content) {
                callback(res);
            } else {
                callback('服务端错误');
            }
        }).catch(function (res) {
            let msg = '服务端异常';
            if (res.msg) msg = res.msg;
            callback(msg);
        })
    }

    function tree(data, curid) {
        let sildbarHtml = '';
        if (data) {
            sildbarHtml = '<ul>';
            $.each(data, function (k, v) {
                if (v && v.title) {
                    sildbarHtml += '<li';
                    if (v.link && v.link == curid) {
                        sildbarHtml += ' class="active"';
                    }
                    sildbarHtml += '><div class="wholerow"></div>';
                    sildbarHtml += '<i class="icon';
                    if (v.child) {
                        sildbarHtml += ' iconfont iconjiantou caret'
                    }
                    sildbarHtml += '"></i>';
                    sildbarHtml += '<a class="text"';
                    if (v.link) {
                        sildbarHtml += ' href="' + v.link + '"';
                    }
                    sildbarHtml += '>' + v.title + '</a>';
                    if (v.child) {
                        sildbarHtml += tree(v.child, curid);
                    }
                    sildbarHtml += '</li>';
                }
            })
            sildbarHtml += '</ul>';
        }
        return sildbarHtml;
    }

    function contentRender(content) {
        let articleContent = $('.article-body').children('.content'), html = '', paramHtml = '';
        articleContent.html('');
        html += '<div class="layui-tab-item layui-show"><blockquote class="layui-elem-quote"><strong>接口地址: </strong><span class="title-tab address"> {域名}/' + content.action.url + '</span></blockquote></div>';
        html += '<div class="layui-tab-item layui-show"><blockquote class="layui-elem-quote"><strong>请求方式：</strong><span class="title-tab layui-btn-xs layui-btn-danger">' + content.action.method + '</span></blockquote></div>';
        if (content.action.public) {
            paramTitle = '公共参数';
            html += tableBox(content.action.public, paramTitle);
        }
        if (content.action.param) {
            paramTitle = '请求参数';
            html += tableBox(content.action.param, paramTitle);
        }
        if (content.action.return) {
            paramTitle = '返回参数';
            html += tableBox(content.action.return, paramTitle);
        }
        if (content.action.test == 1) {
            html += '<div class="layui-collapse"><div class="layui-colla-item"><h2 class="layui-colla-title"><strong>接口测试工具</strong></h2><div class="layui-colla-content layui-show">';
            let httpUri = document.location.protocol + '//' + window.location.host + '/' + content.action.url, pubParams=[];
            if (content.action.public) {
                $.each(content.action.public, function (key, val) {
                    pubParams.push(val.name);
                })
            }
            html += '<form class="layui-form test-form" action="'+ httpUri +'" method="'+content.action.method.toLowerCase()+'" public="'+pubParams+'" onsubmit="return false;">';
            if (content.action.public) {
                html += '<fieldset class="layui-elem-field"><legend><a name="before">请求头信息 (Header)</a></legend><div class="layui-field-box">';
                $.each(content.action.public, function (key, val) {
                    let value = val.default != '' ? val.default : '';
                    html += '<div class="layui-form-item">';
                    html += '<label class="layui-form-label">'+val.name+'</label>';
                    html += '<div class="layui-input-inline">';
                    if(val.name == 'timestamp'){
                        value = dateFormat("YYYY-mm-dd HH:MM:SS", new Date());
                    }else if(val.name == 'rand'){
                        value = randomWord(false, 8);
                    }
                    html += '<input type="text" name="'+val.name+'" value="'+value+'"';
                    if(val.need == 1){
                        html += ' required lay-verify="required"';
                    }
                    html += 'placeholder="" autocomplete="off" class="layui-input"></div>';
                    if(val.name == 'sign' || val.name == 'token'){
                        let btnType = val.name == 'sign' ? 'sign':'token',btnTxt = val.name == 'sign' ? '接口签名':'用户令牌';
                        html += '<button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="'+btnType+'-btn">获取'+btnTxt+'</button>';
                    }
                    html += '</div>';
                });
                html += '</div></fieldset>';
            }
            if (content.action.param) {
                html += '<fieldset class="layui-elem-field"><legend><a name="before">请求参数</a></legend><div class="layui-field-box">';
                $.each(content.action.param, function (key, val) {
                    html += '<div class="layui-form-item">';
                    html += '<label class="layui-form-label">'+val.name+'</label>';
                    html += '<div class="layui-input-inline">';
                    html += '<input type="text" name="'+val.name+'"';
                    if(val.need == 1){
                        html += ' required lay-verify="required"';
                    }
                    html += 'placeholder="" autocomplete="off" class="layui-input"></div>';
                    if(val.need == 1){
                        html += '<div class="layui-form-mid layui-word-aux">(必填)</div>';
                    }
                    html += '</div>';
                });
                html += '</div></fieldset>';
            }
            html += '<input type="hidden" name="app_id" value="'+appid+'"/>';
            html += '<div class="layui-input-block" style="margin-left:0">';
            html += '<button class="layui-btn" id="test-btn" lay-submit lay-filter="test-btn">提交测试</button>';
            html += '</div>';
            html += '</div></form></div></div></div><div class="layui-form-item">';
        }
        articleContent.html(html);
        element.init();

    }

    function tableBox(data, title) {
        let paramHtml = '';
        paramHtml = '<div class="layui-collapse"><div class="layui-colla-item"><h2 class="layui-colla-title"><strong>'+title+'</strong></h2><div class="layui-colla-content layui-show">';
        paramHtml += '<table class="layui-table"><thead><tr><th>参数名</th><th>类型</th><th>必须</th><th>默认值</th><th>描述</th></tr></thead><tbody>';
        $.each(data, function (key, value) {
            paramHtml += '<tr><td>' + value.name + '</td><td>' + value.type + '</td><td>' + (value.need == 0 ? "否":"是") + '</td><td>' + value.default + '</td><td>' + value.desc + '</td></tr>';
        });
        paramHtml += '</tbody></table></div></div></div>';
        return paramHtml;
    }

    function http(url, data = {}, type = 'post') {
        return new Promise(function (resolve, reject) {
            $.ajax({
                url: url,
                dataType: "json",
                type: type,
                data: data,
                success: function (res) {
                    if (!res) reject({'code': 0, 'msg': '服务端数据错误'});
                    resolve(res);
                },
                error: function () {
                    reject({'code': 0, 'msg': '服务端链接错误'});
                }
            });
        })
    }

    function loaderShow(node = 'body') {
        if (!$('#hi-shadebox').length) {
            $(node).append('<div id="hi-shadebox"><div class="loader-round"></div></div>');
        }
    }

    function loaderHide() {
        if ($('#hi-shadebox').length) {
            $('#hi-shadebox').remove();
        }
    }

    function isMobile() {
        if (window.navigator.userAgent.match(/(phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i)) {
            return true;
        } else {
            return false;
        }
    }

    function dateFormat(fmt, date) {
        let ret;
        const opt = {
            "Y+": date.getFullYear().toString(),        // 年
            "m+": (date.getMonth() + 1).toString(),     // 月
            "d+": date.getDate().toString(),            // 日
            "H+": date.getHours().toString(),           // 时
            "M+": date.getMinutes().toString(),         // 分
            "S+": date.getSeconds().toString()          // 秒
            // 有其他格式化字符需求可以继续添加，必须转化成字符串
        };
        for (let k in opt) {
            ret = new RegExp("(" + k + ")").exec(fmt);
            if (ret) {
                fmt = fmt.replace(ret[1], (ret[1].length == 1) ? (opt[k]) : (opt[k].padStart(ret[1].length, "0")))
            };
        };
        return fmt;
    }
    function timeFormat(format) {
        format = format.substring(0,19);
        format = format.replace(/-/g,'/');
        return new Date(format).getTime()/1000;
    }

    //flag-是否任意长度 min-任意长度最小位[固定位数] max-任意长度最大位
    function randomWord(flag, min, max){
        let str = "";
            range = min,
            arr = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        // 随机产生
        if(flag){
            range = Math.round(Math.random() * (max-min)) + min;
        }
        for(var i=0; i<range; i++){
            pos = Math.round(Math.random() * (arr.length-1));
            str += arr[pos];
        }
        return str;
    }

})