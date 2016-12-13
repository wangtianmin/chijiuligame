<?php
	require_once "jssdk.php";
	$jssdk = new JSSDK("wx319984f85f22ac64", "c0da4caf6cad8a701cdc29b3f86993fe");
	$signPackage = $jssdk->GetSignPackage();
?>
<?php
	require_once "common.php";
	$code = $_GET["code"];
	$appid = "wx319984f85f22ac64";
	$appsecret = "c0da4caf6cad8a701cdc29b3f86993fe";
	
	$getTokenApi = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$appsecret}&code={$code}&grant_type=authorization_code";
	
	$str = httpGet($getTokenApi);
	$arr = json_decode($str,true);
	$openid = $arr["openid"];
	$token = $arr["access_token"];
	
	$userinfoApi = "https://api.weixin.qq.com/sns/userinfo?access_token={$token}&openid={$openid}&lang=zh_CN";
	$str = httpGet($userinfoApi);
	$arr=json_decode($str,true);
	$nickname = $arr["nickname"];
	$headimgurl = $arr["headimgurl"];
	
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
	<title>持久力大挑战</title>
	<style type="text/css">
		* {
			margin: 0;
			padding: 0;
		}
		
		body,
		html {
			height: 100%;
		}
		
		#cover {
			width: 100%;
			height: 100%;
			background: url("img 4/bg.png") no-repeat;
			background-size: 100% 100%;
		}
		
		img {
			position: absolute;
		}
		#game{
			display: none;
			height: 100%;
			background: url("img 4/game_bg.png") 0 0 no-repeat;
			background-size: 100% 100%;
		}
		#time{
			width: 35.9375%;
		    height: 5.55066%;
		    background: url('img 4/time.png') no-repeat;
		    position: absolute;
		    left: 30.15625%;
		    top: 2.64317%;
		    background-size: 100% 100%;
		    font-size: 16px;
		    text-align: center;
		    font-weight: bold;
		    line-height: 30px;
		}
		#mask{
			position: absolute;
			width: 100%;
			height: 100%;
			background-color: black;
			opacity: 0.5;
		}
		#explain img,
		#close img{
			width: 100%;
		}
		#explain{
			width: 80.15625%;
		    position: absolute;
		    left: 9.921875%;
		    top: 17.70925%;
		}
		#close{
			width: 9.0625%;
		    position: absolute;
		    right: 7.03125%;
		    top: 16.65198%;
		}
		
		#gameover{
			width: 100%;
		    height: 100%;
		    position: absolute;
		    left: 0;
		    top: 0;
		    background: url('img 4/end_bg.png') no-repeat;
		    background-size: 100% 100%;
		    display: none;
		}
		#grade{
			position: absolute;
		    left: 10.3125%;
		    top: 39.912894%;
		    width: 79.6875%;
		    height: 10.11013%;
		    background: url("img 4/time_bg.png") no-repeat;
		    background-size: 100% 100%;
		}
		#source{
			position: absolute;
		    top: 0;
		    left: 48.09375%;
		    font-size: 33px;
		    color: #e5fa34;
		}
		#rank{
			position: absolute;
		    left: 79.78125%;
		    top: 75.13656%;
		    color: white;
		    font-size: 19px;
		}
		#fenxiang{
			width: 68.125%;
		    position: absolute;
		    left: 15.9375%;
		    top: 76.29955%;
		}
		#fenxiang img{
			width: 100%;
		}
		#again{
			width: 68.125%;
		    position: absolute;
		    left: 15.9375%;
		    top: 84.40528%;
		}
		#again img{
			width: 100%;
		}
		#share{
			width: 100%;
		    height: 100%;
		    position: absolute;
		    left: 0;
		    top: 0;
		    display: none;
		}
		#share img{
			width: 100%;
		}
		#ranking{
			width: 68.125%;
			height: 6.3%;
		    position: absolute;
		    left: 15.9375%;
		    top: 92.40528%;
		    background-color: red;
			text-align: center;
			font-size: 1.5em;
		}
	</style>
