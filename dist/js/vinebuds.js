var budsvine = {
	v: '1',
	imgerror: function(img){
		var _replace = img.dataset.replace;
		var _text = img.dataset.text;
		if(_replace){
			console.log('replace');
			img.dataset.replace='';
			img.src=_replace;
		}
		else if(_text){
			console.log('text');
			var image = budsvine.text2img(_text);
			img.src=image;
		}
	},
	text2img: function(text, config){
		var img = {
			fontsize: 50,
			fontcolor: "#FFFFFF",
			transparent:false,
			backcolor: "#0099FF"
		};
		var canvas = document.createElement('canvas');
		var margin = img.fontsize/2;
		canvas.height = img.fontsize+margin*2;
		var context = canvas.getContext('2d');
		context.fillStyle = img.fontcolor;
		context.font=img.fontsize+"px Arial";
		//top（顶部对齐） hanging（悬挂） middle（中间对齐） bottom（底部对齐） alphabetic是默认值
		context.textBaseline = 'top';
		context.fillText(text,margin,margin);
		
		//改变大小后，重新设置一次文字
		canvas.width = context.measureText(text).width + margin*2;
		if(img.transparent){
			// 擦除(0,0)位置大小为200x200的矩形，擦除的意思是把该区域变为透明
			context.clearRect(0, 0, canvas.width, canvas.height);
		}
		else{
			context.fillStyle = img.backcolor;
			context.fillRect(0,0, canvas.width, canvas.height);
		}
		
		context.fillStyle = img.fontcolor;
		context.font=img.fontsize+"px Arial";
		context.textBaseline = 'top';
		context.fillText(text,margin,margin);
		var dataUrl = canvas.toDataURL('image/png');//注意这里背景透明的话，需要使用png
		return dataUrl;
	}
};
