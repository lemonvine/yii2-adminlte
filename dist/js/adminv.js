/**
 * 
 */
var bolevine = {
	v: '1',
	referer: '',
	opening: null,
	cookie_name: {save_reappear: "j2u8i3"},
	turnoff: function(){
		if(bolevine.opening){
			layer.close(bolevine.opening);
			bolevine.opening = null;
		}
	},
	alert: function(param){
		var base = {flag: 1, message: 'md', icon: 1, time: 2000, callback:null};
		base = bolevine.merge(base, param);
		switch (base.flag){
			case "4":
				base.icon = 5;
				base.time = 4000;
				layer.alert(base.message,{time: base.time, skin: 'layui-layer-molv', icon: base.icon});
				break;
			case "8":
			case "9":
				base.time = 1000;
			case "1":
				layer.msg(base.message,{time: base.time, skin: 'layui-layer-molv', icon: base.icon}, function(){
					if(base.callback) eval(base.callback);
				});
				break;
		}
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
			bolevine.turnoff();
			layer.msg(msg, {time: 1000}, function(){
				bolevine.forward(url);
			});
		}else{
			bolevine.forward(url);
		}
	},
	reload: function(url){
		url = location.href;
		if(!url.includes('referer_url') && is_chain){
			if(url.includes('?')){
				url = url + "&referer_url="+encodeURI(bolevine.referer);
			}
			else{
				url = url + "?referer_url="+encodeURI(bolevine.referer);
			}
		}
		location.href=url;
	},
	dialogok: function(msg, url, chain){
		var is_chain=false;
		if(chain) is_chain=true;
		if(msg){
			bolevine.turnoff();
			layer.msg(msg, {time: 1000}, function(){
				if(url)
					bolevine.forward(url);
				else 
					bolevine.reload(is_chain);
			});
		}else{
			if(url)
				bolevine.forward(url);
			else 
				bolevine.reload(is_chain);
		}
	},
	dialog:function(param){
		var size = {xs:['420px', '280px'], sm:['600px', '450px'], md: ['800px', '600px'], lg: ['1000px', '750px']}
		var base = {title:'信息', size: 'md', resize:false, max:false, url: '', html:'', callback:null};
		base = bolevine.merge(base, param);
		var config = {type: 1, title: base.title, area:size[base.size], resize: base.resize};
		if(base.max) config.maxmin = true;
		if(base.url) {
			config.type = 2;
			config.content = base.url;
		}
		else{
			config.content = base.html
		}
		if(base.callback){
			config.success = function(layero, index){eval(base.callback+"(layero, index)");};
		}
		bolevine.opening = layer.open(config);
	},
	confirm: function(param){
		var base = {title: '询问', word:'', way: '', url:'', type:'post', data:'', target: null, callback:null};
		base = bolevine.merge(base, param);
		layer.confirm(base.word, {icon: 3, title:base.title}, function(index){
			layer.close(index);
			switch(base.way){
				case "page":
					bolevine.forward(base.url);
					break;
				case "dialog":
					bolevine.dialog({url:base.url});
					break;
				case "ajax":
					bolevine.vjax(base);
					break;
				default:
					break;
			}
			
		}, function(){
			
		});
	},
	vjax: function(param){
		var base = {url:'', type:'post', data:'', target: null, callback:null};
		base = bolevine.merge(base, param);
		$.ajax({type: base.type, url: base.url, data: base.data, success: function(r) {
			if(r.status==202){
				if(base.callback){
					var target = base.target;
					var data = r.data;
					eval(base.callback+"(target, data)");
				}
			}else{
				bolevine.alert({message: r.message, flag: 4});
			}
		}, error : function(e){bolevine.alert({message: "错误："+e.responseText, flag: 4});}
		});
	},
	precall: function(target){
		var _precall = $(target).data('precall');
		if(_precall){
			return eval(_precall+"(target)");
		}
		return true;
	},
	merge: function(array1, array2){
		for(item in array2){
			if(array2[item]!=undefined){
				array1[item] = array2[item];
			}
		}
		return array1;
	},
	calldel: function(target, json){
		$(target).parent().parent().remove();
	},
	callload: function(target, json){
		var msg='', url='';
		if(json.message){
			msg = json.message;
		}
		if(json.url){
			url = json.url;
		}
		bolevine.dialogok(msg, url);
	},
	callback: function(target, json){
		bolevine.forward();
	},
	initdate: function(target){
		if(!$(target).hasClass('rendered') && $(target).attr('readonly')!="readonly"){
			$(target).prop('autocomplete','off');// 取消记忆功能
			var id = $(target).attr('id');
			var type = $(target).data('type');
			var format = $(target).data('format');
			var save = $(target).data('save') || 'timestamp';
			var callback = $(target).data('callback');
			var hidden = $(target).data('hidden');
			var params = {};
			params.elem = '#'+id;
			if(type!=''){
				params.type=type;
			}
			if(format!=''){
				params.format=format;
			}
			params.trigger='click';
			params.done=function(date){
				if(save == 'string'){
					if(date){
						$("#"+hidden).val(date);
					}else{
						$("#"+hidden).val('');
					}
				}else{
					var newTimestamp = Date.parse(new Date(date.replace('-','/')))/1000;
					if(newTimestamp){
						$("#"+hidden).val(newTimestamp);
					}else{
						$("#"+hidden).val('');
					}
				}
				
				if(callback){
					eval(callback+"(target)");
				}
			};
			laydate.render(params);
			$(target).addClass('rendered');
		}
	},
	cookie: function(cname, cvalue, exminutes){
		if(cvalue){
			var d = new Date();
			d.setTime(d.getTime()+(exminutes*60*1000));
			var expires = "expires="+d.toGMTString();
			document.cookie = cname + "=" + cvalue + "; " + expires;
		}else{
			var name = cname + "=";
			var ca = document.cookie.split(';');
			for(var i=0; i<ca.length; i++)
			{
				var c = ca[i].trim();
				if (c.indexOf(name)==0) return c.substring(name.length,c.length);
			}
			return null;
		}
	},
	sy: function(){
		layer.alert('only test');
	}
}

