var GALLERY_BUTTON;


function initGallery(){
	var formHtml = $("#template_upload").html();
	$("form").after(formHtml);
	
	//打开目录
	$(document).on('click', '.gallery-button',function(){
		if($(this).hasClass('active')){
			return;
		}
		$(".gallery-button").removeClass('active');
		$(this).addClass('active');
		GALLERY_BUTTON = $(this);
		galleryvine.layout();
	});

	// 点击上传
	$(document).on('click','#gallery-add',function(){
		if($(this).hasClass('disabled')){
			return;
		}
		$("#btn_file_upload").attr('multiple',true).click();
	});
	
	// 选择图片后，开始上传
	$(document).on('change','#btn_file_upload',function(event){
		layer.load();
		$('#gallery-add').addClass('disabled');
		var _directory = $(GALLERY_BUTTON).data('json');
		delete _directory.files;
		$("#txt_directory").val(JSON.stringify(_directory));
		console.log($("#txt_directory").val());
	});
	Handlebars.registerHelper('moment', function (url) {
		var formatStr = options.hash.format || 'YYYY-MM-DD';
		return new Handlebars.SafeString(moment(date).format(formatStr));
	});

}


var galleryvine = {
	v: "1",
	layout: function(){
		var json = $(GALLERY_BUTTON).data('json');
		var tmpfn = Handlebars.compile($("#template_gallery").html());
		$(".gallery-upload").empty().hide();
		$(GALLERY_BUTTON).parents(".gallery-box").find(".gallery-upload").html(tmpfn(json.files)).fadeIn();
		layer.closeAll();
	}
};
