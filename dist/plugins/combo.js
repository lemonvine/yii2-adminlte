(function (factory) {
	'use strict';
	if (typeof define === 'function' && define.amd) {
		define(['jquery'], factory);
	} else if (typeof exports === 'object' && typeof require === 'function') {
		factory(require('jquery'));
	} else {
		factory(jQuery);
	};
}(function ( $, undefined ) {
	var pluginName = "comboselect",
		dataKey = 'comboselect';
	var defaults = {
		comboPluginClass   : 'combo-plugin',
		comboClass         : 'combo',
		comboArrowClass    : 'combo-arrow',
		disabledClass      : 'option-disabled',
		selectedClass      : 'option-selected',
		unMatchClass       : 'unmatched',
		markerClass        : 'combo-marker',
		themeClass         : '',
		maxHeight          : 200,
		extendStyle        : true,
		focusInput         : true,
		datajson           : [],
	};
	var keys = {
		ESC: 27,
		RETURN: 13,
		LEFT: 37,
		UP: 38,
		RIGHT: 39,
		DOWN: 40,
		ENTER: 13,
		SHIFT: 16
	},	
	isMobile = (/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()));
	/**
	 * Constructor
	 * @param {[Node]} element [Select element]
	 * @param {[Object]} options [Option object]
	 */
	function Plugin (element, options ) {
		/* Name of the plugin */
		this._name = pluginName;
		/* Reverse lookup */
		this.input = element;
		/* Element */
		this.$input = $(element);
		
		/* Settings */
		this.settings = $.extend( {}, defaults, options, this.$input.data() );
		/* Defaults */
		this._defaults = defaults;
		/* Options */
		//this.$options = this.$input.find('option, optgroup')
		/* Initialize */
		this.init();
		/* Instances */
		$.fn[ pluginName ].instances.push(this);
	}
	$.extend(Plugin.prototype, {
		init: function () {
			/* Construct the comboselect */
			this._construct();
			/* Add event bindings */          
			this._events();
		},
		_construct: function(){
			var self = this;
			this.$container = this.$input.parent().addClass(this.settings.comboPluginClass);
			this.$arrow = this.$input.siblings(this.settings.comboArrowClass);
			this.$dropdown = this.$input.siblings('ul');
			if(false){
				var o = '', k = 0, p = '';
				this.selectedIndex = this.$input.prop('selectedIndex');
				this.$options.each(function(i, e){
					if(e.nodeName.toLowerCase() == 'optgroup'){
						return o+='<li class="option-group">'+this.label+'</li>'
					};
					if(!e.value) p = e.innerText;

					o+='<li class="'+(this.disabled? self.settings.disabledClass : "combo-item") + ' ' +(k == self.selectedIndex? self.settings.selectedClass : '')+ '" data-index="'+(k)+'" data-value="'+this.value+'">'+ (this.innerText) + '</li>';
					k++;
					
				});
				this.$dropdown.html(o);
			}
			this.$items = this.$dropdown.children();
			
			this._valued();
		},
		_events: function(){
			/* Input: focus */
			this.$container.on('focus.input', 'input', $.proxy(this._focus, this));
			this.$container.on('mouseup.input', 'input', function(e){e.preventDefault()});
			/* Input: blur */
			this.$container.on('blur.input', 'input', $.proxy(this._blur, this));
			
			/* Dropdown Arrow: click */
			this.$container.on('click.arrow', '.'+this.settings.comboArrowClass , $.proxy(this._toggle, this));
			/* Dropdown: close */
			this.$container.on('comboselect:close', $.proxy(this._close, this));
			/* Dropdown: open */
			this.$container.on('comboselect:open', $.proxy(this._open, this));

			/* HTML Click */
			$('html').off('click.comboselect').on('click.comboselect', function(){;
				$.each($.fn[ pluginName ].instances, function(i, plugin){
					plugin.$container.trigger('comboselect:close')
				})
			});
			/* Stop `event:click` bubbling */
			this.$container.on('click.comboselect', function(e){e.stopPropagation();});

			/* Input: keydown */
			this.$container.on('keydown', 'input', $.proxy(this._keydown, this));
			/* Input: keyup */
			this.$container.on('keyup', 'input', $.proxy(this._keyup, this));
			/* Dropdown item: click */
			this.$container.on('click', '.combo-item', $.proxy(this._select, this));
		},
		_keydown: function(event){
			switch(event.which){
				case keys.UP:
					this._move('up', event);
					break;
				case keys.DOWN:
					this._move('down', event);
					break;
				case keys.RIGHT:
					this._autofill(event);
					break;
				case keys.ENTER:
					this._enter(event);
					break;
				default:
					break;
			}
		},
		_keyup: function(event){
			
			switch(event.which){
				case keys.ESC:													
					this.$container.trigger('comboselect:close');
					break;
				case keys.ENTER:
				case keys.UP:
				case keys.DOWN:
				case keys.LEFT:
				case keys.RIGHT:
				case keys.SHIFT:							
					break;
				default:							
					this._filter(event.target.value);
					break;
			}
		},
		_enter: function(event){
			var item = this._getHovered();
			item.length && this._select(item);
			if(event && event.which == keys.ENTER){
				if(!item.length) {
					this._blur();
					return true;
				}
				event.preventDefault();
			};
		},
		_move: function(dir){
			var items = this._getVisible(),
				current = this._getHovered(),
				index = current.prevAll('.combo-item').filter(':visible').length,
				total = items.length;
			
			switch(dir){
				case 'up':
					index--;
					(index < 0) && (index = (total - 1));
					break;
				case 'down':							
					index++;
					(index >= total) && (index = 0);							
					break;
			}
			
			items
				.removeClass(this.settings.hoverClass)
				.eq(index)
				.addClass(this.settings.hoverClass);

			if(!this.opened) this.$container.trigger('comboselect:open');
			this._fixScroll();
		},
		_select: function(event){
			var item = event.currentTarget? $(event.currentTarget) : $(event);
			if(!item.length) return;
			var value = item.html();
			this.$input.val(value);
			this._valued();
			this.$container.trigger('comboselect:close');
		},
		_valued: function(){
			var value = this.$input.val();
			if(value==''){
				return;
			}
			var matched = this._getAll().removeClass(this.settings.selectedClass)
			.filter(function(){
				return $(this).html() == value
			})
			.addClass(this.settings.selectedClass);
			if(matched.length>0){
				this.$input.removeClass(this.settings.unMatchClass);
			}
			else{
				this.$input.addClass(this.settings.unMatchClass);
			};
		},
		_autofill: function(){
			var item = this._getHovered();
			if(item.length){
				this.$input.val(item.html());
				var index = 
				this._selectByIndex();
			}
		},
		_filter: function(search){
			var self = this,
				items = this._getAll();
				needle = $.trim(search).toLowerCase(),
				reEscape = new RegExp('(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join('|\\') + ')', 'g'),
				pattern = '(' + search.replace(reEscape, '\\$1') + ')';
			$('.'+self.settings.markerClass, items).contents().unwrap();
			if(needle){
				this.$items.filter('.option-group, .option-disabled').hide();
				items.hide()
				.filter(function(){
						var $this = $(this),
							text = $.trim($this.text()).toLowerCase();
						
						/* Found */
						if(text.toString().indexOf(needle) != -1){
							$this
								.html(function(index, oldhtml){
								return oldhtml.replace(new RegExp(pattern, 'gi'), '$1');
							});									
							return true;
						}
					})
					.show();
			}else{
								
				this.$items.show();
			}
			/* Open the comboselect */
			this.$container.trigger('comboselect:open')
			
		},
		_highlight: function(){
			var visible = this._getVisible().removeClass(this.settings.hoverClass),
				$selected = visible.filter('.'+this.settings.selectedClass);
			if($selected.length){
				
				$selected.addClass(this.settings.hoverClass);
			}else{
				visible
					.removeClass(this.settings.hoverClass)
					.first()
					.addClass(this.settings.hoverClass);
			}
		},
		_blurSelect: function(){
			this.$container.removeClass('combo-focus');
		},
		_focus: function(event){
			this.$container.toggleClass('combo-focus', !this.opened);
			if(isMobile) return;
			if(!this.opened) this.$container.trigger('comboselect:open');
			//this.settings.focusInput && event && event.currentTarget && event.currentTarget.nodeName == 'INPUT' && event.currentTarget.select()
		},
		_blur: function(){
			this._valued();
		},
		_change: function(){
			this._valued();
		},
		_getAll: function(){
			return this.$items.filter('.combo-item');
		},
		_getVisible: function(){
			return this.$items.filter('.combo-item').filter(':visible');
		},
		_getHovered: function(){
			return this._getVisible().filter('.' + this.settings.hoverClass);
		},
		_open: function(){
			var self = this;
			this.$container.addClass('combo-open');			
			this.opened = true;
			this.settings.focusInput && setTimeout(function(){ !self.$input.is(':focus') && self.$input.focus(); });
			this._highlight();
			this._fixScroll();
			$.each($.fn[ pluginName ].instances, function(i, plugin){
				if(plugin != self && plugin.opened) plugin.$container.trigger('comboselect:close');
			})
		},
		_toggle: function(){
			this.opened? this._close.call(this) : this._open.call(this);
		},
		_close: function(){				
			this.$container.removeClass('combo-open combo-focus');
			this.$container.trigger('comboselect:closed');
			this.opened = false;
			/* Show all items */
			this.$items.show();
		},
		_fixScroll: function(){
			if(this.$dropdown.is(':hidden')) return;
			var item = this._getHovered();
			if(!item.length) return;
			var offsetTop,
				upperBound,
				lowerBound,
				heightDelta = item.outerHeight();
			offsetTop = item[0].offsetTop;
			
			upperBound = this.$dropdown.scrollTop();
			lowerBound = upperBound + this.settings.maxHeight - heightDelta;
			
			if (offsetTop < upperBound) {
					
				this.$dropdown.scrollTop(offsetTop);
			} else if (offsetTop > lowerBound) {
					
				this.$dropdown.scrollTop(offsetTop - this.settings.maxHeight + heightDelta);
			}
		},
		dispose: function(){
			this.$input.removeData('plugin_'+dataKey);
		}	
	});
	$.fn[ pluginName ] = function ( options, args ) {
		this.each(function() {
			var $e = $(this),
				instance = $e.data('plugin_'+dataKey);
			if (typeof options === 'string') {
				if (instance && typeof instance[options] === 'function') {
						instance[options](args);
				}
			}else{
				$.data( this, "plugin_" + dataKey, new Plugin( this, options ) );
			}
		});
		return this;
	};
	$.fn[ pluginName ].instances = [];
}));
