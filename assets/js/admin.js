/**
 * Noindex SEO - Admin Panel JavaScript
 * Version: 2.0.0
 * Modern, interactive admin interface
 *
 * @package noindex-seo
 */

(function ($) {
	'use strict';

	/**
	 * Initialize the admin panel
	 */
	function init() {
		initTabs();
		initCollapsibleCards();
		initSearch();
		initStats();
		initTooltips();
		initMethodDependentFields();
	}

	/**
	 * Initialize tabs functionality
	 */
	function initTabs() {
		$( '.noindex-seo-tab' ).on(
			'click',
			function (e) {
				e.preventDefault();

				const tabId = $( this ).data( 'tab' );

				// Update active tab.
				$( '.noindex-seo-tab' ).removeClass( 'active' );
				$( this ).addClass( 'active' );

				// Update active content.
				$( '.noindex-seo-tab-content' ).removeClass( 'active' );
				$( '#' + tabId ).addClass( 'active' );

				// Save last active tab to localStorage.
				localStorage.setItem( 'noindexSeoActiveTab', tabId );
			}
		);

		// Restore last active tab.
		const lastTab = localStorage.getItem( 'noindexSeoActiveTab' );
		if (lastTab && $( '#' + lastTab ).length) {
			$( '.noindex-seo-tab[data-tab="' + lastTab + '"]' ).click();
		}
	}

	/**
	 * Initialize collapsible card sections
	 */
	function initCollapsibleCards() {
		$( '.noindex-seo-card-header' ).on(
			'click',
			function () {
				const card = $( this ).closest( '.noindex-seo-card' );
				card.toggleClass( 'collapsed' );

				// Save collapsed state.
				const cardId      = card.attr( 'id' );
				const isCollapsed = card.hasClass( 'collapsed' );

				let collapsedCards = {};
				try {
					collapsedCards = JSON.parse( localStorage.getItem( 'noindexSeoCollapsed' ) || '{}' );
				} catch (e) {
					// If localStorage is corrupted, reset to empty object.
					collapsedCards = {};
				}
				collapsedCards[cardId] = isCollapsed;
				localStorage.setItem( 'noindexSeoCollapsed', JSON.stringify( collapsedCards ) );
			}
		);

		// Restore collapsed states.
		let collapsedCards = {};
		try {
			collapsedCards = JSON.parse( localStorage.getItem( 'noindexSeoCollapsed' ) || '{}' );
		} catch (e) {
			// If localStorage is corrupted, reset to empty object.
			collapsedCards = {};
		}
		Object.keys( collapsedCards ).forEach(
			function (cardId) {
				if (collapsedCards[cardId]) {
					$( '#' + cardId ).addClass( 'collapsed' );
				}
			}
		);
	}

	/**
	 * Initialize search/filter functionality
	 */
	function initSearch() {
		$( '.noindex-seo-search input' ).on(
			'input',
			function () {
				const searchTerm = $( this ).val().toLowerCase();

				$( '.noindex-seo-option' ).each(
					function () {
						const optionTitle       = $( this ).find( '.noindex-seo-option-title' ).text().toLowerCase();
						const optionDescription = $( this ).find( '.noindex-seo-option-description' ).text().toLowerCase();
						const directivesText    = $( this ).find( '.noindex-seo-directives' ).text().toLowerCase();
						const combinedText      = optionTitle + ' ' + optionDescription + ' ' + directivesText;
						const isMatch           = combinedText.indexOf( searchTerm ) > -1;

						$( this ).toggle( isMatch );
					}
				);

				// Show/hide empty cards.
				$( '.noindex-seo-card' ).each(
					function () {
						const visibleOptions = $( this ).find( '.noindex-seo-option:visible' ).length;
						$( this ).toggle( visibleOptions > 0 );
					}
				);
			}
		);
	}

	/**
	 * Update statistics counters
	 */
	function initStats() {
		updateStats();

		// Update stats when toggles change.
		$( 'input[type="checkbox"]' ).on(
			'change',
			function () {
				updateStats();
			}
		);
	}

	/**
	 * Update statistics display
	 */
	function updateStats() {
		const totalContexts   = $( '.noindex-seo-option' ).not( '.disabled' ).length;
		const enabledCount    = $( '.noindex-seo-directive-checkbox input[type="checkbox"]:checked' ).not( ':disabled' ).length;
		const totalDirectives = $( '.noindex-seo-directive-checkbox input[type="checkbox"]' ).not( ':disabled' ).length;

		$( '#noindex-seo-stat-total' ).text( totalDirectives );
		$( '#noindex-seo-stat-enabled' ).text( enabledCount );
		$( '#noindex-seo-stat-recommended' ).text( totalContexts );
	}

	/**
	 * Initialize tooltips (using native browser tooltips for now)
	 */
	function initTooltips() {
		// Tooltips are handled by title attributes.
		// Can be enhanced with a tooltip library if needed.
	}

	/**
	 * Initialize method-dependent field states
	 * Some fields only work with HTTP headers, not HTML meta tags
	 */
	function initMethodDependentFields() {
		const methodSelect = $( '#noindex_seo_config_method' );

		if (methodSelect.length === 0) {
			return;
		}

		/**
		 * Update field states based on selected implementation method
		 */
		function updateFieldStates() {
			const method          = methodSelect.val();
			const isHeaderEnabled = (method === 'header' || method === 'both');

			// Contexts that ONLY work with HTTP headers (non-HTML content).
			const headerOnlyContexts = [
				'attachment',    // Attachment pages (may contain PDFs, images, etc.).
				'feed',          // RSS/Atom feeds (XML, not HTML).
				'comment_feed'   // Comment feeds (XML, not HTML).
			];

			headerOnlyContexts.forEach(
				function (context) {
					// Find all directive checkboxes for this context.
					const directiveCheckboxes = $( 'input[name$="_seo_' + context + '"]' );
					const option              = directiveCheckboxes.first().closest( '.noindex-seo-option' );

					if ( ! isHeaderEnabled) {
						// Disable all directives for this context.
						directiveCheckboxes.prop( 'disabled', true );
						directiveCheckboxes.prop( 'checked', false );
						option.addClass( 'disabled' );
						option.attr( 'title', 'This option only works with HTTP Headers implementation method' );
					} else {
						// Enable all directives for this context.
						directiveCheckboxes.prop( 'disabled', false );
						option.removeClass( 'disabled' );
						option.removeAttr( 'title' );
					}
				}
			);

			// Update stats after changing field states.
			updateStats();
		}

		// Listen for method changes.
		methodSelect.on( 'change', updateFieldStates );

		// Execute on page load.
		updateFieldStates();
	}

	/**
	 * Expand all cards
	 */
	function expandAll() {
		$( '.noindex-seo-card' ).removeClass( 'collapsed' );
		localStorage.removeItem( 'noindexSeoCollapsed' );
	}

	/**
	 * Collapse all cards
	 */
	function collapseAll() {
		$( '.noindex-seo-card' ).addClass( 'collapsed' );

		const collapsedCards = {};
		$( '.noindex-seo-card' ).each(
			function () {
				collapsedCards[$( this ).attr( 'id' )] = true;
			}
		);
		localStorage.setItem( 'noindexSeoCollapsed', JSON.stringify( collapsedCards ) );
	}

	/**
	 * Show success message
	 */
	function showSuccessMessage(message) {
		const successHtml = '<div class="noindex-seo-success">' +
			'<span class="dashicons dashicons-yes-alt"></span>' +
			'<p>' + message + '</p>' +
			'</div>';

		$( '.noindex-seo-admin-wrap' ).prepend( successHtml );

		// Auto-hide after 5 seconds.
		setTimeout(
			function () {
				$( '.noindex-seo-success' ).fadeOut(
					function () {
						$( this ).remove();
					}
				);
			},
			5000
		);
	}

	/**
	 * Highlight changes
	 */
	function highlightChanges() {
		$( 'input[type="checkbox"]' ).on(
			'change',
			function () {
				const option = $( this ).closest( '.noindex-seo-option' );
				option.addClass( 'noindex-seo-changed' );

				setTimeout(
					function () {
						option.removeClass( 'noindex-seo-changed' );
					},
					2000
				);
			}
		);
	}

	/**
	 * Add keyboard shortcuts
	 */
	function initKeyboardShortcuts() {
		$( document ).on(
			'keydown',
			function (e) {
				// Ctrl/Cmd + S to save.
				if ((e.ctrlKey || e.metaKey) && e.key === 's') {
					e.preventDefault();
					$( 'form' ).submit();
				}

				// Ctrl/Cmd + F to focus search.
				if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
					if ($( '.noindex-seo-search input' ).length) {
						e.preventDefault();
						$( '.noindex-seo-search input' ).focus();
					}
				}
			}
		);
	}

	/**
	 * Initialize all features when DOM is ready
	 */
	$( document ).ready(
		function () {
			init();
			highlightChanges();
			initKeyboardShortcuts();

			// Check for success parameter in URL.
			const urlParams = new URLSearchParams( window.location.search );
			if (urlParams.get( 'updated' ) === 'true') {
				showSuccessMessage( noindexSeoAdmin.successMessage || 'Settings saved successfully!' );

				// Remove the parameter from URL without reload.
				const newUrl = window.location.pathname + window.location.hash;
				window.history.replaceState( {}, document.title, newUrl );
			}
		}
	);

	// Expose functions globally for potential external use.
	window.noindexSeoAdmin             = window.noindexSeoAdmin || {};
	window.noindexSeoAdmin.expandAll   = expandAll;
	window.noindexSeoAdmin.collapseAll = collapseAll;
	window.noindexSeoAdmin.updateStats = updateStats;

})( jQuery );
