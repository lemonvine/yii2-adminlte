{{#each this}}  
<div class="col-sm-6 col-md-3">
	<div class="file-cont">
		<div class="file-view">
			<img class="lazy viewer-toggle" data-src="{{file}}" data-original="{{thumb}}" data-id="{{id}}" data-name="{{name}}">
		</div>
		<p class="file-text">
			<a class="image-del js-image-del" href="javascript:;" data-status="new" data-file="{{file}}">
				<i class="fa fa-trash-o"></i>
			</a>
		</p>
	</div>
</div>
{{/each}}