</head>
<body>
	<div id="cover">
		<img src="img 4/car.png" id="car"/>
		<img src="img 4/dongli.png" id="dongli"/>
		<img src="img 4/wenzi1.png" id="wenzi1"/>
		<img src="img 4/wenzi2.png" id="wenzi2"/>
		<a href="###" id="abtn"><img src="img 4/begin.png"/></a>
	</div>
	<div id="game">
		<div id="time">
			<span>0</span>
			<strong>分钟</strong>
		</div>
		<div id="mask">
			
		</div>
		<div id="explain">
			<img src="img 4/state.png"/>
		</div>
		<div id="close">
			<img src="img 4/close.png"/>
		</div>
		<canvas id="canvas" width="" height=""></canvas>
	</div>
	<div id="gameover">
		<div id="grade">
			<div id="source"></div>
			<div id="rank"></div>
		</div>
		<div id="fenxiang">
			<img src="img 4/fenxiang.png"/>
		</div>
		<div id="again">
			<img src="img 4/agin.png"/>
		</div>
		<div id="share">
			<img src="img 4/share.png"/>
		</div>
		<div id="ranking">上传成绩并查看排行榜</div>
	</div>
</body>
<script src="js/jquery-3.1.0.min.js"></script>
<script type="text/javascript">//获取屏幕的分辨率
	var screenW = $(document.body).width();
	var screenH = $(document.body).height();
	var gameStartBol = false;//判断游戏开始
	var gameOverBol = false;//判断游戏结束
	var num = 0;//时间
	function rand(min,max){
		return parseInt(Math.random()*(max-min+1)+min);
	}
	
	$(function() {
		/*************************************** 首页页面 *************************************************/
		var cover = $("#cover");
		var game = $("#game");
		var car = $("#car");
		var dongli = $("#dongli");
		var wenzi1 = $("#wenzi1");
		var wenzi2 = $("#wenzi2");
		var abtn = $("#abtn");
	
		//汽车
		car.width(424 * screenW / 640);
		var carW = car.width();
		var carH = car.height();
		car.css({
			"left": -carW + "px",
			"top":"0px"
		});
		car.animate({"left":0.078125*screenW+"px","top":0.14437*screenH+"px"});
		
		//动力
		dongli.width(274 * screenW / 640);
		dongli.css({"left":0.5*screenW+"px","top":0.37852*screenH+"px","opacity":0})
		dongli.animate({"opacity":1});
		
		//文字1
		wenzi1.width(206 * screenW / 640);
		wenzi1.css({"left":"0px","top":0.45158*screenH+"px","opacity":0});
		wenzi1.animate({"left":0.125*screenW+"px","opacity":1});
		
		//文字2
		wenzi2.width(319 * screenW / 640);
		wenzi2.css({"left":0.1*screenW+"px","opacity":0});
		wenzi2.animate({"left":0.3615625*screenW+"px","top":0.51937*screenH+"px","opacity":1});
		
		//开始游戏按钮
		abtn.find("img").width(436 * screenW / 640);
		abtn.find("img").css({"left":0.159375*screenW+"px","bottom":0.04754*screenH+"px"})
		abtn.on("click",function(){
			cover.hide();
			game.show();
			box1.draw();
			box2.draw();
			box3.draw();
			box4.draw();
			carObj.draw();
		})
		
		/*************************************** 游戏界面的说明 *************************************************/
		
		var close = $("#close");
		var explain = $("#explain");
		var mask = $("#mask");
		var time = $('#time span');
		close.on("click",function(){
			mask.hide();
			explain.hide();
			close.hide();
		})
		
		
	})
	/*************************************** 游戏界面的canvas *************************************************/
	var canvas = document.getElementById("canvas");
	var cxt = canvas.getContext("2d");
	canvas.width = screenW;
	canvas.height = screenH;
	
	/*************************************** 画障碍 *************************************************/
	var suduarr = [1,-1];
	function Box(x,y,w,h,sx,sy){
		this.x=x;
		this.y=y;
		this.w=w;
		this.h=h;
		this.sx = sx;
		this.sy = sy;
	}
	Box.prototype.draw = function(){
		cxt.beginPath();
		cxt.fillStyle = "#bcdf44";	
		cxt.fillRect(this.x,this.y,this.w,this.h);
	}
	Box.prototype.move = function(){
		this.x+=this.sx;
		this.y+=this.sy;
		if(this.x<0 || this.x>canvas.width-this.w){
			this.sx*=-1;
		}else if(this.y<0 || this.y>canvas.height-this.h){
			this.sy*=-1;
		}	
	}
	/*************************************** 画汽车 *************************************************/
	var carImg = new Image();
	carImg.src = "img 4/car2.png";
	var carObj = {
		x:0.36*canvas.width,
		y:0.40*canvas.height,
		w:0.16*canvas.width,
		h:0.16*canvas.height,
		draw:function(){
			cxt.drawImage(carImg,this.x,this.y,this.w,this.h);
		}
	}
		
	var box1 = new Box(0.064*canvas.width,0.400*canvas.height,0.12*canvas.width,0.2871*canvas.height,1,-1);
	var box2 = new Box(0.28*canvas.width,0.608*canvas.height,0.48*canvas.width,0.145*canvas.height,-1,-1);
	var box3 = new Box(0.752*canvas.width,0.405*canvas.height,0.208*canvas.width,0.166*canvas.height,-1,-1);
	var box4 = new Box(0.56*canvas.width,0.215*canvas.height,0.187*canvas.width,0.089*canvas.height,1,1);
	
	var framNum = 0;
	function main(){
		box1.draw();
		box2.draw();
		box3.draw();
		box4.draw();
		if(gameStartBol){
			framNum++;
			if(framNum%30==0){
				num++;
				time.innerHTML = num;
			}
			box1.move();
			box2.move();
			box3.move();
			box4.move();
		}
		carObj.draw();
		
		//碰撞
		if(crash(carObj,box1) || crash(carObj,box2) || crash(carObj,box3) || crash(carObj,box4)){
			isDown = false;
			gameOverBol = true;
		}
	}
	
	var game = document.getElementById("game");
	var gameOver = document.getElementById("gameover");
	var source = document.getElementById("source");
	var rank = document.getElementById("rank");
	var score;
	function animate() {	
		if(gameOverBol == false){
			cxt.clearRect(0, 0, canvas.width, canvas.height);
			main();
			animateId = window.requestAnimationFrame(animate);
		}else{
			cancelAnimationFrame(animateId);
			/*************************************** 游戏结束界面 *************************************************/
			game.style.display = "none";
			source.innerHTML = time.innerHTML;
			score = source.innerHTML;
			gameOver.style.display = "block";
			
		}
	}
	
	/*************************************** 点击分享 *************************************************/
	var share = document.getElementById("share");
	var fenxiang = document.getElementById("fenxiang");
	fenxiang.onclick = function(){
		share.style.display = "block";
	}
	/*************************************** 点击再玩一次 *************************************************/
	var again = document.getElementById("again");
	again.onclick = function(){
		game.style.display = "block";
		gameOver.style.display = "none";
		cxt.clearRect(0, 0, canvas.width, canvas.height);
		box1 = new Box(0.064*canvas.width,0.400*canvas.height,0.12*canvas.width,0.2871*canvas.height,1,-1);
		box2 = new Box(0.28*canvas.width,0.608*canvas.height,0.48*canvas.width,0.145*canvas.height,-1,-1);
		box3 = new Box(0.752*canvas.width,0.405*canvas.height,0.208*canvas.width,0.166*canvas.height,-1,-1);
		box4 = new Box(0.56*canvas.width,0.215*canvas.height,0.187*canvas.width,0.089*canvas.height,1,1);
		box1.draw();
		box2.draw();
		box3.draw();
		box4.draw();
		carObj.x=0.36*canvas.width,
		carObj.y=0.40*canvas.height,
		carObj.draw();
		time.innerHTML = 0+"分钟";
		num=0;
		gameOverBol = false;
	}
	
	/*************************************** 拖拽汽车 *************************************************/
	//拖拽汽车
	var isDown = false;
	canvas.addEventListener("touchstart",function(e){
		var e = e || window.event;
		var first = e.touches[0];
		var x = first.clientX;
		var y = first.clientY;
		animate();
		if(x>carObj.x && x<carObj.x+carObj.w && y>carObj.y && y<carObj.y+carObj.h){
			isDown = true;
			gameStartBol =true;
		}

		canvas.addEventListener("touchmove",function(e){
			if(!isDown){
				return;
			}
			var e = e || window.event;
			var first = e.touches[0];
			var x = first.clientX-carObj.w/2;
			var y = first.clientY-carObj.h/2;
			
			//此时，因为要移动汽车，所以要先清空画布,所以后面要重新绘制汽车
			cxt.clearRect(0,0,canvas.width,canvas.height);
			//把汽车新的起点赋值给它
			if(x<0){
				x=0;
			}else if(x>canvas.width-carObj.w){
				x=canvas.width-carObj.w;
			}else if(y<0){
				y=0;
			}else if(y>canvas.height-carObj.h){
				y=canvas.height-carObj.h;
			}
			carObj.x = x;
			carObj.y = y;
			carObj.draw();
			
			e.preventDefault();
		},false)
		canvas.addEventListener("touchend",function(e){
			isDown = false;
		},false)
	},false)
	
	
	//碰撞函数
	function crash(obj1,obj2){
		//obj1的四条边
		var l1 = obj1.x;
		var t1 = obj1.y;
		var r1 = obj1.x+obj1.w;
		var b1 = obj1.y+obj1.h;
		//obj2的四条边
		var l2 = obj2.x;
		var t2 = obj2.y;
		var r2 = obj2.x+obj2.w;
		var b2 = obj2.y+obj2.h;
		
		if(r1>l2 && b1>t2 && l1<r2 && t1<b2){
			return true;//碰撞返回true
		}else{
			return false;//没碰撞返回false
		}	
	}
