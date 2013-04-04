(function($) {
	
	// Note to self: remember jQuery data attributes only accessible
	// using lower case name!
	
	$.phpqti = {
		'init': function() {
			$('.qti_choiceInteraction').choiceInteraction();
			$('.qti_selectPointInteraction').selectPointInteraction();
			$('.qti_endAttemptInteraction').endAttemptInteraction();
			$('.qti_sliderInteraction').sliderInteraction();
		}	
	};
	
	// Deal with maxChoices and minChoices
	// TODO: minChoices support is poor - uses JS alert
	$.widget ( "phpqti.choiceInteraction", {
		
		options: {
			
		},
		
		_create: function() {
			var self = this;
			
			var maxChoices = Number($(self.element).data('maxchoices'));
			maxChoices = isNaN(maxChoices) ? 1 : maxChoices;
			
			var minChoices = Number($(self.element).data('minchoices'));
			minChoices = isNaN(minChoices) ? 0 : minChoices;
			
			$(self.element).on('change', '.qti_simpleChoice input:checkbox', function(el) {
				if (!maxChoices > 0) {
					return;
				}
				console.log(maxChoices);
				var choices = $(self.element).find('.qti_simpleChoice input:checkbox:checked');
				if(choices.length >= maxChoices){
					$(self.element).find('.qti_simpleChoice input:checkbox:not(:checked)').attr('disabled', true);
				} else {
					$(self.element).find('.qti_simpleChoice input:checkbox').attr('disabled', false);
				}
			});

			if (minChoices > 0) {
				$(self.element).closest('form').on('submit', function(e) {
					var choices = $(self.element).find('.qti_simpleChoice input:checkbox:checked');
					if(choices.length < minChoices){
						alert('You must select at least ' + minChoices + ' choices.');
					}
				});
			}

		}

	});
	
	// TODO: Stop this allowing fractional x & y values
	$.widget ( "phpqti.selectPointInteraction", {
		
		options: {
			
		},
		
		_create: function() {
			var self = this;
			
			self.varname = self.element.attr('id').replace(/^selectPointInteraction_/, '');

			var maxChoices = Number($(self.element).data('maxchoices'));
			maxChoices = isNaN(maxChoices) ? 1 : maxChoices;
			var minChoices = $(self.element).data('minchoices');
			minChoices = isNaN(minChoices) ? 0 : minChoices;

			var input = $(self.element).find('input:hidden');
			var currentValues = [];
			if (!input.val().match(/^\s*$/)) {
				currentValues = input.val().split(',');
			}
			
			var choices = currentValues.length;
			// show already selected values
			$.each(currentValues, function(i, o) {
				if (o == '') {
					return;
				}
				self._showSelection(o);
			});
			
			$(self.element).on('click', 'object', function(e){
				if (choices >= maxChoices) {
					alert('Only ' + maxChoices + ' choices allowed.');
					return;
				}
				choices++;
				var offset = $(this).offset();
				var x = (e.pageX - offset.left);
				var y = (e.pageY - offset.top);
				var selectedValue = x + " " + y;
				if (input.val() == '') {
					input.val(selectedValue);
				} else {
					var currentValues = input.val().split(/,/);
					currentValues.push(selectedValue);
					input.val(currentValues.join(','));
				}
				
				self._showSelection(selectedValue);

			});
			
			var resetButton = $('<input type="button" value="Remove selected">').on('click', function() {
				$('.selected', self.element).remove();
				$('input:hidden', self.element).val('');
				choices = 0;
			});
			$(self.element).append(resetButton);
		},
		
		_showSelection: function(selectedValue) {
			var self = this;
			// Draw a red pixel on the selected space
			// TODO: Use an image?
			var point = util.parsePoint(selectedValue);
			var size = '3px';
			var offset = $('object', self.element).offset();

			$(self.element).append(
				$('<div class="selected"></div>')
                .css('position', 'absolute')
                .css('top', (offset.top + point.y - 1) + 'px')
                .css('left', (offset.left + point.x - 1) + 'px')
                .css('width', size)
                .css('height', size)
                .css('background-color', 'red')
            );
		}
		
	});
	
	$.widget ( "phpqti.endAttemptInteraction", {
		
		options: {
			
		},
		
		_create: function() {
			var self = this;
			
			self.varname = self.element.attr('id').replace(/^endAttemptInteraction_/, '');

			$(self.element).on('click', 'input:submit', function(e){
				$(self.element).find('input:hidden').val('true');
			});
		}
		
	});
	
	$.widget ( "phpqti.sliderInteraction", {
		
		options: {
			
		},
		
		_create: function() {
			var self = this;
			var el = $(self.element);
			var input = $('input:hidden', el);
			var valueElement = $('.value', el);
			
			var sliderOpts = {
				min: el.data('lowerbound'),
				max: el.data('upperbound'),
				range: 'min',
				slide: function(event, ui) {
					input.val(ui.value);
					valueElement.text(ui.value);
				}
			};
			
			if (input.val()) {
				sliderOpts.value = input.val();
				valueElement.text(input.val());
			}
			
			if (el.data('step')) {
				sliderOpts.step = el.data('step');
			}
			
			if (el.data('orientation')) {
				sliderOpts.orientation = el.data('orientation');
			}
			
			$('.slider', el).slider(sliderOpts);
		}
		
	});
	
	var util = {
		
		parsePoint: function(pointString) {
			var parts = pointString.split(' ');
			var x = parseInt(parts[0]);
			var y = parseInt(parts[1]);
			if (isNaN(x) || isNaN(y)) {
				return null;
			}
			return { x: x, y: y };
		}
			
	};
	
}(jQuery));