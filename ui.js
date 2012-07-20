"use strict";

(function($) {
	$(function () {
		$('legend.toggle-open').fastClick(function() {
			$(this).parent().find('.control-group').toggle();		
			$(this).find('i').toggleClass('icon-chevron-down icon-chevron-up');
		}).css({'cursor': 'pointer'}).filter('.closed').parent().children('.control-group').hide();
		$('#search-plenary-by-date').change(function() {
			$(this).closest('fieldset').find('.controls p').hide();
			$(this).closest('fieldset').find('.controls p input').prop('disabled', true);
			$('#search-plen-date-controls p').show();
			$('#search-plen-date-controls p input').prop('disabled', false);
			$('#search-plen-timeframe').hide();
			$('#search-plen-timeframe input').prop('disabled', true);
		});
		$('#search-plenary-by-mep').change(function() {
			$(this).closest('fieldset').find('.controls p').hide();
			$(this).closest('fieldset').find('.controls p input').prop('disabled', true);
			$('#search-plen-mep-controls p').show();
			$('#search-plen-mep-controls p input').prop('disabled', false);
			$('#search-plen-timeframe').hide();
			$('#search-plen-timeframe input').prop('disabled', true);
		});
		$('#search-plenary-by-keyword').change(function() {
			$(this).closest('fieldset').find('.controls p').hide();
			$(this).closest('fieldset').find('.controls p input').prop('disabled', true);
			$('#search-plen-keyword-controls p').show();
			$('#search-plen-keyword-controls p input').prop('disabled', false);
			$('#search-plen-timeframe').show();
			$('#search-plen-timeframe input').prop('disabled', false);
		});
		$('#search-plenary-by-mep').change().attr('checked', true);
	});
})(jQuery);
