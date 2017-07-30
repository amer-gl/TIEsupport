// ============ Tooltip ============
$(function () {
    $( '[data-toggle="tooltip"]' ).tooltip()
})

// ============ SELECT STYLE ============
$(document).ready(function() {
    $(".notifications ul.notifications-list").niceScroll({
	    cursorcolor: "#050505", // change cursor color in hex
	    cursoropacitymin: 0.4, // change opacity when cursor is inactive (scrollabar "hidden" state), range from 1 to 0
	    cursorwidth: "6.5px", // cursor width in pixel (you can also write "5px")
	    cursorborder: "none", // css definition for cursor border
	    cursorborderradius: "0", // border radius in pixel for cursor
	});
    $( '.support-form .remember label' ).on( 'click', function(e) {
    	e.preventDefault();
    	if ( $('#remember_me').val() == 0 ){
    		$('#remember_me').val( '1' );
    	} else {
    		$('#remember_me').val( '0' );
    	}
    	$( this ).toggleClass( 'check' );
    	$( '.support-form .remember label span' ).toggleClass( 'check' );
    });

});

// ============ SELECT STYLE ============
$( '.select-wrapper .select-dropdown' ).on( 'click', function(e) {
	$( this ).siblings( 'ul' ).slideToggle( 'fast' );
	e.stopPropagation();
});
$( '.select-wrapper' ).on( 'click', function(e) {
	e.stopPropagation();
});
$( 'html' ).click(function() {
	$( '.dropdown-content' ).slideUp( 'fast' );
});

$( '.select-wrapper .dropdown-content li' ).not( '.disabled' ).on( 'click', function(e) {
	$( this ).addClass( 'active' );
	$( this ).siblings( 'li' ).removeClass( 'active' );
	$( '.select-wrapper .select-dropdown' ).val( $( this ).text() );
	$( this ).parent( 'ul' ).fadeOut( 'fast' );
});


// ============ Topnav Toggle Menu Button ============
$( '.toggle-menu' ).on( 'click', function(){
  $( '.top-nav .tiequde-navigation ul' ).slideToggle();
  $( this ).toggleClass( 'close-menu' );
});
// ============ Support Notifications ============
var check = 0,
	log   = 1,
	checkNoti = 0,
	ajaxurl = 'http://www.tiequde.com/support/includes/ajax.php';
	regdata = '',
	logdata = '';

$( '.notifications .num' ).on( 'click', function( e ) {
	$(this).siblings('.notifications-container').toggle();
	redirectTicket();
	$(this).removeClass( 'new' );
	$.ajax({
		url: ajaxurl,
		type: 'POST',
		data: {
			action: 'tiequde_update_noti'
		},
	});
	e.stopPropagation();
});

$( '.notifications .notifications-container li a.redirect-ticket' ).on( 'click', function( e ) {
	$( '.notifications .notifications-container' ).hide();
});

$( '.notifications' ).bind( 'click', function( e ) {
	e.stopPropagation();
});
$( 'html' ).click(function() {
   $( '.notifications .notifications-container' ).hide();
});


// ============ Support Message Toggle ============
function toggleMessage() {
	$( '.messages .message-title:not(.defined)' ).on( 'click', function(){
	  $(this).siblings( '.message-content' ).slideToggle();
	});
	$( '.messages .message-title' ).each( function(){
	  $(this).addClass('defined');
	});
}
toggleMessage();

