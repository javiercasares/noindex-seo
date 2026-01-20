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
		initFormSubmit();
		initTooltips();
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

				let collapsedCards     = JSON.parse( localStorage.getItem( 'noindexSeoCollapsed' ) || '{}' );
				collapsedCards[cardId] = isCollapsed;
				localStorage.setItem( 'noindexSeoCollapsed', JSON.stringify( collapsedCards ) );
			}
		);

		// Restore collapsed states.
		const collapsedCards = JSON.parse( localStorage.getItem( 'noindexSeoCollapsed' ) || '{}' );
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
						const optionText = $( this ).text().toLowerCase();
						const isMatch    = optionText.indexOf( searchTerm ) > -1;

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
		const totalOptions       = $( 'input[name^="noindex_seo_"]' ).not( '[name="noindex_seo_config_seoplugins"]' ).length;
		const enabledOptions     = $( 'input[name^="noindex_seo_"]:checked' ).not( '[name="noindex_seo_config_seoplugins"]' ).length;
		const recommendedOptions = $( '.noindex-seo-badge.recommended' ).length;

		$( '#noindex-seo-stat-total' ).text( totalOptions );
		$( '#noindex-seo-stat-enabled' ).text( enabledOptions );
		$( '#noindex-seo-stat-recommended' ).text( recommendedOptions );
	}

	/**
	 * Handle form submission with loading state
	 */
	function initFormSubmit() {
		$( 'form' ).on(
			'submit',
			function () {
				const submitButton = $( '#submit' );
				submitButton.addClass( 'loading' );
				submitButton.prop( 'disabled', true );
			}
		);
	}

	/**
	 * Initialize tooltips (using native browser tooltips for now)
	 */
	function initTooltips() {
		// Tooltips are handled by title attributes.
		// Can be enhanced with a tooltip library if needed.
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
