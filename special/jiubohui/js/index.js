
function sport(obj,json,fn){
	clearInterval(obj.timer);
	obj.timer = setInterval(function(){
		//设置一个开关，控制计时器的结束
		var stop = true; //假设当前对象中的属性都已经到达目标，停止计时器
		for(var attr in json){
			//取当前属性值
			var cur = 0;
			if(attr == "opacity"){
				cur = parseInt(parseFloat(getStyle(obj,attr)) * 100);
			}else{
				cur = parseInt(getStyle(obj,attr));
			}
			//计算速度
			var speed = (json[attr] - cur) / 8; //基数
			speed = speed > 0 ? Math.ceil(speed) : Math.floor(speed);
			//检测停止
			if(cur != json[attr]){
				stop = false;
			}
			if(attr == "opacity"){
				obj.style.filter = "alpha(opacity=" + (cur + speed) + ")";
				obj.style.opacity = (cur + speed) / 100;
			}else{
				obj.style[attr] = cur + speed + "px";
			}
			
		}
		if(stop){
			clearInterval(obj.timer);
			fn && fn();
		}
		
		//console.log( cur + ":" + target + ":" + speed);
	},30);
	
}
//alert(getStyle(oDiv,"top"));
function getStyle(obj,attr){
	return obj.currentStyle ? obj.currentStyle[attr] : getComputedStyle(obj,true)[attr];
}