// ============ Support Login ============
;
	// -> Check Purchase Code
	$( '.support-form .next-register-button button' ).on( 'click', function(e){

		e.preventDefault();
		var purchase = $(this).parent().parent().find('#purchase_code').val();
	    if( purchase != '' ){
	    	$.get('http://marketplace.envato.com/api/edge/TIEQUDE/yhzjs1jopt8dp9g9ptpyyigxi078t413/verify-purchase:' + purchase + '.xml', function(data) {

		        if( $(data).find('item-id').html() != null ) {
		        	$.ajax({
		        		url: $('.support-form').data('url'),
		        		type: 'POST',
		        		data: {
		        			purchase: purchase,
		        			action: 'tiequde_check_purchase'
		        		},
		        		success: function (response) {
		        			if( response != 'exist' ) {
				        		$( '#register-step2' ).append('<input type="hidden" name="item_id" id="item_id" value="'+ $(data).find('item-id').text() +'" /><input type="hidden" name="purchase" id="purchase" value="'+ purchase +'" /><input type="hidden" name="support_time" id="support_time" value="'+ $(data).find('supported-until').text() +'" />');
					        	$( '.support-form.register-form-step2' ).fadeIn(300);
								$( '.support-form.login' ).animate({
									'min-height' : '350px',
									'padding' : '20px 0'
								}, 300);

								check = 1;

				        	} else {
				        		$( '.support-form .errors' ).html('<div class="error"><span class="error-icon"><i class="ion-close"></i></span><span class="error-message">Sorry, This purchase code has been used by another person</span></div>');
								$( '.support-form .error-bg' ).addClass( 'show' ).removeClass( 'success' );
								$( '.error-forget-password' ).addClass('hide');
								$( '.support-form .toggle-errors' ).show();
								$( '.support-form .register, .support-form .back-login' ).addClass( 'hide' );

				        	}
		        		}
		        	});
		        	
		        } else {
		        	$( '.support-form .errors' ).html('<div class="error"><span class="error-icon"><i class="ion-close"></i></span><span class="error-message">Sorry, Purchase Code is invalid !</span></div>');
					$( '.support-form .error-bg' ).addClass( 'show' ).removeClass( 'success' );
					$( '.error-forget-password' ).addClass('hide');
					$( '.support-form .toggle-errors' ).show();
					$( '.support-form .register, .support-form .back-login' ).addClass( 'hide' );
		        }
		    });
		} else {
			$( '.support-form .errors' ).html('<div class="error"><span class="error-icon"><i class="ion-close"></i></span><span class="error-message">Sorry, Purchase Code Can\'t Be Empty</span></div>');
			$( '.support-form .error-bg' ).addClass( 'show' ).removeClass( 'success' );
			$( '.error-forget-password' ).addClass('hide');
			$( '.support-form .toggle-errors' ).show();
			$( '.support-form .register, .support-form .back-login' ).addClass( 'hide' );
		}
	});

	// Login & Register Proccessing
	$( '.support-form .login-button button, .support-form .register-button button' ).on( 'click', function(e){
		e.preventDefault();
		
		log = 1;
		ajaxurl = $('.support-form').data('url');
		$( this ).parent().siblings('.support-input').each(function(){ if( $( this ).find( 'input' ).val() == '' ) { log = 0 } });

		if( $(this).attr('id') == 'login-button' ){
		 	logdata = $(this).parent().parent().serialize();
			logdata += '&action=tiequde_user_login';
			$.ajax({
				url: ajaxurl,
				type : 'post',
				data : logdata,
				beforeSend: function() { 
					if( log == 1 ) {
						e.preventDefault();
						$( '.support-form .check-information' ).fadeIn('fast');
					}
				   
			   	},
				error : function( response ){
					setTimeout( function() {
						$( '.support-form .check-information' ).fadeOut('fast');
						console.log(response);
					}, 1000);	
				},
				success : function( response ){
					setTimeout( function() {
						$( '.support-form .check-information' ).fadeOut('fast');
						if( response != '' ) {
							var res = $.parseJSON(response);
							if( res.errors !== '' ) {
								$( '.support-form .errors' ).html(res.errors);
								$( '.support-form .error-bg' ).addClass( 'show' ).removeClass( 'success' );
								$( '.support-form .toggle-errors' ).show();
								$( '.support-form .register, .support-form .back-login' ).addClass( 'hide' );
							} else {
								$( '.support-form .errors' ).html(res.success);
								$( '.support-form .error-bg' ).addClass( 'show' ).addClass( 'success' );
								$( '.support-form .toggle-errors' ).show();
								$( '.support-form .register, .support-form .back-login' ).addClass( 'hide' );

								setTimeout( function () {
									location.reload();
								}, 2000 );						
							}
						}
					}, 1000);	
				}		
			});
		}

		if( $(this).attr('id') == 'register-button' ){
			// var d = new Date();
			// d = d.getFullYear() + '-' + d.getMonth() + '-' + d.getDate();
			regdata = $(this).parent().parent().serialize();		
			regdata = regdata + '&action=tiequde_user_register';

			$.ajax({
				url: ajaxurl,
				type : 'POST',
				data : regdata,
				beforeSend: function() { 
					if( log == 1 ) {
						e.preventDefault();
						$( '.support-form .check-information' ).fadeIn('fast');
					}   
			   	},
				error : function( response ){
					console.log(response);
				},
				success : function( response ){
					setTimeout( function() {
						$( '.support-form .check-information' ).fadeOut('fast');
						if( response != '' ) {
							res = $.parseJSON(response);
							if( res.errors !== '' ) {
								$( '.support-form .errors' ).html(res.errors);
								$( '.support-form .error-bg' ).addClass( 'show' ).removeClass( 'success' );
								$( '.support-form .toggle-errors' ).show();
								$( '.support-form .register, .support-form .back-login' ).addClass( 'hide' );
							} else {
								$( '.support-form .errors' ).html(res.success);
								$( '.support-form .error-bg' ).addClass( 'show' ).addClass( 'success' );
								$( '.support-form .toggle-errors' ).show();
								$( '.support-form .register, .support-form .back-login' ).addClass( 'hide' );
								setTimeout( function () {
									location.reload();
								}, 2000 );
							}
						}
					}, 1000);
					
				}		
			});
		}

	});
	$( '.support-form .register' ).on( 'click', function(){
		$( '.support-form.register-form' ).fadeIn(300);
		if( check == 0 ) {
			$( '.support-form.login' ).animate({
				'min-height' : '250px',
				'padding' : '0'
			}, 300);
		}
		$(this).hide();
		$('.support-form .back-login').show();
	});
	$( '.back-login' ).on( 'click', function(){
		$( '.support-form.register-form' ).fadeOut(300);
		$( '.support-form.login' ).animate({
			'min-height' : '350px',
			'padding' : '20px 0'
		}, 300);
		$(this).hide();
		$('.support-form .register').show();
	});
	$( '.support-form .support-input input, .ticket-form textarea' ).blur( function(e){
		if( $( this ).val() == '' ) { $( this ).siblings( 'span' ).show() } else { $( this ).siblings( 'span' ).hide(); }
	});
	$( '.support-form .close-error' ).on( 'click', function() {
		$( '.support-form .error-bg' ).removeClass( 'show' );
		$( '.support-form .toggle-errors' ).hide();
		$( '.support-form .register, .support-form .back-login' ).removeClass( 'hide' );
		$( '.support-form .check-information' ).fadeOut('fast');
		setTimeout( function() {
			$( '.support-form.new-ticket-form .error-container' ).hide();
		}, 300);
	});

	// Notificatios Actions
	function get_notification(){
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'tiequde_get_noti'
			},
			success: function (response) {
				if( response != '' ){
					var noti = $.parseJSON(response);
					if ( noti.status == 0 && check == 0 ) {
						$('.notifications .num').siblings('.notifications-container').find( 'ul.notifications-list' ).html((noti.noti_list == '' ? '<li class="transition text-center"><a href=""><div class="title-container"><span class="title">You doesn\'t have any notifications yet!</span></div></a></li>' : noti.noti_list ));
						$('.widget-noti .widget-content .no-content ul').html((noti.noti_list == '' ? '<li class="transition text-center"><a href=""><div class="title-container"><span class="title">You doesn\'t have any notifications yet!</span></div></a></li>' : noti.noti_list ));
						redirectTicket();
						check = 1;
					} else if( noti.status > 0  ) {
						$('.notifications .num').siblings('.notifications-container').find( 'ul.notifications-list' ).html(noti.noti_list);
						$('.widget-noti .widget-content .no-content ul').html(noti.noti_list);
						redirectTicket();
						$('.notifications .num').addClass( 'new' );

					}
					setTimeout( get_notification, 5000);
				}
			}
		});
		
	}
	get_notification();

	// Insert New Ticket
	$( '#new-ticket' ).on('click', function(e) {
		e.preventDefault();
		var ticket_inf = $( '.ticket-form' ).serialize();
		ticket_inf = ticket_inf.replace(/%0D%0A/g, '<br>');
		ticket_inf = ticket_inf + '&action=tiequde_new_ticket';

		$.ajax({
			url: ajaxurl,
			type : 'POST',
			data : ticket_inf,
			beforeSend: function() { 
				$( '.support-form .check-information' ).fadeIn('fast');
		   	},
			error : function( response ){
				console.log(response);
			},
			success : function( response ){
				res = $.parseJSON(response);
				setTimeout( function() {
					if( res.errors !== '' ) {
						$( '.support-form .error-container' ).show();
						$( '.support-form .errors' ).html(res.errors);
						$( '.support-form .error-bg' ).addClass( 'show' ).removeClass( 'success' );
						$( '.support-form .toggle-errors' ).show();
					} else {
						redirect_ticket_chat( res.ticket_id );
					}
				}, 1500);
				
			}		
		});

	});

	// Close Ticket
	function closeTicket(){
		$( '.close-ticket' ).on('click', function(e) {
			$.ajax({
				url: ajaxurl,
				type : 'POST',
				data : {
					action: 'tiequde_close_ticket'
				},
				error : function( response ){
					console.log(response);
				},
			});
		});
	}
	closeTicket();

	// Redirect to Ticket
	function redirect_ticket_chat( id ) {
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				ticket_id: id,
				action: 'tiequde_redirect_chat'
			},
			success: function( main_response ) {
				setTimeout( function () {
					location.reload();
				}, 1150 );
			}
		});
	}

