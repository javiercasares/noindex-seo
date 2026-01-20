/**
 * Noindex SEO - Gutenberg Sidebar Panel
 * Version: 2.0.0
 *
 * Provides a native Gutenberg sidebar panel for controlling robots directives
 * on individual posts and pages.
 *
 * @package noindex-seo
 */

(function (wp) {
	const { registerPlugin }             = wp.plugins;
	const { PluginDocumentSettingPanel } = wp.editPost;
	const { CheckboxControl, PanelRow }  = wp.components;
	const { useSelect, useDispatch }     = wp.data;
	const { createElement: el }          = wp.element;
	const { __ }                         = wp.i18n;

	/**
	 * Robots Directives Sidebar Panel Component
	 */
	const NoindexSeoSidebarPanel = function () {
		// Get current post meta values.
		const postMeta = useSelect(
			function (select) {
				return select( 'core/editor' ).getEditedPostAttribute( 'meta' ) || {};
			}
		);

		const { editPost } = useDispatch( 'core/editor' );

		// Current values.
		const override     = Boolean( postMeta._noindex_seo_override );
		const noindex      = Boolean( postMeta._noindex_seo_noindex );
		const nofollow     = Boolean( postMeta._noindex_seo_nofollow );
		const noarchive    = Boolean( postMeta._noindex_seo_noarchive );
		const nosnippet    = Boolean( postMeta._noindex_seo_nosnippet );
		const noimageindex = Boolean( postMeta._noindex_seo_noimageindex );

		// Update meta function.
		const updateMeta = function (key, value) {
			const newMeta = {};
			newMeta[key]  = value ? 1 : 0;
			editPost( { meta: newMeta } );
		};

		// Clear all directive meta values.
		const clearAllDirectives = function () {
			const directives = ['noindex', 'nofollow', 'noarchive', 'nosnippet', 'noimageindex'];
			directives.forEach(
				function (directive) {
					updateMeta( '_noindex_seo_' + directive, false );
				}
			);
		};

		// Handle override toggle.
		const handleOverrideChange = function (value) {
			updateMeta( '_noindex_seo_override', value );

			// If disabling override, clear all directives.
			if ( ! value) {
				clearAllDirectives();
			}
		};

		// Get active directives for preview.
		const activeDirectives = [];
		if (override) {
			if (noindex) {
				activeDirectives.push( 'noindex' );
			}
			if (nofollow) {
				activeDirectives.push( 'nofollow' );
			}
			if (noarchive) {
				activeDirectives.push( 'noarchive' );
			}
			if (nosnippet) {
				activeDirectives.push( 'nosnippet' );
			}
			if (noimageindex) {
				activeDirectives.push( 'noimageindex' );
			}
		}

		return el(
			PluginDocumentSettingPanel,
			{
				name: 'noindex-seo-panel',
				title: __( 'Search Engine Visibility', 'noindex-seo' ),
				icon: 'visibility',
			},
			[
				// Override checkbox.
				el(
					PanelRow,
					{ key: 'override-row' },
					el(
						CheckboxControl,
						{
							label: __( 'Override global settings', 'noindex-seo' ),
							help: __( 'Enable to set custom robots directives for this content', 'noindex-seo' ),
							checked: override,
							onChange: handleOverrideChange,
						}
					)
				),

				// Directives (shown only if override is enabled).
				override && el(
					'div',
					{
						key: 'directives-container',
						style: {
							borderTop: '1px solid #ddd',
							paddingTop: '16px',
							marginTop: '8px',
						}
					},
					[
					el(
						'p',
						{
							key: 'directives-label',
							style: {
								fontSize: '12px',
								fontWeight: '600',
								marginBottom: '12px',
								color: '#1e1e1e',
							}
						},
						__( 'Robots Directives:', 'noindex-seo' )
					),

					el(
						PanelRow,
						{ key: 'noindex-row' },
						el(
							CheckboxControl,
							{
								label: '🔍 ' + __( 'noindex', 'noindex-seo' ),
								help: __( 'Prevent search engines from indexing', 'noindex-seo' ),
								checked: noindex,
								onChange: function (value) {
									updateMeta( '_noindex_seo_noindex', value ); },
							}
						)
					),

					el(
						PanelRow,
						{ key: 'nofollow-row' },
						el(
							CheckboxControl,
							{
								label: '🔗 ' + __( 'nofollow', 'noindex-seo' ),
								help: __( 'Prevent search engines from following links', 'noindex-seo' ),
								checked: nofollow,
								onChange: function (value) {
									updateMeta( '_noindex_seo_nofollow', value ); },
							}
						)
					),

					el(
						PanelRow,
						{ key: 'noarchive-row' },
						el(
							CheckboxControl,
							{
								label: '💾 ' + __( 'noarchive', 'noindex-seo' ),
								help: __( 'Prevent cached versions in search results', 'noindex-seo' ),
								checked: noarchive,
								onChange: function (value) {
									updateMeta( '_noindex_seo_noarchive', value ); },
							}
						)
					),

					el(
						PanelRow,
						{ key: 'nosnippet-row' },
						el(
							CheckboxControl,
							{
								label: '📄 ' + __( 'nosnippet', 'noindex-seo' ),
								help: __( 'Prevent text snippets in search results', 'noindex-seo' ),
								checked: nosnippet,
								onChange: function (value) {
									updateMeta( '_noindex_seo_nosnippet', value ); },
							}
						)
					),

					el(
						PanelRow,
						{ key: 'noimageindex-row' },
						el(
							CheckboxControl,
							{
								label: '🖼️ ' + __( 'noimageindex', 'noindex-seo' ),
								help: __( 'Prevent image indexing', 'noindex-seo' ),
								checked: noimageindex,
								onChange: function (value) {
									updateMeta( '_noindex_seo_noimageindex', value ); },
							}
						)
					),

					// Preview section.
					activeDirectives.length > 0 && el(
						'div',
						{
							key: 'preview-section',
							style: {
								marginTop: '16px',
								padding: '12px',
								background: '#f0f6fc',
								border: '1px solid #0969da',
								borderRadius: '4px',
							}
						},
						[
						el(
							'p',
							{
								key: 'preview-label',
								style: {
									fontSize: '11px',
									fontWeight: '600',
									marginBottom: '8px',
									color: '#0969da',
									textTransform: 'uppercase',
								}
							},
							__( 'Active Directives:', 'noindex-seo' )
						),
						el(
							'code',
							{
								key: 'preview-code',
								style: {
									fontSize: '12px',
									display: 'block',
									padding: '8px',
									background: '#fff',
									border: '1px solid #d0d7de',
									borderRadius: '3px',
									wordBreak: 'break-word',
								}
							},
							activeDirectives.join( ', ' )
						)
						]
					)
					]
				)
			]
		);
	};

	// Register the plugin.
	registerPlugin(
		'noindex-seo-sidebar',
		{
			render: NoindexSeoSidebarPanel,
		}
	);

})( window.wp );
