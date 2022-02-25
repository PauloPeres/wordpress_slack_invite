(function( $ ) {
	'use strict';
	$( window ).load(function() {
		$('#myog-slack-invite_slack').submit('submit',function(event){
			$('#myog-slack-inviteform-button').addClass('active');
			$('#myog-slack-inviteform-button').prop('disabled', true);
		})
	});
})( jQuery );
