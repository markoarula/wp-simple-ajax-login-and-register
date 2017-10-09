jQuery( document ).ready(function($) {
	"use strict";

	$( document ).on( 'click', '.mytheme_show_login', show_login )
			   .on( 'click', '.mytheme_logout', logout_user )
			   .on( 'click', '.wp_simple_ajax_login_and_register_overlay', close_login )
			   .on( 'click', '.mytheme_register', register_user )
			   .on( 'click', '.mytheme_lost_password', lost_password )
			   .on( 'click', '.go_back_to_login', go_back_to_login )
			   .on( 'click', '.go_back_to_register', go_back_to_register )
			   .on( 'click', '#mytheme_login .submit_button', ajax_login_user )
			   .on( 'click', '#mytheme_register .submit_button', ajax_registration )
			   .on( 'click', '#mytheme_forgotten_pass .submit_button', ajax_lost_pass );

	function show_login(e){
		e.preventDefault();
		$( '.wp_simple_ajax_login_and_register_overlay, #mytheme_login' ).fadeIn( 500 );
	}

	function close_login(e){
		$( '#mytheme_login, #mytheme_register, #mytheme_forgotten_pass, .wp_simple_ajax_login_and_register_overlay' ).fadeOut( 500 );
		$( '.ajax_login .status' ).html( '' );
		$( '#mytheme_login' ).hide();
		$( '#mytheme_register, #mytheme_forgotten_pass' ).removeClass( 'show' );
	}

	function logout_user(e){
		e.preventDefault();
		window.location.href = ajax_login_object.logouturl;
	}

	// Perform AJAX login on form submit.
	function ajax_login_user(e){
		e.preventDefault();
		$.ajax({
			type: 'POST',
			url: ajax_login_object.ajaxurl,
			data: {
				'action': 'mytheme_ajax_login',
				'username': $( '#mytheme_login #username' ).val(),
				'password': $( '#mytheme_login #password' ).val(),
				'security': $( '#mytheme_login #security' ).val()
			},
			beforeSend: function(){
				if ($( '.error_message' ).length) {
					$( '.error_message' ).remove();
				}
				if ($( '#username' ).hasClass( 'error' )) {
					$( '#username' ).removeClass( 'error' );
				}
				if ($( '#password' ).hasClass( 'error' )) {
					$( '#password' ).removeClass( 'error' );
				}
				$( '#mytheme_login p.status' ).html( '' );
				$( '#mytheme_login p.status' ).show().text( ajax_login_object.loadingmessage );
			},
			success: function(data){
				$( '#mytheme_login p.status' ).text( data.message );
				if ( 'user_logged' === data ) {
					window.location.reload();
				} else {
					$( '#mytheme_login p.status' ).html( '' ).hide();
					var error_array = JSON.parse( data );
					for (var i = 0; i < error_array.length; i++) {
						var single_error = error_array[i];
						var error_split = error_array[i].split( '; ' );
						var error_msg = error_split[0];
						var $error_input = $( error_split[1] );
						if ( ! $error_input.hasClass( 'error' )) {
							$error_input.addClass( 'error' );
							$error_input.after( '<div class="error_message">' + error_msg + '</div>' );
						}
					}
				}
			},
			error : function (jqXHR, textStatus, errorThrown) {
				console.log( jqXHR + ' :: ' + textStatus + ' :: ' + errorThrown );
			}
		});
	}

	function register_user(e){
		e.preventDefault();
		if ( $( '.wp_simple_ajax_login_and_register_overlay:hidden' ).length > 0 ) {
			$( '.wp_simple_ajax_login_and_register_overlay' ).fadeIn( 500 );
		}
		$( '#mytheme_login' ).hide().addClass( 'hide' );
		$( '#mytheme_register' ).fadeIn( 500 ).addClass( 'show' );
	}

	// AJAX registration.
	function ajax_registration(e){
		e.preventDefault();

		if ( '' == $( '#user_email' ).val() || '' == $( '#account_password' ).val() || '' == $( '#account_password_repeat' ).val() ) {
			$( '#mytheme_register p.status' ).text( ajax_login_object.faild_register );
			return;
		}
		if (!$( '#terms' ).is(':checked')) {
			$( '#mytheme_register p.status' ).text( ajax_login_object.term_of_use );
			return;
		}
		if ($( '#account_password' ).val() != $( '#account_password_repeat' ).val()) {
			$( '#mytheme_register p.status' ).text( ajax_login_object.pass_no_match );
			return;
		}

		$.ajax({
			type: 'POST',
			url: ajax_login_object.ajaxurl,
			data: {
				'action'           : 'mytheme_ajax_register',
				// 'user_login'       : $( '#user_login' ).val(),
				'user_email'       : $( '#user_email' ).val(),
				'terms'            : $( '#terms:checked' ).val(),
				'account_password' : $( '#account_password' ).val(),
				'user_register'    : $( '#user_register' ).val(),
			},
			beforeSend: function(){
				if ($( '.error_message' ).length) {
					$( '.error_message' ).remove();
				}
				// if ($( '#user_login' ).hasClass( 'error' )) {
				// 	$( '#user_login' ).removeClass( 'error' );
				// }
				if ($( '#user_email' ).hasClass( 'error' )) {
					$( '#user_email' ).removeClass( 'error' );
				}
				if ($( '#account_password' ).hasClass( 'error' )) {
					$( '#account_password' ).removeClass( 'error' );
				}
				if ($( '#terms' ).hasClass( 'error' )) {
					$( '#terms' ).removeClass( 'error' );
				}
				$( '#mytheme_register p.status' ).html( '' );
				$( '#mytheme_register p.status' ).show().text( ajax_login_object.loadingmessage );
			},
			success: function(data) {
				if ( 'user_registered' === data ) {
					$( '#mytheme_register p.status' ).text( ajax_login_object.success_register );
					$( '#mytheme_register p.status' ).nextAll().remove();
					// document.location.href = ajax_login_object.redirecturl;
				} else {
					var error_array = JSON.parse( data );
					if ( ajax_login_object.used_email === error_array[1] ) {
						$( '#mytheme_register p.status' ).text( ajax_login_object.used_email );
					}
					for (var i = 0; i < error_array.length; i++) {
						var single_error = error_array[i];
						var error_split = error_array[i].split( '; ' );
						var error_msg = error_split[0];
						var $error_input = $( error_split[1] );
						if ( ! $error_input.hasClass( 'error' ) ) {
							$error_input.addClass( 'error' );
							if ( 'terms' === $error_input.attr( 'id' ) ) {
								$error_input.next( 'label[for="terms"]' ).after( '<div class="error_message">' + error_msg + '</div>' );
							} else {
								$error_input.after( '<div class="error_message">' + error_msg + '</div>' );
							}
						}
					}
				}
			},
			error : function (jqXHR, textStatus, errorThrown) {
				// $( '#mytheme_register p.status' ).show().text( ajax_login_object.loadingmessage );
				console.log( jqXHR + ' :: ' + textStatus + ' :: ' + errorThrown );
			},
		});
	}

	function lost_password(e){
		e.preventDefault();
		$( '#mytheme_login' ).hide().addClass( 'hide' );
		$( '#mytheme_register' ).hide().addClass( 'hide' );
		$( '#mytheme_forgotten_pass' ).fadeIn( 500 ).addClass( 'show' );
	}

	// AJAX lost pass.
	function ajax_lost_pass(e){
		e.preventDefault();

		$.ajax({
			type: 'POST',
			url: ajax_login_object.ajaxurl,
			data: {
				'action'               : 'mytheme_lost_password',
				'user_forgotten_email' : $( '#user_forgotten_email' ).val(),
				'user_get_password'    : $( '#user_get_password' ).val(),
			},
			beforeSend: function(){
				if ($( '.error_message' ).length) {
					$( '.error_message' ).remove();
				}
				if ($( '#user_forgotten_email' ).hasClass( 'error' )) {
					$( '#user_forgotten_email' ).removeClass( 'error' );
				}
				$( '#mytheme_forgotten_pass p.status' ).html( '' );
				$( '#mytheme_forgotten_pass p.status' ).show().text( ajax_login_object.loadingmessage );
			},
			success: function(data) {
				if ( 'mail_sent' === data ) {
					$( '#mytheme_forgotten_pass p.status' ).text( ajax_login_object.mail_sent );
					window.location.reload();
				} else {
					$( '#mytheme_forgotten_pass p.status' ).html( '' ).hide();
					var error_array = JSON.parse( data );
					for (var i = 0; i < error_array.length; i++) {
						var single_error = error_array[i];
						var error_split = error_array[i].split( '; ' );
						var error_msg = error_split[0];
						var $error_input = $( error_split[1] );
						if ( ! $error_input.hasClass( 'error' ) ) {
							$error_input.addClass( 'error' ).after( '<div class="error_message">' + error_msg + '</div>' );
						}
					}
				}
			},
			error : function (jqXHR, textStatus, errorThrown) {
				console.log( jqXHR + ' :: ' + textStatus + ' :: ' + errorThrown );
			},
		});
	}

	function go_back_to_login(e){
		e.preventDefault();
		$( '#mytheme_login' ).removeClass( 'hide' ).show();
		$( '#mytheme_register' ).removeClass( 'show' ).hide();
		$( '#mytheme_forgotten_pass' ).removeClass( 'show' ).hide();
	}

	function go_back_to_register(e){
		e.preventDefault();
		$( '#mytheme_register' ).removeClass( 'hide' ).addClass( 'show' ).show();
		$( '#mytheme_forgotten_pass' ).removeClass( 'show' ).hide();
		$( '#mytheme_login' ).removeClass( 'show' ).hide();
	}

});
