var GALLERY_BUTTON;
var ELEMENT_UPLOAD = '#gallery-add';
var ELEMENT_FILEINPUT = '#btn_file_upload';
var ELEMENT_FILEID = '#txt_id';
var ELEMENT_MOVETO = '#button_moveto';
var SELECTED_FILES = [];

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
		$(ELEMENT_FILEID).val(0);
		$(ELEMENT_FILEINPUT).attr('multiple',true).click();
	});

	// 显示名称编辑
	$(document).on('click', '.file-pencil', function(){
		$(this).parents('.gallery-thumb').find('.file-text').hide().siblings('.file-edit').css('display','flex');
	});

	// 修改名称
	$(document).on('click', '#edit_submit', function(){
		var _this = this;
			new_title = $(_this).siblings('textarea').val(),
			_file = galleryvine.json2str($(this).parents('.gallery-thumb').data('soul'));

		var _data={};
			_data.title = new_title;
			_data.file = _file;

		$.ajax({url: PATH_EDIT, data: _data, type: 'POST', cache: false,
			success: function(rd){
				rd = galleryvine.str2json(rd);
				if(rd.status==202){
					var _json = galleryvine.str2json(_file);
					var _id = _json.file_id;
					var _files = galleryvine.str2json($(GALLERY_BUTTON).data('files'));

					for(var i=0,len=_files.length; i<len; i++){
						if(_id==_files[i]['file_id']){
							_files[i]['title'] = new_title;
							break;
						}
					}

					$(_this).parents('.gallery-thumb').find('.file-edit').hide().siblings('.file-text').html(new_title).fadeIn();

					bolevine.alert({message:'修改成功', flag: 8});
				}
				else{
					var msg = '修改失败:' + rd.message;
					bolevine.alert({message: msg, flag: 4});
				}
			},
			error: function(data){
				bolevine.alert({message:'修改失败', flag: 4});
			}
		});
	});
	
	// 替换
	$(document).on('click', '.file-replace', function(){
		var e = window.event;
		e.stopPropagation();
		var _file = galleryvine.str2json($(this).parents('.gallery-thumb').data('soul'));
		$(ELEMENT_FILEID).val(_file.file_id);
		$(ELEMENT_FILEINPUT).removeAttr('multiple').trigger('click');
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
		var _attr = galleryvine.json2str($(GALLERY_BUTTON).data('attr'));
		var _file = galleryvine.json2str($(this).parents('.gallery-thumb').data('soul'));
		var _data={};
		_data.folder = _attr;
		_data.file = _file;
		$.ajax({url: PATH_DELFILE, data: _data, type: 'POST', cache: false,
			success: function(rd){
				rd = galleryvine.str2json(rd);
				if(rd.status==202){
					var _json = galleryvine.str2json(_file);
					var _id = _json.file_id;
					var _files = galleryvine.str2json($(GALLERY_BUTTON).data('files'));

					for(var i=0,len=_files.length; i<len; i++){
						if(_id==_files[i]['file_id']){
							_files.splice(i, 1);
							break;
						}
					}

					$(GALLERY_BUTTON).data('files', galleryvine.json2str(_files));
					_json = galleryvine.str2json(_attr);
					if(_json.upload_type==3 && _files.length==0){
						galleryvine.docid(0);
					}
					
					galleryvine.layout();
					bolevine.alert({message:'删除成功', flag: 8});
				}
				else{
					var msg = '删除失败:' + rd.message;
					bolevine.alert({message: msg, flag: 4});
				}
			},
			error: function(data){
				bolevine.alert({message:'删除失败', flag: 4});
			}
		});
	});
	
	/********************批量处理*******************/

	// 全选
	$(document).on('click', '.check-all', function(){
		var _input = $("#checkboxall");
		var _checks = $(_input).parents(".gallery-upload").find('.file-filter>input');
		if(_input[0].checked){
			$(_checks).each(function(){this.checked=true;});
		}else{
			$(_checks).each(function(){this.checked=false;});
		}
	});
	
	// 点击移动到
	$(document).on('click', '.move-all', function(){
		//return false;
		galleryvine.select($(this), 'galleryvine.move');
		
	});
	//移动目的目录选中事件
	/*$(document).on('click', '.moveto_btn', function(){
		$("#box_file_moveto .moveto_btn").removeClass('active');
		$(this).addClass('active');
	});
	*/
	//移动目的目录选中事件
	$(document).on('show.bs.dropdown', '.moveto_btn', function(){
		return false;
		$("#box_file_moveto .moveto_btn").removeClass('active');
		$(this).addClass('active');
	});
	
	
	Handlebars.registerHelper('ifeven', function (url, extension, options) {
		var point = url.lastIndexOf("."), type = url.substr(point+1);
		var _extension = EXTENSION[type];
		if(extension == _extension) {
			return options.fn(this);
		} else {
			return options.inverse(this);
		}
	});
	Handlebars.registerHelper('json2str', function (data) {
		if(typeof(data)!="string"){
			try{
				data = JSON.stringify(data);
			}
			catch(error){
				data = "";
			}
		}
		return data;
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
		var sortable1 = document.getElementById('gallery-content');
		new Sortable(sortable1, {
			handle: '.file-sort',
			animation: 300,
			filter:'.gallery-add',
			ghostClass: '.gallery-add',
			onEnd: function (evt) {
				galleryvine.sort(evt);
			}
		});
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
	},
	select: function(object, callback){
		SELECTED_FILES = [];
		var _checks = $(object).parents(".gallery-upload").find('.file-filter>input:checked');
		$(_checks).each(function(){
			var _file = galleryvine.json2str($(this).parents('.gallery-thumb').data('soul'));
			SELECTED_FILES.push(_file);
		});
		if(SELECTED_FILES.length == 0){
			bolevine.alert({message: '请选择文件', flag: 4});
			return;
		}
		eval(callback+"()");
	},
	move: function(){
		var html ='<div class="card"><div class="card-header"><h3 class="card-title text-primary">请选择移动到的目录</h3></div><div class="card-body"><ul id="category_tree" class="ztree"></ul><a href="javascript:;" class="btn btn-primary" onclick="galleryvine.moveto();"><b>确定</b></a></div></div>';
		layer.open({type: 4, fixed: 0, shade: 0, shadeClose:1, title: false, closeBtn:1, time: 18000,
			tips: [1, '#efefef'], content: [html, ELEMENT_MOVETO],
			success: function(layero, index){
				$.fn.zTree.init($("#category_tree"), {
					check: {enable: false, chkStyle: "radio", radioType: "all"},
					view:{showIcon: false}
				}, galleryvine.str2json(MOVETO_TREE));

				var treeObj = $.fn.zTree.getZTreeObj("category_tree");
				treeObj.expandAll(true);
			}
		});
	},
	moveto: function(nodes){
		var _attr = galleryvine.json2str($(GALLERY_BUTTON).data('attr'));
		var treeObj = $.fn.zTree.getZTreeObj("category_tree");
		var nodes = treeObj.getSelectedNodes(true);

		if(nodes.length==0){
			bolevine.alert({message: '请选择要移动到的目录', flag: 4});
			return false;
		}
		
		var selected = nodes[0].id;

		if(selected == galleryvine.str2json(_attr).folder_id){
			bolevine.alert({message: '不可移动到当前目录', flag: 4});
			return false;
		}

		var _data={};
		_data.files = galleryvine.json2str(SELECTED_FILES);
		_data.moveto = selected;

		$.ajax({url: PATH_MOVE, data: _data, type: 'POST', cache: false,
			success: function(rd){
				rd = galleryvine.str2json(rd);
				if(rd.status==202){
					var _select = SELECTED_FILES;
					var _files = galleryvine.str2json($(GALLERY_BUTTON).data('files'));
					var to_label = $('#file-label-'+ selected).find('button'),
						to_files = galleryvine.str2json(to_label.data('files'));

					for(var i=0; i < _select.length; i++){
						_select[i] = galleryvine.str2json(_select[i]);
						_select[i]['folder_id'] = selected;
						to_files.push(_select[i]);
						var _id = _select[i]['file_id'];

						for(var j=0; j < _files.length; j++){
							if(_id == _files[j]['file_id']){
								_files.splice(j, 1);
							}
						}
					}

					$(GALLERY_BUTTON).data('files', galleryvine.json2str(_files));
					$(to_label).data('files', galleryvine.json2str(to_files));

					_json = galleryvine.str2json(_attr);
					if(_json.upload_type==3 && _files.length==0){
						galleryvine.docid(0);
					}

					galleryvine.layout();

					layer.closeAll();
					bolevine.alert({message:'修改成功', flag: 8});
				}
				else{
					var msg = '修改失败:' + rd.message;
					bolevine.alert({message: msg, flag: 4});
				}
			},
			error: function(data){
				bolevine.alert({message:'修改失败', flag: 4});
			}
		})
	},

	sort: function(evt){
		var _files = galleryvine.str2json($(GALLERY_BUTTON).data('files')),
			old_index = evt.oldIndex;
			new_index = evt.newIndex,
			old_array = galleryvine.str2json(_files[old_index]),
			new_array = galleryvine.str2json(_files[new_index]),
			old_serial = old_array.serial,
			news_erial = new_array.serial,
			_data={};

		_data.files={};
		_data.files[0] = {file:old_array,serial:news_erial};
		_data.files[1] = {file:new_array,serial:old_serial};

		$.ajax({url: PATH_SERIAL, data: _data, type: 'POST', cache: false,
			success: function(rd){
				rd = galleryvine.str2json(rd);
				if(rd.status==202){
					old_array['serial'] = news_erial;
					new_array['serial'] = old_serial;

					_files[old_index] = new_array;
					_files[new_array] = old_array;
					
					$(GALLERY_BUTTON).data('files', galleryvine.json2str(_files));
				}
				else{
					galleryvine.layout();
					var msg = '修改失败:' + rd.message;
					bolevine.alert({message: msg, flag: 4});
				}
			},
			error: function(data){
				galleryvine.layout();
				bolevine.alert({message:'修改失败', flag: 4});
			}
		})
	}
};
