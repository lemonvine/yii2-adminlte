/**
 * 
 */
var bolevine = {
	v: '1',
	referer: '',
	opening: null,
	cookie_name: {save_reappear: "j2u8i3"},
	loadimg: '<div class="loading"></div>',
	str2json: function(data){
		if(typeof(data)=="string"){
			try{
				data = eval('('+data+')');
			}
			catch(error){
				data = {status:401, message:'非法JSON'+error.message};
			}
		};
		return data;
	},
	json2str: function(data){
		if(typeof(data)!="string"){
			try{
				data = JSON.stringify(data);
			}
			catch(error){
				data = "";
			}
		};
		return data;
	},
	turnoff: function(){
		if(bolevine.opening){
			layer.close(bolevine.opening);
			bolevine.opening = null;
		};
	},
	suicide: function(){
		parent.bolevine.turnoff();
	},
	unyii: function(form){
		var data = $(form).data('yiiActiveForm');
		if(data){
			data.validated=true;
			$(form).data('yiiActiveForm',data);
		}
	},
	alert: function(param){
		var base = {flag: 1, message: 'md', icon: 1, time: 2000, callback:null};
		$.extend(base, param);
		switch (base.flag.toString()){
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
		};
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
		};
	},
	reload: function(is_chain){
		var url = location.href;
		if(!url.includes('ref')){
			if(url.includes('?')){
				url = url + "&ref="+encodeURI(bolevine.referer);
			}
			else{
				url = url + "?ref="+encodeURI(bolevine.referer);
			};
		};
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
		};
	},
	dialog:function(param){
		var size = {xs:['420px', '280px'], sm:['600px', '450px'], md: ['800px', '600px'], lg: ['1000px', '750px']};
		var base = {title:'信息', size: 'md', resize:false, max:false, url: '', html:'', refer:'', callback:null};
		$.extend(base, param);
		var config = {type: 1, title: base.title, area:size[base.size], resize: base.resize};
		if(base.max) config.maxmin = true;
		if(base.url) {
			if(base.refer){
				var refer = base.refer;
				var refers = refer.split(';');
				var exts = [];
				for(var i=0,l=refers.length; i<l; i++){
					var el = refers[i];
					var val = $(el).val();
					exts.push({c:el, v: val});
				}
				var ext = encodeURIComponent(bolevine.json2str(exts));
				base.url = bolevine.appendurl(base.url, 'ext', ext);
			}
			config.type = 2;
			config.content = base.url;
		}
		else{
			config.content = base.html
		};
		if(base.callback){
			config.success = function(layero, index){eval(base.callback+"(layero, index)");};
		};
		bolevine.opening = layer.open(config);
	},
	confirm: function(param){
		var base = {title: '询问', word:'', way: '', url:'', type:'post', data:'', target: null, callback:null};
		$.extend(base, param);
		layer.confirm(base.word, {icon: 3, title:base.title}, function(index){
			layer.close(index);
			switch(base.way){
				case "page":
					bolevine.forward(base.url);
					break;
				case "dialog":
					bolevine.dialog({title:base.title, url:base.url});
					break;
				case "ajax":
					bolevine.vjax(base);
					break;
				default:
					break;
			};
			
		}, function(){
			
		});
	},
	vjax: function(param){
		var base = {url:'', type:'post', data:'', target: null, callback:null, reload: true};
		$.extend(base, param);
		var data = bolevine.str2json(base.data);
		$.ajax({type: base.type, url: base.url, data: data, success: function(r) {
			if(r.status==202){
				if(base.callback){
					var target = base.target;
					var data = r.data;
					var reg=new RegExp("\\(*\\)");
					if(reg.test(base.callback)){
						eval(base.callback);
					}else{
						eval(base.callback+"(target, data)");
					}
				}else if(base.reload){
					bolevine.reload();
				};
			}else{
				bolevine.alert({message: r.message, flag: 4});
			};
		}, error : function(e){bolevine.alert({message: "错误："+e.responseText, flag: 4});}
		});
	},
	precall: function(target){
		var _precall = $(target).data('precall');
		if(_precall){
			return eval(_precall+"(target)");
		};
		return true;
	},
	search: function(form){
		$('#submit_type').val('search');
		if(!form){
			form = "form";
		};
		$(form).submit();
	},
	export: function(form){
		$('#submit_type').val('export');
		if(!form){
			form = "form";
		};
		$(form).submit();
	},
	save: function(url, form){
		if(!form){
			form = "#main_form";
		};
		$.ajax({url: url, type: 'POST', cache: false, processData: false, contentType: false,
			data: new FormData($(form)[0]),
			success: function(rd){
				rd = bolevine.str2json(rd);
				if(rd.status == 202){
					bolevine.alert({message: rd.data, flag: 8});
				}
				else{
					bolevine.alert({message:rd.message, flag: 4});
				}
			}
		});
	},
	linkage: function(target, data){
		$(target).html(data);
	},
	merge: function(array1, array2){
		for(item in array2){
			if(array2[item]!=undefined){
				array1[item] = array2[item];
			};
		};
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
			var btn = $(target).data('btn')||'';
			var params = {};
			params.elem = '#'+id;
			if(type!=''){
				params.type=type;
			};
			if(format!=''){
				params.format=format;
			};
			params.trigger='click';
			
			if(btn!=''){
				params.btns= ['clear', btn, 'confirm'];
			};
			
			params.done=function(date){
				if(hidden!=''){
					if(save == 'string'){
						if(date){
							$("#"+hidden).val(date);
						}else{
							$("#"+hidden).val('');
						}
					}else{
						var newTimestamp;
						if(btn=='long' && date=='2099-12-31'){
							newTimestamp=1;
						}
						else{
							newTimestamp = Date.parse(new Date(date.replace('-','/')))/1000;
						}
						if(newTimestamp){
							$("#"+hidden).val(newTimestamp);
						}else{
							$("#"+hidden).val('');
						}
					};
				}
				if(callback){
					eval(callback+"(target)");
				};
			};
			laydate.render(params);
			$(target).addClass('rendered');
		};
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
			};
			return null;
		};
	},
	handlebarReady:function(bound){
		if(!bound){
			bound = document;
		}
		var hbs = $(bound).find(".handlebar-field");
		$(hbs).each(function(){
			var _v = $(this).val();
			if(!_v){
				_v = '{}';
			};
			_v = eval("("+_v+")");
			var _id = $(this).attr('id');
			var _tp = $(this).data('template');
			var _sort = $(this).parents('.form-group').data('sort');
			if(_sort=="desc"){
				_v = _v.reverse();
			}
			var _template = Handlebars.compile($(_tp).html());
			$("#hb_"+_id).html(_template(_v));
		});
	},
	ready:function(bound){
		if(!bound){
			bound = document;
		}
		bolevine.handlebarReady(bound);
		$(bound).find('.js-laydate').each(function(){
			bolevine.initdate(this);
		});
		$(bound).find('.combo').each(function(){
			$(this).comboselect();
		});
		$(bound).find(".leafblinds").each(function(){
			$(this).change();
		});
	},
	appendurl:function(url, key, value){
		if (url.indexOf(key+"=") > -1) {
			var re = eval('/(' + key + '=)([^&]*)/gi');
			url = url.replace(re, key + '=' + value);
		}else {
			var paraStr = key + '=' + value;
			var idx = url.indexOf('?');
			if (idx < 0)
				url += '?';
			else if (idx >= 0 && idx != url.length - 1)
				url += '&';
			url=url + paraStr;
		}
		return url;
	},
	imageurl: function (file) {
		var url = null ;
		if(window.webkitURL!=undefined){
			url = window.webkitURL.createObjectURL(file);
		}else if(window.createObjectURL!=undefined){
			url = window.createObjectURL(file);
		}else if(window.URL!=undefined){
			url = window.URL.createObjectURL(file);
		}
		return url;
	},
	gallery: function(obj){
		var _for = $(obj).data('for');
		if(_for){
			var _images = $(obj).find(".media");
			var _result = [];
			$(_images).each(function(){
				var _file = $(this).data('f');
				var _type = $(this).data('p');
				if(_file){
					if(_type=='image'){
						_result.push(_file);
					}else{
						_result.push([_type, _file]);
					}
				}
			});
			$("#"+_for).val(bolevine.json2str(_result));
		}
	},
	rctt: function(url, callback){
		$("#content").html(bolevine.loadimg);
		$.ajax({
			url: url,
			success: function(result){
				$("#content").html(result);
				if(callback){
					eval("("+callback+"())");
				}
			},
			error: function(jqXHR, textStatus, errorThrown){
				$("#content").html("数据出错了，请联系管理员");
			}
		});
	}
};
$(window).ready(function(){
	bolevine.referer = $("#referer_url").val();
	setTimeout(function(){
		$('.form-btns').width($('.content-wrapper').width()-32).fadeIn(1800);
	},100);
	//弹出框
	$(document).on('click','.modaldialog',function(){
		if(!bolevine.precall($(this))) return false;
		var param = {};
		param.title=$(this).data('title');
		param.size=$(this).data('area');
		param.url = $(this).data('url');
		param.html = $(this).data('html');
		param.refer = $(this).data('refer');
		var _maxmin = $(this).data('maxmin');
		if(_maxmin) param.max= true;
		bolevine.dialog(param);
	});
	//保存
	$(document).on('click', '.preservation', function(){
		var _form = $(this).data('f');
		var _method = $(this).data('m');
		var _submit = $(this).data('s');
		var _callback = $(this).data('c');
		$(_form).find("#submit_type").val(_submit);
		var data = $(_form).data('yiiActiveForm');
		if(data){
			data.validated=true;
			$(_form).data('yiiActiveForm',data);
		}
		try{
			var _tabs = $(_form + " li>a.active");
			if(_tabs.length>0){
				var _tabid = $(_tabs[0]).attr('aria-controls');
				var _scroll = $("#"+_tabid).scrollTop();
				bolevine.cookie(bolevine.cookie_name.save_reappear, ""+_tabid+","+_scroll, 2);
			}
		}
		catch(ex){
		}
		if(_method=='post'){
			$(_form).submit();
		}else if(_method=='ajax'){
			layer.load();
			var _url = decodeURI($(this).data('u'));
			var patt = _url.match(/(?<={).*?(?=})/g);
			if(patt){
				for(var i=0,l=patt.length; i<l; i++){
					var _val = $("#"+patt[i]).val();
					var reg = new RegExp("{"+patt[i]+"}");
					_url = _url.replace(reg, _val);
				}
			}
			var data = new FormData($(_form)[0]);
			$.ajax({
				type: 'POST',
				dataType: 'json',
				processData: false,
				contentType: false,
				data: data,
				url: _url,
				success: function(r) {
					layer.closeAll();
					if(r.status==202){
						if(_callback!=""){
							var _data=r.data;
							eval(_callback+"(_data)");
						}else if(r.data != ""){
							layer.msg(r.data);
						}else{
							layer.msg('保存成功');
						}
						
					}else{
						layer.msg(r.message);
					}
				},
				error: function(xhr, textStatus, errorThrown) {
					layer.closeAll();
				}
			});
		}
		
	});
	//提交
	$(document).on('click', '.ceremonial',function(){
		if(!bolevine.precall($(this))) return false;
		var _form = $(this).data('form');
		var _word = $(this).data('word');
		layer.confirm(_word, {
		  btn: ['提交','放弃'], skin: 'layui-layer-molv'
		}, function(index){
			$("#submit_type").val('submit');
			$(_form+' .nav-tabs li').removeClass('active').eq(0).addClass('active');
			$(_form+' .tab-content .tab-pane').removeClass('active').eq(0).addClass('active');
			try{
				$(_form).submit();
				layer.close(index);
				setTimeout(function () {
					var error = $(".is-invalid");
					if(error.length>0){
						var block = $(error[0]).parents('.form-group').find(".invalid-feedback");
						var message = $(block[0]).html();
						bolevine.alert({flag: 4, message: message});
					}
				}, 500);
			}
			catch($er){
				var message = '出现异常,请联系管理员';
				bolevine.alert({message: message});
			}
		}, function(){
			var message = '已放弃';
			bolevine.alert({message: message});
		});
		
	});
	//确认框
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
	//异步ajax
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
	//关联下拉框
	$(document).on('change', '.implicate',function(){
		if(!bolevine.precall($(this))) return false;
		var param = {};
		param.url=$(this).data("url");
		param.data={id: $(this).val()};
		param.callback = 'bolevine.linkage';
		param.target = $(this).data('target');
		param.type='get';
		bolevine.vjax(param);
	});
	//change事件，调用方法
	$(document).on('change', '.changecall',function(){
		var callback = $(this).data('callback');
		eval(callback);
	});
	//change事件显示隐藏模块
	$(document).on('change', '.leafblinds', function(){
		var _tag = this.tagName;
		var _val;
		if(_tag=='DIV'){
			_val=$(this).find('input:radio:checked').val();
		}else{
			_val=$(this).val();
		}
		
		var _mutex=""+$(this).data('mutex');
		var _map=bolevine.str2json($(this).data('map'));
		var _land=$(this).data('land');
		if(_mutex=="1"){
			for(var key in _map ){
				$(_map[key]).hide();
			}
			if(_land!=""){
				$(_land).hide();
			}
		}else if(_mutex!=""){
			$(_mutex).hide();
		}
		if(_map.hasOwnProperty(_val)){
			$(_map[_val]).show();
		}else if(_land!=""){
			$(_land).show();
		}
		var _callback = $(this).data('callback');
		if(_callback != undefined && _callback != ""){
			eval("("+_callback+"(_val))");
		}
	});
	//begin handlebar
	$(document).on('blur', '.handlebar-blur', function(){
		var _index=$(this).data('index'), _key = $(this).data('key'), _info = $(this).parents('.handlebar-info')[0];
		var _id = $(_info).data('hidden');
		var _v = $("#"+_id).val();
		if(!_v){
			_v = '{}';
		}
		_v = eval("("+_v+")");

		if(_index!=undefined){
			if(!_v[_index]){
				_v[_index] = new Array();
			}
			_v[_index][_key] = $(this).val();
		}
		else if(_key!=undefined){
			_v[_key] = $(this).val();
		}

		$("#"+_id).val(JSON.stringify(_v));
	});
	//end handlebar
	//begin list load
	$(document).on('click', '.cursor-item', function(){
		var _id = $(this).data('id');
		var _url = $(this).data('url');
		var _callback = $(this).data('callback');
		if(!_url){
			_url = "";
		}
		_url = eval('RECONTURL'+_url);
		var _url = bolevine.appendurl(_url, 'id', _id);
		$(this).closest('ul').find('li.active').removeClass('active');
		$(this).closest('li').addClass('active');
		bolevine.rctt(_url, _callback);
	});
	//load content
	$(document).on('click', '.loadpage', function(){
		var _id = $(this).data('k');
		var _url = $(this).data('url');
		var _target = $(this).data('target');
		var _type = $(this).data('type');
		var _callback = $(this).data('callback');
		var that=this;
		if(_id){
			_id = parseInt(_id)+1;
			_url = bolevine.appendurl(_url, 'key', _id);
		}
		if(!_type){
			_type = 'replace';
		}
		$.ajax({type: 'get', url: _url, success: function(r){
				$(_target).find(".empty-state").remove();
				if(_type=='replace'){
					$(_target).html(r);
					bolevine.ready(_target);
				}else if(_type=='append'){
					$(_target).append(r);
					bolevine.ready(_target+">:last");
				}else if(_type=='prepend'){
					$(_target).prepend(r);
					bolevine.ready(_target+">:first");
				}
				if(_id){
					$(that).data('k', _id).attr('data-k', _id);
				}
				
				if(_callback){
					eval(_callback+"(that, _target)");
				}
			}, error: function(e){
				bolevine.alert({message: "错误："+e.responseText, flag: 4});
			}
		});
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
	$(document).on('click', ':radio',function(){
		var pair = $(this).data('pair');
		if(pair){
			var txt = $(this).data('text');
			$("#"+pair).val(txt);
		}
	});
	//
	//金额转为大写
	$(document).on('keyup', '.rmb-chinese', function(){
		var _val = parseFloat($(this).val());
		var _id = $(this).attr('id');
		layer.tips(_val.chinese(), "#"+_id, {tips:1});
	});
	$(document).on('mousedown', '.rmb-chinese', function(){
		var _val = parseFloat($(this).val());
		var _id = $(this).attr('id');
		layer.tips(_val.chinese(), "#"+_id, {tips:1});
	});
	$(document).on('click', '.btn-save', function(){
		var url = $(this).data('url');
		var form = $(this).data('form');
		bolevine.save(url, form);
	});
	//上传图片
	$(document).on('change', '.upload-btn input', function(){
		var files = this.files;
		var multi = $(this).attr('multiple');
		var beforehand = $(this).data('bh');
		var folder = $(this).data('f');
		var gallery = $(this).parents('.upload-btn').siblings('.upload-gallery');
		var uploadfail = function(pm, pf, po){
			if(pm){
				$("#"+pf).closest('li').remove();
			}else{
				var img = $(gallery).find('.media');
				if(img.length>0){
					if(po!=''){
						$(img[0]).attr('src', po);
					}else{
						$(img[0]).closest('li').remove();
					}
				}
			}
			layer.msg('上传失败');
		};
		var lookhref = function(obj, url){
			var _look = $(obj).parents('li.upload-show').find('.look');
			if(_look && _look[0]){
				$(_look[0]).attr('href', url);
			}
		}
		
		var random = Math.floor((Math.random()*1000)+1);
		var original = '';
		for(var i=0,l=files.length; i<l; i++){
			var file = this.files[i];
			var imgsrc = bolevine.imageurl(file);
			var id="img"+random+"_"+i;
			var type= file.type.substring(0, file.type.indexOf('/'));

			if(imgsrc){
				var _title = '<div class="upload-tools"><a href="javascript:;" class="delete text-danger">删除</a><a href="javascript:;" class="look" target="_blank">查看</a></div>';
				var _media = '';
				if(type=="image"){
					_media = $('<img class="media">');
				}else if(type=="video"){
					_media = $('<video class="media" controls="controls"></video>');
				}else if(type=="audio"){
					_media = $('<audio class="media" controls="controls"></audio>');
				}
				_media.attr('id', id).attr('src', imgsrc).data('p', type);
				
				if(multi){
					$('<li class="upload-show img-thumbnail"></li>').append(_media).append(_title).appendTo($(gallery));
				}else{
					var img = $(gallery).find('.media');
					if(img.length>0){
						original = $(img[0]).attr('src');
						$(img[0]).attr('src', imgsrc);
					}else{
						$('<li class="upload-show img-thumbnail"></li>').append(_media).append(_title).appendTo($(gallery));
					}
				}
				if(beforehand){
					var formData = new FormData();
					formData.append('uploadfile', file);
					formData.append('path', folder);
					formData.append('fid', id);
					$.ajax({
						url: URL_UPLOAD,
						type:"post",
						data: formData,
						contentType: false,
						processData: false,
						success: function(data) {
							if (data.status == 202) {
								var result = data.data;
								$(result).each(function(i){
									var _fid = this.fid;
									if(this.result==0){
										uploadfail(multi, _fid, original);
									}else if(multi){
										$("#"+_fid).data('f', this.file);
										$("#"+_fid).data('h', this.http);
										lookhref($("#"+_fid), this.http+this.file);
									}else{
										var img = $(gallery).find('.media');
										$(img[0]).data('f', this.file);
										$(img[0]).data('h', this.http);
										lookhref($(img[0]), this.http+this.file);
									}
								});
								bolevine.gallery(gallery);
							} else {
								uploadfail(multi, id, original);
							}
						},
						error:function(data) {
							uploadfail(multi, id, original);
						}
					});
				}
			}else{
				layer.msg('图片异常');
			}
		}
		
	});
	//上传图片view
	$(document).on('click', '.upload-show', function() {
		var _pid = $(this).parent().attr("id");
		layer.photos({
			photos: '#'+_pid
			,anim: 5
		}); 
	});
	//上传图片-删除
	$(document).on('click', '.upload-tools>.delete', function() {
		var _gallery = $(this).parents('ul.upload-gallery');
		$(this).parents('li.upload-show').remove();
		if(_gallery && _gallery[0]){
			bolevine.gallery(_gallery[0]);
		}
		
	});
	//tab切换
	$(document).on('click', '.lazytab a', function () {
		var _tab = $(this).data("load");
		if(_tab!=""){
			var _target = this;
			$.ajax({ url: LAZYTAB_URLS[_tab], success: function(rd){
				$("#"+_tab).html(rd);
				$(_target).data("load", "");
			}});
		}
	});
	
	var menuid = $("#chain_menu").val();
	if(menuid !=undefined && menuid != ""){
		$("#sys_"+menuid).addClass('active');
		var lists = $("#sys_"+menuid).parent().parents('li');
		for(var i=0; i<lists.length; i++){
			$(lists[i]).addClass('menu-open');
			$(lists[i]).children('a').addClass('active');
		}
	}
	bolevine.ready();

	$(document).on('collapsed.lte.cardwidget', '.card', function (){
		$(this).find(".card-tools .loadpage").hide();
		$(this).find(".card-tools button[data-card-widget=collapse]>i").removeClass('fa-angle-down').addClass('fa-angle-right');
	});
	$(document).on('expanded.lte.cardwidget', '.card', function (){
		$(this).find(".card-tools .loadpage").show();
		$(this).find(".card-tools button[data-card-widget=collapse]>i").removeClass('fa-angle-right').addClass('fa-angle-down');
	});
	
	if(typeof(MESSAGE)!="undefined" && MESSAGE){
		bolevine.alert({message:MESSAGE, flag:MESSAGE_FLAG});
	}
	$('.table-form').resize(function(){
		$('.form-btns').width($('.content-wrapper').width()-32)
	});
});

Date.prototype.format = function(fmt){
	var o = {
			"M+" : this.getMonth()+1,
			"d+" : this.getDate(),
			"h+" : this.getHours(),
			"m+" : this.getMinutes(),
			"s+" : this.getSeconds(),
			"q+" : Math.floor((this.getMonth()+3)/3),//季度
			"S"  : this.getMilliseconds()//毫秒
			};
	if(/(y+)/.test(fmt))
		fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));
	for(var k in o)
		if(new RegExp("("+ k +")").test(fmt))
			fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
	return fmt; 
};
Number.prototype.date = function(){
	var time = new Date(this);
	return time;
};
Number.prototype.chinese = function(){
	n=this;
	if (!/^(0|[1-9]\d*)(\.\d+)?$/.test(n))
		return "";
	var unit = "仟佰拾亿仟佰拾万仟佰拾元角分", str = "";
		n += "00";
	var p = n.indexOf('.');
	if (p >= 0)
		n = n.substring(0, p) + n.substr(p+1, 2);
		unit = unit.substr(unit.length - n.length);
	for (var i=0; i < n.length; i++)
		str += '零壹贰叁肆伍陆柒捌玖'.charAt(n.charAt(i)) + unit.charAt(i);
	return str.replace(/零(仟|佰|拾|角)/g, "零").replace(/(零)+/g, "零").replace(/零(万|亿|元)/g, "$1").replace(/(亿)万/g, "$1$2").replace(/^元零?|零分/g, "").replace(/元$/g, "");
};

//加法
Number.prototype.add = function(arg){
	var r1,r2,m;
	try{r1=this.toString().split(".")[1].length}catch(e){r1=0}
	try{r2=arg.toString().split(".")[1].length}catch(e){r2=0}
	m=Math.pow(10,Math.max(r1,r2));
	return (this*m+arg*m)/m
};
//减法
Number.prototype.sub = function (arg){
	return this.add(-arg);
};
//乘法
Number.prototype.mul = function (arg)
{
	var m=0,s1=this.toString(),s2=arg.toString();
	try{m+=s1.split(".")[1].length}catch(e){}
	try{m+=s2.split(".")[1].length}catch(e){}
	return Number(s1.replace(".",""))*Number(s2.replace(".",""))/Math.pow(10,m);
};
//除法
Number.prototype.div = function (arg){
	var t1=0,t2=0,r1,r2;
	try{t1=this.toString().split(".")[1].length}catch(e){}
	try{t2=arg.toString().split(".")[1].length}catch(e){}
	with(Math){
		r1=Number(this.toString().replace(".",""));
		r2=Number(arg.toString().replace(".",""));
		return r1/(r2*Math.pow(10,t1-t2));
	}
};
