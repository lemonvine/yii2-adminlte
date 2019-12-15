/**
 * 
 */
var bolevine = {
	v: '1',
	referer: '',
	opening: null,
	alert: function(options){
		layer.msg(MESSAGE, {time: 1000});
	},
	forward: function(url){
		if(url){
			location.href=url;
		}
		else{
			location.href=bolevine.referer;
		}
	},
	back2begin: function(msg, url){
		if(msg){
			if(bolevine.opening){
				layer.close(bolevine.opening);
			}
			layer.msg(msg, {time: 1000}, function(){
				bolevine.forward(url);
			});
		}else{
			bolevine.forward(url);
		}
	},
	sy: function(){
		layer.alert('ssssss');
	}
}