/*
 * Ajax Plugin for Flash Album Gallery
 * Version:  1.0.0
 */ 
(function($) {
flagAjax = {
		settings: {
			url: flagAjaxSetup.url, 
			type: "POST",
			action: flagAjaxSetup.action,
			operation : flagAjaxSetup.operation,
			nonce: flagAjaxSetup.nonce,
			ids: flagAjaxSetup.ids,
			permission: flagAjaxSetup.permission,
			error: flagAjaxSetup.error,
			failure: flagAjaxSetup.failure,
			timeout: 10000,
			//wait: false
		},
	
		run: function( index ) {
			s = this.settings;
			var req = $.ajax({
				type: "POST",
			   	url: s.url,
			   	data:"action=" + s.action + "&operation=" + s.operation + "&_wpnonce=" + s.nonce + "&image=" + s.ids[index],
			   	cache: false,
			   	timeout: 10000,
			   	success: function(msg){
			   		switch (msg) {
			   			case "-1":
					   		flagProgressBar.addNote( flagAjax.settings.permission );
						break;
			   			case "0":
					   		flagProgressBar.addNote( flagAjax.settings.error );
						break;
			   			case "1":
					   		// show nothing, its better
						break;
						default:
							// Return the message
							flagProgressBar.addNote( "<strong>ID " + flagAjax.settings.ids[index] + ":</strong> " + flagAjax.settings.failure, msg );
						break; 			   			
			   		}

			    },
			    error: function (msg) {
					flagProgressBar.addNote( "<strong>ID " + flagAjax.settings.ids[index] + ":</strong> " + flagAjax.settings.failure, msg );
				},
				complete: function () {
					index++;
					flagProgressBar.increase( index );
					// parse the whole array
					if (index < flagAjax.settings.ids.length)
						flagAjax.run( index );
					else 
						flagProgressBar.finished();
				} 
			});
		},
	
		init: function( s ) {

			var index = 0;	
					
			// get the settings
			this.settings = $.extend( {}, this.settings, {}, s || {} );

			// start the ajax process
			this.run( index );			
		}
	}

}(jQuery));