</script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
  wx.config({
    //debug: true,
    appId: '<?php echo $signPackage["appId"];?>',
    timestamp: <?php echo $signPackage["timestamp"];?>,
    nonceStr: '<?php echo $signPackage["nonceStr"];?>',
    signature: '<?php echo $signPackage["signature"];?>',
    jsApiList: [
      // 所有要调用的 API 都要加到这个列表中
      "onMenuShareTimeline",
      "onMenuShareAppMessage",
    ]
  });
  wx.ready(function () {
    // 在这里调用 API
    wx.onMenuShareTimeline({
	    title: '小游戏', // 分享标题
	    link: 'http://www.waterhomeall.com/wangtianmin/chijiuligame/send.html', // 分享链接
	    imgUrl: 'http://img4.imgtn.bdimg.com/it/u=2456866554,992463706&fm=23&gp=0.jpg', // 分享图标
	    success: function () { 
	        // 用户确认分享后执行的回调函数
	    },
	    cancel: function () { 
	        // 用户取消分享后执行的回调函数
	    }
	});
	wx.onMenuShareAppMessage({
	    title: '小游戏', // 分享标题
	    desc: '最新排行榜出来了，看看你是否榜上有名', // 分享描述
	    link: 'http://www.waterhomeall.com/wangtianmin/chijiuligame/send.html', // 分享链接
	    imgUrl: 'http://img4.imgtn.bdimg.com/it/u=2456866554,992463706&fm=23&gp=0.jpg', // 分享图标
	    type: '', // 分享类型,music、video或link，不填默认为link
	    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
	    success: function () { 
	        // 用户确认分享后执行的回调函数
	    },
	    cancel: function () { 
	        // 用户取消分享后执行的回调函数
	    }
	});


  });
</script>
<script type="text/javascript">
	var openid = "<?php echo $openid ?>";
	var username = "<?php echo $nickname ?>";
	var headimg = "<?php echo $headimgurl ?>";
</script>
<script src="js/chijiuligame.js"></script>
</html>