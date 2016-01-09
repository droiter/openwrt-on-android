(function($) {
	$.widget('oc.addnew', {
		options: {
			width: 'auto',
			height: 'auto',
			closeOnEscape: true,
			autoOpen: false,
			autoClose: true
		},
		_create: function() {
			//console.log('ocaddnew._create', this);
			var self = this;

			this.originalCss = {
				display: this.element[0].style.display,
				width: this.element[0].style.width,
				height: this.element[0].style.height
			};

			this.originalTitle = this.element.attr('title') || this.element.attr('original-title');
			//console.log('ocaddnew, originalTitle', this.originalTitle);
			this.options.title = this.options.title || this.originalTitle;
			//console.log('ocaddnew, title', this.options.title);
			this.element.hide();

			this.$ul = $('<ul class="oc-addnew" />').insertBefore(this.element).css('display', 'inline-block');
			$('<a class="oc-addnew-init"></a>').text(this.options.title).appendTo(this.$ul).wrap('<li />');
			this.element.addClass('oc-addnew-name').removeAttr('original-title').appendTo(this.$ul).wrap('<li />');
			//console.log('li', $li.parent());
			//$li.appendTo(this.$ul);
			$('<button />').addClass('new-button primary icon-checkmark-white').insertAfter(this.element).hide();
			this.element.on('input', function() {
				// Enable button when input is non-empty
				$(this).next('button').prop('disabled', $(this).val().trim().length === 0);
			});
			this.$ul.on('click keydown',function(event) {
				if(self._wrongKey(event)) {
					return;
				}
				if($(event.target).is('.oc-addnew-init')) {
					if(self.$ul.is('.open')) {
						self.close();
					} else {
						self.open();
					}
					return false;
				} else if($(event.target).is('button.primary')) {
					var result = self.element.val().trim();
					if(result.length > 0) {
						self._trigger('ok', null, result);
						self.element.val('');
					}
					return false;
				}
			});

			$(document).on('click keydown keyup', function(event) {
				if(self._isOpen && self.options.autoClose
					&& event.target !== self.$ul.get(0)
					&& self.$ul.find($(event.target)).length === 0) {
					//console.log('outside', $(event.target), self.$ul.find(event.target));
					self.close();
					self._trigger('cancel');
					return;
				}
				// Escape
				if(event.keyCode && event.keyCode === 27 && self.options.closeOnEscape) {
					self.close();
					self._trigger('cancel');
					return false;
				}
			});

			this.element.on('keydown keyup', function(event) {
				// Enter
				if(event.keyCode && event.keyCode === 13) {
					event.stopPropagation();
					event.stopImmediatePropagation();
					if(event.type === 'keyup') {
						event.preventDefault();
						return false;
					}
					if(event.target === self.element.get(0)) {
						self.element.next('button').trigger('click');
					}
					return false;
				}
			});
			//console.log('ocaddnew._create done', this);
			this._setOptions(this.options);
			this._isOpen = false;
		},
		_init: function() {
			//console.log('ocaddnew._init');
			if(this.options.autoOpen) {
				this.open();
			}
		},
		_setOption: function(key, value) {
			switch(key) {
				case 'width':
					this.$ul.css('width', value);
					break;
				case 'height':
					this.$ul.css('height', value);
					break;
			}
			//this._super(key, value);
			$.Widget.prototype._setOption.apply(this, arguments );
		},
		_setOptions: function() {
			//console.log('_setOptions', options);
			//this._super(options);
			$.Widget.prototype._setOptions.apply(this, arguments);
		},
		_wrongKey: function(event) {
			return ((event.type === 'keydown' || event.type === 'keypress')
				&& (event.keyCode !== 32 && event.keyCode !== 13));
		},
		widget: function() {
			return this.$ul;
		},
		close: function() {
			if(!this._isOpen) {
				return;
			}
			//console.log('ocaddnew.close()');
			this.$ul.removeClass('open');
			this.$ul.find('li:not(:first-child)').hide();
			this.$ul.find('li:first-child').show();
			this._isOpen = false;
			this._trigger('close');
		},
		open: function() {
			if(this._isOpen) {
				return;
			}
			//console.log('ocaddnew.open()', this.element.parent('li'), this.$ul);
			this.$ul.addClass('open');
			this.$ul.find('li:first-child').hide();
			this.element.show().next('button').show().parent('li').show();
			this.element.focus();
			if(this.options.addTo) {
			}
			this._isOpen = true;
			this._trigger('open');
		},
		destroy: function() {
			//console.log('destroy');
			if(this.originalTitle) {
				this.element.attr('title', this.originalTitle);
			}
			this.element.removeClass('oc-addnew-name')
					.css(this.originalCss).detach().insertBefore(this.$ul);
			this.$ul.remove();
		}
	});
}(jQuery));
