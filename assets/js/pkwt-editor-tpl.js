/**
 * PowerPlus — Elementor editor template picker
 *
 * Handles template import from within the Elementor widget panel.
 * After a successful import the Elementor preview reloads automatically
 * so the imported layout is visible immediately without leaving the editor.
 */
(function () {
	'use strict';

	var cfg = window.PKWTEditorTpl || {};

	function getAjaxUrl() { return cfg.ajaxUrl || ''; }

	/**
	 * Reload the Elementor preview iframe to show the newly imported template.
	 * We use the official Elementor JS API where available, then fall back to
	 * a direct iframe src reload.
	 */
	function reloadElementorPreview() {
		try {
			// Elementor 3.x — preferred API
			if ( window.elementor && typeof window.elementor.reloadPreview === 'function' ) {
				window.elementor.reloadPreview();
				return;
			}
			// Elementor 2.x fallback
			if ( window.elementorFrontend && window.elementorFrontend.isEditMode && window.elementorFrontend.isEditMode() ) {
				var iframe = document.getElementById( 'elementor-preview-iframe' );
				if ( iframe ) { iframe.contentWindow.location.reload(); return; }
			}
			// Last-resort: reload the whole editor page (preserves ?action=elementor)
			window.location.reload();
		} catch ( e ) {
			window.location.reload();
		}
	}

	/**
	 * Attach click-delegation to root so it works regardless of panel state.
	 */
	function attachPicker( root ) {
		root.addEventListener( 'click', function ( e ) {
			var btn = e.target.closest( '.pkwt-tpl-picker__btn' );
			if ( ! btn ) { return; }
			e.preventDefault();

			var picker   = btn.closest( '.pkwt-tpl-picker' );
			if ( ! picker ) { return; }

			var setSlug  = btn.dataset.set;
			var pageType = picker.dataset.pageType;
			var ajaxUrl  = getAjaxUrl();
			var status   = picker.querySelector( '.pkwt-tpl-picker__status' );

			if ( ! setSlug || ! pageType || ! ajaxUrl ) {
				showStatus( status, 'err', 'Configuration error — please reload the editor and try again.' );
				return;
			}

			// Lock all cards while importing.
			var allBtns = picker.querySelectorAll( '.pkwt-tpl-picker__btn' );
			allBtns.forEach( function ( b ) { b.disabled = true; } );
			btn.classList.add( 'pkwt-tpl-picker__btn--loading' );
			btn.innerHTML = '<span class="pkwt-spin">&#9696;</span> ' + escHtml( cfg.importing || 'Importing…' );

			if ( status ) {
				status.style.display = 'none';
				status.className     = 'pkwt-tpl-picker__status';
				status.innerHTML     = '';
			}

			// Step 1 — get a fresh nonce at click time (bypasses all caching)
			var nfd = new FormData();
			nfd.append( 'action', 'pkwt_get_import_nonce' );

			fetch( ajaxUrl, { method: 'POST', credentials: 'same-origin', body: nfd } )
			.then( function ( r ) { return r.json(); } )
			.then( function ( nd ) {
				if ( ! nd.success || ! nd.data || ! nd.data.nonce ) { throw { isNonceFail: true }; }

				// Step 2 — run the import with the fresh nonce
				var fd = new FormData();
				fd.append( 'action',    'pkwt_ajax_import_template' );
				fd.append( 'nonce',     nd.data.nonce );
				fd.append( 'set_slug',  setSlug );
				fd.append( 'page_type', pageType );

				return fetch( ajaxUrl, { method: 'POST', credentials: 'same-origin', body: fd } )
				.then( function ( res ) {
					return res.text().then( function ( raw ) {
						var parsed;
						try { parsed = JSON.parse( raw ); }
						catch ( ex ) { throw { isRawResponse: true, raw: raw }; }
						return parsed;
					} );
				} );
			} )
			.then( function ( data ) {
				// Re-enable buttons
				allBtns.forEach( function ( b ) { b.disabled = false; } );
				btn.classList.remove( 'pkwt-tpl-picker__btn--loading' );
				btn.innerHTML = escHtml( cfg.importLabel || 'Import' );

				if ( data.success ) {
					var msg = ( data.data && data.data.message ) ? data.data.message : ( cfg.success || 'Imported!' );

					// Build success status with reload notice
					var html = '<span class="pkwt-status-icon">&#10003;</span> <strong>' + escHtml( msg ) + '</strong>';
					html += '<span class="pkwt-status-note">' + escHtml( cfg.reloadNote || 'Reloading preview…' ) + '</span>';
					showStatus( status, 'ok', html, true );

					// Reload Elementor preview after a short delay so user can see the message
					setTimeout( function () {
						reloadElementorPreview();
					}, 1400 );

				} else {
					var errMsg = ( data.data && data.data.message ) ? data.data.message : ( cfg.error || 'Import failed.' );
					showStatus( status, 'err', '<span class="pkwt-status-icon">&#10007;</span> ' + escHtml( errMsg ), true );
				}
			} )
			.catch( function ( err ) {
				allBtns.forEach( function ( b ) { b.disabled = false; } );
				btn.classList.remove( 'pkwt-tpl-picker__btn--loading' );
				btn.innerHTML = escHtml( cfg.importLabel || 'Import' );

				var msg;
				if ( err && err.isNonceFail ) {
					msg = 'Session expired — please refresh the editor and try again.';
				} else if ( err && err.isRawResponse ) {
					msg = 'Server error: ' + String( err.raw || '' ).trim().slice( 0, 160 );
				} else {
					msg = cfg.error || 'Import failed. Please try again.';
				}
				showStatus( status, 'err', '<span class="pkwt-status-icon">&#10007;</span> ' + escHtml( msg ), true );
			} );
		} );
	}

	function showStatus( el, type, html, isHtml ) {
		if ( ! el ) { return; }
		el.style.display = 'block';
		el.className = 'pkwt-tpl-picker__status pkwt-tpl-picker__status--' + type;
		if ( isHtml ) { el.innerHTML = html; }
		else { el.textContent = html; }
	}

	function escHtml( str ) {
		return String( str )
			.replace( /&/g, '&amp;' ).replace( /</g, '&lt;' )
			.replace( />/g, '&gt;' ).replace( /"/g, '&quot;' );
	}

	function init() {
		var panel = document.getElementById( 'elementor-panel' );
		attachPicker( panel || document.body );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
}());
