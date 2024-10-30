jQuery(document).ready(function($) {
	$('#iplocationtools-real-time-visitor-widget-feedback-modal').dialog({
		title: 'Quick Feedback',
		dialogClass: 'wp-dialog',
		autoOpen: false,
		draggable: false,
		width: 'auto',
		modal: true,
		resizable: false,
		closeOnEscape: false,
		position: {
			my: 'center',
			at: 'center',
			of: window
		},
				
		open: function() {
			$('.ui-widget-overlay').bind('click', function() {
				$('#iplocationtools-real-time-visitor-widget-feedback-modal').dialog('close');
			});
		},
			
		create: function() {
			$('.ui-dialog-titlebar-close').addClass('ui-button');
		},
	});

	$('.deactivate a').each(function(i, ele) {
		if ($(ele).attr('href').indexOf('iplocationtools-real-time-visitor-widget') > -1) {
			$('#iplocationtools-real-time-visitor-widget-feedback-modal').find('a').attr('href', $(ele).attr('href'));

			$(ele).on('click', function(e) {
				e.preventDefault();

				$('#iplocationtools-real-time-visitor-widget-feedback-response').html('');
				$('#iplocationtools-real-time-visitor-widget-feedback-modal').dialog('open');
			});

			$('input[name="iplocationtools-real-time-visitor-widget-feedback"]').on('change', function(e) {
				if($(this).val() == 4) {
					$('#iplocationtools-real-time-visitor-widget-feedback-other').show();
				} else {
					$('#iplocationtools-real-time-visitor-widget-feedback-other').hide();
				}
			});

			$('#iplocationtools-real-time-visitor-widget-submit-feedback-button').on('click', function(e) {
				e.preventDefault();

				$('#iplocationtools-real-time-visitor-widget-feedback-response').html('');

				if (!$('input[name="iplocationtools-real-time-visitor-widget-feedback"]:checked').length) {
					$('#iplocationtools-real-time-visitor-widget-feedback-response').html('<div style="color:#cc0033;font-weight:800">Please select your feedback.</div>');
				} else {
					$(this).val('Loading...');
					$.post(ajaxurl, {
						action: 'iplocaitontools_real_time_visitor_widget_submit_feedback',
						feedback: $('input[name="iplocationtools-real-time-visitor-widget-feedback"]:checked').val(),
						others: $('#iplocationtools-real-time-visitor-widget-feedback-other').val(),
					}, function(response) {
						window.location = $(ele).attr('href');
					}).always(function() {
						window.location = $(ele).attr('href');
					});
				}
			});
		}
	});
});