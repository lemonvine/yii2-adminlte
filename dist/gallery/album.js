(function($){

	$.fn.changeColor = function(option){
		var defaultSetting = { colorStr:"green",fontSize:12};
		var setting = $.extend(defaultSetting,option);
		this.css("color",setting.colorStr).css("fontSize",setting.fontSize+"px");
		return this;
	}
}(jQuery));


$(function(){
	
	$(document).on('click', '.album-choice', function(){
		
	});
	$(document).on('click', '.album-update', function(){
		
	});
	$(document).on('change', '.album-file', function(){
		
	});
	$(".lemon-album").each(function(){
		var _item = $(this).data('item');
		var _dirt = $(this).data('dirt');
		var _hdbar = $(this).data('hdbar');
		var _json = bolevine.str2json(_item);
		if(_json.length>0){
			var tmpfn = Handlebars.compile($('#'+_hdbar).html());
			$("#"+_dirt).html(tmpfn(_json));
		}
	});
	
});