// ============ Send Message Animation ============
$('.send .buttons .send-message').on( 'click', function( e ) {
	e.preventDefault();
	var content = $('#message_editor').val();
	var content = content.replace(/\n/g, '<br>');
	var messageTitle = $('.message_header').html();
	var admin = $('.message_header').data( 'bool' );
	var message = '<div class="message last ' + ( admin == 1 ? 'admin' : '' ) + '"><div class="message-title">'+ messageTitle +'<div class="right pull-right"><div class="date">now</div></div><div class="clear"></div></div> <!-- .message-title --><div class="message-content">'+ content +'</div> <!-- .message-content --></div>';
	$('.messages .message.last' ).removeClass('last').addClass('before-last').after(message);
	$('.last').hide();
	$('.last').slideDown('fast');	
	toggleMessage();
	$('#message_editor').val('');
	$('#message_editor').focus();

	if( content != '' ){
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'tiequde_insert_message',
				message: content,
			},
			success: function (argument) {
				console.log( argument );
			}
		});
	} else {
		$('#message_editor').focus();
	}
});
var chatCheck = 0;
function get_chat_messages(){
	$.ajax({
		url: ajaxurl,
		type: 'POST',
		data: {
			action: 'tiequde_get_messages'
		},
		success: function (response) {
			if( response != '' ){
				var res = $.parseJSON( response );
				var chatMessages = $( '.messages.wraper' ).html();
				if( undefined != chatMessages ) {
					chatMessages = (chatMessages.match(/defined/g) || []).length;
					if( chatMessages != res.num ) {
						for( var i = chatMessages ; i < (res.chat).length; i++ ){
							$( '.messages.wraper' ).append( res.chat[ i ] );
						}
					}
					
				}
				if( chatCheck == 0 ) { 
					$( '.messages.wraper' ).html( res.chat );
					chatCheck = 1;
				}
				toggleMessage();
				if( res.status == 'open' ){
					setTimeout( get_chat_messages, 5000);
				}
			}
		}
	});
}
get_chat_messages();

// Support Admin Dashboard ==========================
function get_tickets(){
	$.ajax({
		url: ajaxurl,
		type: 'POST',
		data: {
			action: 'tiequde_get_tickets'
		},
		success: function (response) {
			$( '.support-table tbody' ).html( response );
			redirectTicket();
			setTimeout( get_tickets, 5000);
		}
	});
}
get_tickets();

$( '.redirect-dashboard' ).on('click', function(e) {
	$( 'main > .check-information' ).fadeIn('fast');
	$.ajax({
		url: ajaxurl,
		type: 'POST',
		data: { action: 'tiequde_deredirect_chat' },
		success: function() {
			location.reload();
		}
	});
	
	
});

function redirectTicket() {
	$( '.redirect-ticket:not(.defined)' ).on('click', function(event) {
		event.preventDefault();
		var id = $(this).data('id');
		$( 'main > .check-information' ).fadeIn('fast');
		redirect_ticket_chat( id );
	});
	$( '.redirect-ticket' ).addClass( 'defined' );
}
redirectTicket();