$(window).ready(function(){
	bolevine.referer = $("#referer_url").val();
	setTimeout(function(){
		$('.form-btns').width($('.content-wrapper').width()-32).fadeIn(1800);
	},100);

	$(document).on('click','.modaldialog',function(){
		if(!bolevine.precall($(this))) return false;
		var param = {};
		param.title=$(this).data('title');
		param.size=$(this).data('area');
		param.url = $(this).data('url');
		param.html = $(this).data('html');
		var _maxmin = $(this).data('maxmin');
		if(_maxmin) param.max= true;
		bolevine.dialog(param);
	});

	$(document).on('click', '.confirmdialog',function(){
		if(!bolevine.precall($(this))) return false;
		var param = {};
		param.word=$(this).data("word");
		param.way=$(this).data('way');
		param.url=$(this).data("url");
		param.type=$(this).data('type');
		param.data=$(this).data('data');
		param.callback = $(this).data('callback');
		param.target = $(this);
		bolevine.confirm(param);
	});

	$(document).on('click', '.asynchtrace',function(){
		if(!bolevine.precall($(this))) return false;
		var param = {};
		param.url=$(this).data("url");
		param.type=$(this).data('type');
		param.data=$(this).data('data');
		param.callback = $(this).data('callback');
		param.target = $(this);
		bolevine.vjax(param);
	});
	
	$(document).on('click', '.btn-radio',function(){
		var radio_type = $(this).data("radio");
		$(".btn-radio[data-radio="+radio_type+"]").removeClass('btn-info');
		$(this).addClass('btn-info');
		var _callback = $(this).data("call");
		if(_callback){
			eval(_callback);
		}
	});
	
	$('.js-laydate').each(function(){
		bolevine.initdate(this);
	});
	
	
	if(MESSAGE){
		bolevine.alert({message:MESSAGE, flag:MESSAGE_FLAG});
	}

	$('.table-form').resize(function(){
		$('.form-btns').width($('.content-wrapper').width()-32)
	});

	// 监听元素宽高变化方法
	//(function($,h,c){var a=$([]),e=$.resize=$.extend($.resize,{}),i,k="setTimeout",j="resize",d=j+"-special-event",b="delay",f="throttleWindow";e[b]=0;e[f]=true;$.event.special[j]={setup:function(){if(!e[f]&&this[k]){return false}var l=$(this);a=a.add(l);$.data(this,d,{w:l.width(),h:l.height()});if(a.length===1){g()}},teardown:function(){if(!e[f]&&this[k]){return false}var l=$(this);a=a.not(l);l.removeData(d);if(!a.length){clearTimeout(i)}},add:function(l){if(!e[f]&&this[k]){return false}var n;function m(s,o,p){var q=$(this),r=$.data(this,d);r.w=o!==c?o:q.width();r.h=p!==c?p:q.height();n.apply(this,arguments)}if($.isFunction(l)){n=l;return m}else{n=l.handler;l.handler=m}}};function g(){i=h[k](function(){a.each(function(){var n=$(this),m=n.width(),l=n.height(),o=$.data(this,d);if(m!==o.w||l!==o.h){n.trigger(j,[o.w=m,o.h=l])}});g()},e[b])}})(jQuery,this);
})

function callRefresh(target, rj){
	window.location.reload();
}

function callDelete(target, rj){
	$(target).parent().parent().remove();
}