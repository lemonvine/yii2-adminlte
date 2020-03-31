var GALLERY_BUTTON;
var ELEMENT_UPLOAD = '#gallery-add';
var ELEMENT_FILEINPUT = '#btn_file_upload';

var initGallery = function(){
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
		galleryvine.initup();
		galleryvine.layout();
	});

	// 点击上传
	$(document).on('click', ELEMENT_UPLOAD,function(){
		if($(this).hasClass('disabled')){
			return;
		}
		$(ELEMENT_FILEINPUT).attr('multiple',true).click();
	});
	
	// 选择图片后，开始上传
	$(document).on('change','#btn_file_upload',function(event){
		layer.load();
		$(ELEMENT_UPLOAD).addClass('disabled');
		var _attr = $(GALLERY_BUTTON).data('attr');
		$("#txt_folder").val(galleryvine.json2str(_attr));
		$.ajax({url: PATH_UPLOAD, data: new FormData($('#uploadForm')[0]), type: 'POST', cache: false, processData: false, contentType: false,
			success: function(rd){
				rd = galleryvine.str2json(rd);
				if(rd.status==202){
					var _upload = rd.data;
					if(_upload.uploaded.length>0){
						var _uploaded = _upload.uploaded;
						var _files = galleryvine.str2json($(GALLERY_BUTTON).data('files'));
						if(_uploaded.length==1){
							var replace = false;
							for(var i=0,len=_files.length; i<len; i++){
								if(_uploaded[0]['file_id']==_files[i]['file_id']){
									_files[i] = _uploaded[0];
									replace = true;
									break;
								}
							}
							if(!replace){
								_files.push(_uploaded[0]);
							}
						}else{
							for(var i=0,len=_uploaded.length; i<len; i++){
								_files.push();
							}
							var json_attr = galleryvine.str2json(_attr);
							if(json_attr.upload_type==3 && json_attr.doc_id==0){
								galleryvine.docid(_uploaded[i]['doc_id']);
							}
						}
						$(GALLERY_BUTTON).data('files', galleryvine.json2str(_files));
						galleryvine.layout();
						layer.closeAll();
						
						if(_upload.fail!=""){
							galleryvine.upfail(_upload.fail);
						}
					}
					else{
						galleryvine.upfail('无上传成功文件');
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
	});
	
	// 删除
	$(document).on('click', '.file-delete',function(){
		var _attr = $(GALLERY_BUTTON).data('attr');
		var _data={};
		_data.id=$(this).data('id');
		_data.folder = _attr;
		var obj_del = $(this);
		$.ajax({url: PATH_DELFILE, data: _data, type: 'POST', cache: false,
			success: function(rd){
				rd = galleryvine.str2json(rd);
				if(rd.status==202){
					var _id = $(obj_del).data('id');
					$(obj_del).parents('.gallery-thumb');
					var _files = $(GALLERY_BUTTON).data('attr');
					for(var i=0,len=_files.length; i<len; i++){
						if(_id==_files[i]['file_id']){
							_files.splice(i, 1);
							break;
						}
					}
					$(GALLERY_BUTTON).data('files', galleryvine.json2str(_files));
					if(upload_type==3 && _files.length==0){
						galleryvine.docid(0);
					}
					
					galleryvine.layout();
					bolevine.alert({message:'删除成功', flag: 8});
				}
				else{
					bolevine.alert({message:'删除失败:' + r.message, flag: 4});
				}
			},
			error: function(data){
				bolevine.alert({message:'删除失败', flag: 4});
			}
		});
	});
	
	
	Handlebars.registerHelper('if_even', function (url, extension, options) {
		var point = url.lastIndexOf("."), type = url.substr(point+1);
		var _extension = EXTENSION[type];
		if(extension == _extension) {
			return options.fn(this);
		} else {
			return options.inverse(this);
		}
	});

}


var galleryvine = {
	v: "1",
	str2json: function(data){
		if(typeof(data)=="string"){
			try{
				data = eval('('+data+')');
			}
			catch(error){
				data = {status:401, message:'非法JSON'+error.message};
			}
			
		}
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
		}
		return data;
	},
	initup: function(){
		var _attr = $(GALLERY_BUTTON).data('attr');
		json = galleryvine.str2json(_attr);
		var _accept = json.file_accept;
		var _type = json.upload_type;
		$(ELEMENT_FILEINPUT).prop("accept",ACCEPT[_accept]);
	},
	layout: function(){
		var _files = $(GALLERY_BUTTON).data('files');
		json = galleryvine.str2json(_files);
		var tmpfn = Handlebars.compile($("#template_gallery").html());
		$(".gallery-upload").empty().hide();
		$(GALLERY_BUTTON).parents(".gallery-box").find(".gallery-upload").html(tmpfn(json)).fadeIn();
		layer.closeAll();
		$(ELEMENT_UPLOAD).prop('files',null);
		if(json.length==0){
			$(GALLERY_BUTTON).find('span').html("");
		}
		else{
			$(GALLERY_BUTTON).find('span').html(json.length);
		}
	},
	upfail: function(msg){
		layer.closeAll();
		$(ELEMENT_UPLOAD).removeClass('disabled');
		$(ELEMENT_UPLOAD).prop('files',null);
		if(msg){
			msg = "上传失败："+msg;
		}
		else{
			msg = "上传失败！";
		}
		bolevine.alert({message:msg, flag: 4});
	},
	docid: function(docid){//更新button的doc_id
		var _attr = galleryvine.str2json($(GALLERY_BUTTON).data('attr'));
		_attr.doc_id = docid;
		$(GALLERY_BUTTON).data('attr', galleryvine.json2str(_attr));
	}
};
