"use strict";

(function($) {
	$(function () {
		$('legend.toggle-open').fastClick(function() {
			$(this).parent().find('.control-group').toggle();		
			$(this).find('i').toggleClass('icon-chevron-down icon-chevron-up');
		}).css({'cursor': 'pointer'}).filter('.closed').parent().children('.control-group').hide();
		$('#search-plenary-by-date').change(function() {
			$(this).closest('fieldset').find('.controls p').hide();
			$('#search-plen-date-controls p').show();
			$('#search-plen-timeframe').hide();
		});
		$('#search-plenary-by-mep').change(function() {
			$(this).closest('fieldset').find('.controls p').hide();
			$('#search-plen-mep-controls p').show();
			$('#search-plen-timeframe').hide();
		});
		$('#search-plenary-by-keyword').change(function() {
			$(this).closest('fieldset').find('.controls p').hide();
			$('#search-plen-keyword-controls p').show();
			$('#search-plen-timeframe').show();
		});
		$('#search-plenary-by-mep').change().attr('checked', true);
	});
})(jQuery);
