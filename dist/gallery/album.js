(function($){

	$.fn.changeColor = function(option){
		var defaultSetting = { colorStr:"green",fontSize:12};
		var setting = $.extend(defaultSetting,option);
		this.css("color",setting.colorStr).css("fontSize",setting.fontSize+"px");
		return this;
	}
}(jQuery));


$(function(){
	$(document).on('click', '.js-image-del', function(){
		var _status = $(this).data('status');
		var _target = $(this).parent().parent().parent();
		if(_status=="new"){
			var _filename = $(this).data("file");
			
			$.ajax({
				url:pictureDelete,
				type: 'POST',
				data:{'file': _filename},
				success: function(redata){
				    if(typeof(redata)=="string"){
				    	redata = eval('('+redata+')');
				    }
				    if(redata.status=="201"){
						$(_target).remove();
				    }
				    else{
				    	messageShow("删除失败！","");
				    }
				}
			});
		}
		else if(_status=="saved"){
			//var _target = $(this).parent().parent().parent();
			$(_target).find("input[type=checkbox]").prop('checked', true);
			$(_target).hide();
			
		}
		//gallerySort($(_target).parent().prop('id'));
	});
	
});