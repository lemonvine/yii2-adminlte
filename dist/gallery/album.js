
var albumvine ={
	v: '1',
	pose: function(hdbar, dirt, json, $control){
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
	evalue: function(control){
		var _item = $("#"+_form).data('item');
		
	}
}


$(function(){
	
	$(document).on('click', '.album-choice', function(){
		var _url = $(this).data('url');
		var _selected = $($(this).siblings('.lemon-album')[0]).val();
		_url += "&sld="+ _selected;
		bolevine.dialog({title:'选择', url: _url});
	});
	$(document).on('click', '.album-upload', function(){
		$(this).siblings('.album-file').click();
	});
	$(document).on('change', '.album-file', function(){
		layer.msg('正在上传中，');
		var file = $(this);
		var _url= $(this).data('url');
		var _dirt=$(this).data('dirt');
		var _hdbar = $(this).data('hdbar');
		var _form = $($(this).siblings('.lemon-album')[0]).attr('id');
		var formData = new FormData();
		var files = this.files;
		$(files).each(function(){
			formData.append('file[]', this)
		});
		;  //添加图片信息的参数
		//formData.append("name", $("#name").val());
		//formData.append("szm", $("#szm").val());
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
						var _item = $("#"+_form).data('item');
						if(_item!=""){
							_item = bolevine.str2json(_json);
							$.extend(_item, _json);
							_json=_item;
						}
						albumvine.pose(_hdbar, _dirt, _json, _form);
					}
				}else{
					bolevine.alert({flag: 4, message: '上传失败'});
				}
			}
		});
		
		
	});
	$(".lemon-album").each(function(){
		var _item = $(this).data('item');
		var _dirt = $(this).data('dirt');
		var _hdbar = $(this).data('hdbar');
		var _control = $(this).attr('id');
		albumvine.pose(_hdbar, _dirt, _item, _control);
	});
	
});