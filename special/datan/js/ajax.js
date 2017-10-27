/**
 * @name:酱酒制造(h5)
 * @link:t.j9zz.com
 * @version:v1.0.0
 * @update:2017-08-07 16:10:21 星期一
 * @author:liuguanhua-liuguahua.github.io-lghayy@foxmail.com
 */
var oRequest = {
    apiUrl: "//t.j9zz.com/api/h5contact/",
    ajax: function(e) {
        (e = e || {}).data = e.data || {}, e.jsonp ? this.jsonpRun(e) : this.jsonRun(e)
    },
    jsonRun: function(e) {
        e.type = (e.type || "GET").toUpperCase(), !e.async || e.async, e.data = this.formatParams(e.data);
        var t = null;
        (t = window.XMLHttpRequest ? new XMLHttpRequest : new ActiveXObjcet("Microsoft.XMLHTTP")).onreadystatechange = function() {
            if (4 == t.readyState) {
                var n = t.status;
                if (n >= 200 && n < 300) {
                    var a = "",
                        o = t.getResponseHeader("Content-type");
                    a = -1 !== o.indexOf("xml") && t.responseXML || "xml" == e.dataType || "XML" == e.dataType ? t.responseXML : "application/json" === o || "json" == e.dataType || "JSON" == e.dataType ? JSON.parse(t.responseText) : t.responseText, e.success && e.success(a)
                } else e.error && e.error(n)
            }
        }, "GET" == e.type ? (t.open(e.type, e.url + "?" + e.data, e.async), t.send(null)) : (t.open(e.type, e.url, e.async), t.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8"), t.send(e.data))
    },
    formatParams: function(e) {
        var t = [];
        for (var n in e) t.push(encodeURIComponent(n) + "=" + encodeURIComponent(e[n]));
        return t.push("v=" + this.random()), t.join("&")
    },
    random: function() {
        return Math.floor(1e4 * Math.random() + 500)
    },
    jsonpRun: function(e) {
        var t = e.jsonp,
            n = document.getElementsByTagName("head")[0];
        e.data.callback = t;
        var a = this.formatParams(e.data),
            o = document.createElement("script");
        n.appendChild(o), window[t] = function(a) {
            n.removeChild(o), clearTimeout(o.timer), window[t] = null, e.success && e.success(a)
        }, o.src = e.url + "?" + a, e.time && (o.timer = setTimeout(function() {
            window[t] = null, n.removeChild(o), e.error && e.error({
                message: "超时"
            })
        }, time))
    },
    countViews: function(e) {
        var t = this;
        this.ajax({
            url: t.apiUrl + "addviews",
            data: {
                id: 1,
                step: e
            }
        })
    }
};
