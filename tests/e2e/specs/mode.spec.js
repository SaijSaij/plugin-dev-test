/**
 * Development mode in wp-dev-env, release mode with the release ZIP
 * (E2E_TARGET=release, wp-dev-env scripts/release-test).
 */
const { test, expect } = require( '../fixtures' );

const { IS_RELEASE, restResponse, VENDOR_PREFIX } = require( '../helpers' );

test.describe( 'Mode', () => {
	test(
		IS_RELEASE
			? 'runs from the built manifest with prefixed dependencies'
			: 'runs from the source checkout in development mode',
		async ( { baseURL, requestUtils } ) => {
			const response = await restResponse( requestUtils, {
				path: '/plugin-dev-test/v1/mode',
				as: 'anonymous',
			} );
			expect( response.status ).toBe( 200 );
			expect( response.body ).toEqual( {
				release: IS_RELEASE,
				devCache: ! IS_RELEASE,
				frameworkClass: `${ VENDOR_PREFIX }Saij\\WpAppFramework\\Plugin`,
				twigClass: `${ VENDOR_PREFIX }Twig\\Environment`,
				// wp_app_framework/init/plugin-dev-test fired once.
				init: 1,
				directoryUri: `${ baseURL }/wp-content/plugins/plugin-dev-test`,
			} );
		}
	);
} );
