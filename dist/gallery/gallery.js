var GALLERY_BUTTON;
var ELEMENT_UPLOAD = '#gallery-add';


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
	$(document).on('click', ELEMENT_UPLOAD,function(){
		if($(this).hasClass('disabled')){
			return;
		}
		$("#btn_file_upload").attr('multiple',true).click();
	});
	
	// 选择图片后，开始上传
	$(document).on('change','#btn_file_upload',function(event){
		layer.load();
		$(ELEMENT_UPLOAD).addClass('disabled');
		var _directory = $(GALLERY_BUTTON).data('json');
		delete _directory.files;
		$("#txt_folder").val(JSON.stringify(_directory));
		
		$.ajax({
			url: PATH_UPLOAD, data: new FormData($('#uploadForm')[0]), type: 'POST', cache: false, processData: false, contentType: false,
			success: function(rd){
				rd = galleryvine.ejson(rd);
				if(rd.status==202){
					var _json = rd.data;
					if(_json.length>0){
						_json = concatTitle(_json);
						var type_btn_id = "#type_btn_" + object_id + "_" +filetype_id;
						var tmpfn = Handlebars.compile($("#template_pictures_data").html());
						if(!replace_sign){
							$(type_btn_id).find('p').append(tmpfn(_json));
						}else{
							$("#hid_file_" + $('#txt_file_id').val()).before(tmpfn(_json)).remove();
						}
						layoutUploadLayer(object_id, filetype_id);
						$("#js_sel_file").prop('files',null);
						event.target.value = '';
						layer.closeAll();
					}
					else{
						layer.msg("上传失败:"+ r.message);
					}
				}
				else{
					galleryvine.upfail(rd.message);
				}
				
			},
			error: function(data){
				galleryvine.upfail(data.statusText);
			}
		});
		//console.log($("#txt_directory").val());
	});
	Handlebars.registerHelper('moment', function (url) {
		var formatStr = options.hash.format || 'YYYY-MM-DD';
		return new Handlebars.SafeString(moment(date).format(formatStr));
	});

}


var galleryvine = {
	v: "1",
	ejson: function(data){
		if(typeof(data)=="string"){
			try{
				data = eval('('+data+')');
			}
			catch(){
				data = {status:401, message:'非法JSON'};
			}
			
		}
		return data;
	},
	layout: function(){
		var json = $(GALLERY_BUTTON).data('json');
		var tmpfn = Handlebars.compile($("#template_gallery").html());
		$(".gallery-upload").empty().hide();
		$(GALLERY_BUTTON).parents(".gallery-box").find(".gallery-upload").html(tmpfn(json.files)).fadeIn();
		layer.closeAll();
	},
	upfail: function(msg){
		layer.closeAll();
		$(ELEMENT_UPLOAD).removeClass('disabled');
		if(msg){
			msg = "上传失败："+msg;
		}
		else{
			msg = "上传失败！";
		}
		layer.msg(msg);
	}
};
