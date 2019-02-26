jQuery(document).ready(function() {
	
	//store actual border color
	var recCss = jQuery('form[name="checkout"]').children().find(':input[validate]').first().css('border-color');
	var patCss = jQuery('form[name="checkout"]').children().find(':input[pattern]').first().css('border-color');
	//attach handler to be triggered by woocommerce
	jQuery('form[name="checkout"]').on('checkout_place_order', function(e) {		
		//e.preventDefault(e);
		$patternsearch = jQuery(this);
		var count = 0;
		var validated = true;
		jQuery(this).children().find(':input[validate]').each(function() {		
			var val = jQuery(this).val();
			//required is handled by woocomerce
			if (val != "" && val != null) {
				switch (jQuery(this).attr('validate')) {
					case "email":
						//simple check
						var rx = /^[\w\.-]+@[\w\.-]+\.\w+$/i;
						if (!rx.test(val)) {
							validated = false;
							jQuery(this).css( "border-color", "red" );
						}
						else jQuery(this).css( "border-color", recCss );
						break;
					case "date":
						//simple check for different date formats
						if (!dateval(val,{format:"yyyy-mm-dd"}) && !dateval(val,{format:"mm-dd-yyyy"}) && !dateval(val,{format:"dd-mm-yyyy"}) && !dateval(val,{format:"yyyy/mm/dd"}) && !dateval(val,{format:"mm/dd/yyyy"})  && !dateval(val,{format:"dd/mm/yyyy"})) {
							validated = false;
							jQuery(this).css( "border-color", "red" );
						}
						else jQuery(this).css( "border-color", recCss );
						break;
					case "time":
						//simple check for different time formats
						if (!checktime(val,{format:"HH:MM:SS T"}) && !checktime(val,{format:"HH:MM T"}) && !checktime(val,{format:"HH:MM"}) && !checktime(val,{format:"HH:MM:SS"})) {
							validated = false;
							jQuery(this).css( "border-color", "red" );
						}
						else jQuery(this).css( "border-color", recCss );
						break;
					case "number":
						if (isNaN(parseFloat(val))) {
							validated = false;
							jQuery(this).css( "border-color", "red" );
						}
						else jQuery(this).css( "border-color", recCss );
						break;
					case "float":
						if (!floatval(val, {none: false})) {
							jQuery(this).css( "border-color", "red" );
							validated = false;
						}
						else jQuery(this).css( "border-color", recCss );
						break;
					case "integer":
						if (!integerval(val, {allowNegative: true})) {
							jQuery(this).css( "border-color", "red" );
							validated = false;
						}
						else jQuery(this).css( "border-color", recCss );
						break;
					case "custom1":
					case "custom2":
					case "custom3":
						
						break;
						
				}
			}
			//pattern validation for safari as long as safari does not support pattern attribute
			var is_safari = navigator.userAgent.indexOf("Safari") > -1;
			if ( is_safari ) {
				$patternsearch.children().find(':input[pattern]').each(function() {		
					if(!jQuery(this).prop('pattern')) { 
						//nothing right now
					}
					else {
						if( ( !jQuery(this).val().match( new RegExp( jQuery(this).attr('pattern') ) ) ) ) {
							jQuery(this).css( "border-color", "red" );
							validated = false;			
						}				
						else {
							jQuery(this).css( "border-color", patCss );
						}
					}
				});
			}
			
		});

		if (!validated) {	
			jQuery('html, body').animate({
				scrollTop: (jQuery('body').offset().top),
			}, 1000, 'linear', function() {
				jQuery('#wcpgsk-dialog-validation-errors').dialog( "option", "position", { center: 60 } );
				jQuery('#wcpgsk-dialog-validation-errors').dialog( 'open' );				
			});
			return false;
		} else true;
	});
	jQuery( "#wcpgsk-dialog-validation-errors" ).dialog({
		autoOpen: false,
		height: 200,
		width: 400,
		modal: true,
		//position: { of: jQuery('body') },
		draggable: false,
		focus:  function() {
			jQuery(this).find('#wcpgsk_error_message').html(jQuery(this).dialog('option', 'errormsg'));
		}
	});
	
	
	function integerval(value, options) {
		if (value == '' || value == '-' || value == '+') {
			return false;
		}
		var regExp = /^[\-\+]?\d*$/;
		if (!regExp.test(value)) {
			return false;
		}
		options = options || {allowNegative:false};
		var ret = parseInt(value, 10);
		if (!isNaN(ret)) {
			var allowNegative = true;
			if (typeof options.allowNegative != 'undefined' && options.allowNegative == false) {
				allowNegative = false;
			}
			if (!allowNegative && value < 0) {
				ret = false;
			}
		} else {
			ret = false;
		}
		return ret;
	}
	
	function floatval (value, options) {
		var regExp = /^[\+\-]?[0-9]+([\.,][0-9]+)?([eE]{0,1}[\-\+]?[0-9]+)?$/;
		if (!regExp.test(value)) {
			return false;
		}
		var ret = parseFloat(value);
		if (isNaN(ret)) {
			ret = false;
		}
		return ret;
	}
	
	function dateval(value, options) {
		var formatRegExp = /^([mdy]+)[\.\-\/\\\s]+([mdy]+)[\.\-\/\\\s]+([mdy]+)$/i;
		var valueRegExp = null;
		var formatGroups = options.format.match(formatRegExp);
		
		var valueGroups = null;
		if (formatGroups !== null) {
			var dayIndex = -1;
			var monthIndex = -1;
			var yearIndex = -1;
			for (var i=1; i<formatGroups.length; i++) {
				switch (formatGroups[i].toLowerCase()) {
					case "dd":
						dayIndex = i;
						break;
					case "mm":
						monthIndex = i;
						break;
					case "yy":
					case "yyyy":
						yearIndex = i;
						break;
				}
			}
			
			if (isNaN(parseInt(dayIndex, 10))) return false;
			else if (isNaN(parseInt(monthIndex, 10))) return false;
			else if (isNaN(parseInt(yearIndex, 10))) return false;			
			else if (dayIndex != -1 && monthIndex != -1 && yearIndex != -1) {
				var maxDay = -1;
				if (yearIndex == 1)
					valueRegExp = /^(\d{4})[\.\-\/\\\s]+(\d{1,2})[\.\-\/\\\s]+(\d{1,2})$/;
				else if (yearIndex == 3)
				   valueRegExp = /^(\d{1,2})[\.\-\/\\\s]+(\d{1,2})[\.\-\/\\\s]+(\d{4})$/;
				else return false;
				
				valueGroups = value.match(valueRegExp);   
				if (valueGroups !== null) {
					var theDay = parseInt(valueGroups[dayIndex], 10);
					var theMonth = parseInt(valueGroups[monthIndex], 10);
					var theYear = parseInt(valueGroups[yearIndex], 10);
					//alert(theDay);
					// Check month value to be between 1..12
					if (theMonth < 1 || theMonth > 12) {
						return false;
					}
					
					// Calculate the maxDay according to the current month
					switch (theMonth) {
						case 1:	// January
						case 3: // March
						case 5: // May
						case 7: // July
						case 8: // August
						case 10: // October
						case 12: // December
							maxDay = 31;
							break;
						case 4:	// April
						case 6: // Juna
						case 9: // September
						case 11: // November
							maxDay = 30;
							break;
						case 2: // February
							if ((parseInt(theYear/4, 10) * 4 == theYear) && (parseInt(theYear/100, 10) * 100 != theYear)) {
								maxDay = 29;
							} else {
								maxDay = 28;
							}
							break;
					}

					// Check day value to be between 1..maxDay
					if (theDay < 1 || theDay > maxDay) {
						return false;
					}
					var cd = new Date(theYear, theMonth - 1, theDay);
					//alert((cd.getMonth() + 1) + ':' + theMonth + ' ' + cd.getDate() + ':' + theDay);
					return cd.getMonth() + 1 == theMonth && cd.getDate() == theDay && cd.getFullYear() == theYear;
				} 
				else return false;
			}
		} else {
			return false;
		}
	}
	
	
	function checktime(value, options) {
			//	HH:MM:SS T
			var formatRegExp = /([hmst]+)/gi;
			var valueRegExp = /(\d+|AM?|PM?)/gi;
			var formatGroups = options.format.match(formatRegExp);
			var valueGroups = value.match(valueRegExp);
			//must match and have same length
			if (formatGroups !== null && valueGroups !== null) {
				if (formatGroups.length != valueGroups.length) {
					return false;
				}

				var hourIndex = -1;
				var minuteIndex = -1;
				var secondIndex = -1;
				//T is AM or PM
				var tIndex = -1;
				var theHour = 0, theMinute = 0, theSecond = 0, theT = 'AM';
				for (var i=0; i<formatGroups.length; i++) {
					switch (formatGroups[i].toLowerCase()) {
						case "hh":
							hourIndex = i;
							break;
						case "mm":
							minuteIndex = i;
							break;
						case "ss":
							secondIndex = i;
							break;
						case "t":
						case "tt":
							tIndex = i;
							break;
					}
				}
				if (hourIndex != -1) {
					var theHour = parseInt(valueGroups[hourIndex], 10);
					if (isNaN(theHour) || theHour > (formatGroups[hourIndex] == 'HH' ? 23 : 12 )) {
						return false;
					}
				}
				if (minuteIndex != -1) {
					var theMinute = parseInt(valueGroups[minuteIndex], 10);
					if (isNaN(theMinute) || theMinute > 59) {
						return false;
					}
				}
				if (secondIndex != -1) {
					var theSecond = parseInt(valueGroups[secondIndex], 10);
					if (isNaN(theSecond) || theSecond > 59) {
						return false;
					}
				}
				if (tIndex != -1) {
					var theT = valueGroups[tIndex].toUpperCase();
					if (
						formatGroups[tIndex].toUpperCase() == 'TT' && !/^a|pm$/i.test(theT) || 
						formatGroups[tIndex].toUpperCase() == 'T' && !/^a|p$/i.test(theT)
					) {
						return false;
					}
				}
				var date = new Date(2000, 0, 1, theHour + (theT.charAt(0) == 'P'?12:0), theMinute, theSecond);
				return date;
			} else {
				return false;
			}
	}
	
	
	
});

