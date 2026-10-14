/**
 * Step 26: static assets of the plugin – URLs below the plugin's directory,
 * an override by the active theme (overrides/plugin-dev-test/assets/), the
 * exact name `@plugin/…` and an inline SVG, through the injected service.
 */
const { test, expect } = require( '../fixtures' );

const {
	requireTestTheme,
	restResponse,
	themeHasAssets,
} = require( '../helpers' );

const PLUGIN = '/wp-content/plugins/plugin-dev-test/assets/images/';

test.describe( 'Static assets', () => {
	test.beforeEach( async ( { requestUtils } ) => {
		await requireTestTheme( requestUtils );
	} );

	test( 'are overridden by the theme unless named exactly', async ( {
		request,
		requestUtils,
	} ) => {
		const { status, body } = await restResponse( requestUtils, {
			path: '/plugin-dev-test/v1/assets',
		} );
		expect( status ).toBe( 200 );

		const path = ( url ) => new URL( url ).pathname;
		expect( path( body.plain ) ).toBe( `${ PLUGIN }plain.svg` );
		expect( new URL( body.plain ).searchParams.get( 'ver' ) ).toMatch(
			/^[0-9a-f]{12}$/
		);
		expect( path( body.exact ) ).toBe( `${ PLUGIN }badge.svg` );
		// Theme releases before assets (0.13) have no override of the badge.
		expect( path( body.badge ) ).toBe(
			( await themeHasAssets( requestUtils ) )
				? '/wp-content/themes/theme-dev-test/overrides/plugin-dev-test/assets/images/badge.svg'
				: `${ PLUGIN }badge.svg`
		);
		for ( const url of [ body.plain, body.exact, body.badge ] ) {
			expect( ( await request.get( url ) ).status() ).toBe( 200 );
		}
		expect( body.svg ).toBe(
			'<svg class="pdt-asset" xmlns="http://www.w3.org/2000/svg" width="16" height="16"><title>Plain</title><rect width="16" height="16"/></svg>'
		);
	} );
} );
