
var albumvine ={
	v: '1',
	pose1: function(hdbar, dirt, json, $control){
		var json = bolevine.str2json(json);
		var _controlValue = "";
		if(json.length>0){
			var tmpfn = Handlebars.compile($('#'+hdbar).html());
			$("#"+dirt).html(tmpfn(json));
			$.each(function(){
				_controlValue += this['id']+",";
			});
			_controlValue = _controlValue.substr(0, _controlValue.length-1);
		}else{
			$("#"+dirt).html('');
		};
		$("#"+$control).val(_controlValue);
	},
	pose: function(control){
		var that = $("#"+control);
		var _item = $(that).data('item');
		var _dirt = $(that).data('dirt');
		var _hdbar = $(that).data('hdbar');
		
		var json = bolevine.str2json(_item);
		var _controlValue = "";
		if(json.length>0){
			var tmpfn = Handlebars.compile($('#'+_hdbar).html());
			$("#"+_dirt).html(tmpfn(json));
			$.each(function(){
				_controlValue += this['id']+",";
			});
			_controlValue = _controlValue.substr(0, _controlValue.length-1);
		}else{
			$("#"+_dirt).html('');
		};
		$(that).val(_controlValue);
	},
	choice: function(field, data){
		layer.closeAll();
		var _original = $("#"+field).data('item');
		_original = bolevine.str2json(_original);
		data = bolevine.str2json(data);
		data= $.merge(_original, data);
		var _new = bolevine.json2str(_original);
		$("#"+field).data('item', _new);
		$("#"+field).attr('data-item', _new);
		albumvine.pose(field);
	},
	evalue: function(control){
		var _item = $("#"+_form).data('item');
	}
}


$(function(){
	$(document).on('click', '.album-choice', function(){
		var _url = $(this).data('url');
		var _control = $(this).parent().find('.lemon-album')[0];
		var _selected = $(_control).val();
		var _field = $(_control).attr('id')
		var _ext = {sel: _selected,  fd: _field};
		var ext = encodeURI(bolevine.json2str(_ext));
		_url += "&ext="+ ext;
		bolevine.dialog({title:'选择', url: _url});
	});
	$(document).on('click', '.album-upload', function(){
		$(this).siblings('.album-file').click();
	});
	$(document).on('change', '.album-file', function(){
		layer.msg('正在上传中，');
		var file = $(this);
		var _url= $(this).data('url');
		var _control = $($(this).parent().find('.lemon-album')[0]).attr('id');
		var formData = new FormData();
		var files = this.files;
		$(files).each(function(){
			formData.append('file[]', this)
		});
		$.ajax({
			url: _url,
			type: "post",
			dataType: "json",
			cache: false,
			data: formData,
			processData: false,// 不处理数据
			contentType: false, // 不设置内容类型
			success: function (data) {
				layer.closeAll();
				data = bolevine.str2json(data);
				if(data.status==202){
					var _json= data.data.s;
					if(_json.length>0){
						var _item = $("#"+_control).data('item');
						if(_item!=""){
							_item = bolevine.str2json(_item);
							_item = $.merge(_item, _json);
						}
						var _new = bolevine.json2str(_item);
						$("#"+_control).data('item', _new);
						$("#"+_control).attr('data-item', _new);
						albumvine.pose(_control);
					}
				}else{
					bolevine.alert({flag: 4, message: '上传失败'});
				}
			}
		});
		
		
	});
	$(".lemon-album").each(function(){
		var _control = $(this).attr('id');
		albumvine.pose(_control);
	});
	
});