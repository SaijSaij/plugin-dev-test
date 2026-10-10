/**
 * Templates of the plugin's instance: its own templates despite theme
 * templates with the same name, exact access and the cache directory.
 */
const { test, expect } = require( '../fixtures' );

const { IS_RELEASE, restResponse } = require( '../helpers' );

async function get( requestUtils, route ) {
	const response = await restResponse( requestUtils, {
		path: `/plugin-dev-test/v1/twig/${ route }`,
		as: 'anonymous',
	} );
	expect( response.status ).toBe( 200 );
	return response.body;
}

test.describe( 'Plugin templates', () => {
	test( 'renders its own template although the theme has one with the same name', async ( {
		requestUtils,
	} ) => {
		expect( await get( requestUtils, 'partial' ) ).toBe(
			'<p class="partial partial--plugin">World</p>\n'
		);
	} );

	test( 'addresses its own templates exactly with @plugin', async ( {
		requestUtils,
	} ) => {
		expect( await get( requestUtils, 'overridable-exact' ) ).toBe(
			'<p class="overridable">from plugin-dev-test</p>\n'
		);
	} );

	test(
		IS_RELEASE
			? 'caches compiled templates per release below the slug'
			: 'caches compiled templates below the slug',
		async ( { requestUtils } ) => {
			const cache = await get( requestUtils, 'cache' );

			expect( cache.directory ).toBe(
				'wp-content/cache/plugin-dev-test/twig'
			);
			expect( cache.compiled ).toBeGreaterThan( 0 );
			if ( IS_RELEASE ) {
				// twig/{templateVersion}, plus a level for the versions of the theme if it overrides templates.
				const [ version, ...rest ] = cache.directories;
				expect( version ).toMatch( /^[0-9a-f]{16}$/ );
				for ( const directory of rest ) {
					expect( directory ).toMatch(
						new RegExp( `^${ version }/[0-9a-f]{16}(/|$)` )
					);
				}
			} else {
				// Twig's own hash directories.
				for ( const directory of cache.directories ) {
					expect( directory ).toMatch( /^[0-9a-f]{2}$/ );
				}
			}
		}
	);
} );
