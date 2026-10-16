/**
 * The plugin's framework instance next to the test theme's: separate runtimes,
 * render routes, hooks and blocks in the same post.
 */
const { test, expect } = require( '../fixtures' );

const {
	block,
	blockCount,
	cleanUp,
	createDraft,
	openPostWith,
	requireTestTheme,
	THEME_RENDER_ROUTE,
	trackRenderRequests,
} = require( '../helpers' );

const POST = [
	block( 'dev-test/hero', { headline: 'Theme hero' } ),
	block( 'plugin-dev-test/note', { title: 'Plugin note' } ),
].join( '\n\n' );

test.describe( 'Theme and plugin', () => {
	test.beforeEach( async ( { requestUtils } ) => {
		await requireTestTheme( requestUtils );
	} );

	test.afterEach( async ( { requestUtils } ) => {
		await cleanUp( requestUtils );
	} );

	test( 'load separate runtimes and render their blocks through their own routes', async ( {
		admin,
		editor,
		page,
		requestUtils,
	} ) => {
		const pluginRenders = trackRenderRequests( page );
		const themeRenders = trackRenderRequests( page, THEME_RENDER_ROUTE );
		await openPostWith( { admin, editor, requestUtils }, POST );

		await expect(
			editor.canvas.locator(
				'[data-type="dev-test/hero"] .hero__headline'
			)
		).toHaveText( 'Theme hero' );
		await expect(
			editor.canvas.locator(
				'[data-type="plugin-dev-test/note"] .note__title'
			)
		).toHaveText( 'Plugin note' );

		await expect.poll( () => pluginRenders.length ).toBe( 1 );
		await expect.poll( () => themeRenders.length ).toBe( 1 );
		expect( blockCount( pluginRenders[ 0 ] ) ).toBe( 1 );
		expect(
			JSON.parse( pluginRenders[ 0 ].postData() ).blocks[ 0 ].name
		).toBe( 'plugin-dev-test/note' );
		expect(
			JSON.parse( themeRenders[ 0 ].postData() ).blocks.map(
				( entry ) => entry.name
			)
		).toEqual( [ 'dev-test/hero' ] );

		const instances = await page.evaluate( () => ( {
			slugs: Object.keys( window.wpAppFramework ).sort(),
			pluginBlocks: Object.keys(
				window.wpAppFramework[ 'plugin-dev-test' ].config.blocks
			),
		} ) );
		// An active child theme with blocks of its own (theme-dev-test-child) adds a third instance.
		expect( instances.slugs ).toEqual(
			expect.arrayContaining( [ 'plugin-dev-test', 'theme-dev-test' ] )
		);
		expect( instances.pluginBlocks.sort() ).toEqual( [
			'plugin-dev-test/counter',
			'plugin-dev-test/note',
			'plugin-dev-test/teaser',
		] );
		await expect(
			page.locator( '#plugin-dev-test-wp-app-framework-js' )
		).toHaveCount( 1 );
		await expect(
			page.locator( '#theme-dev-test-wp-app-framework-js' )
		).toHaveCount( 1 );
	} );

	test( 'render their blocks in the same post on the front end', async ( {
		page,
		requestUtils,
	} ) => {
		const id = await createDraft( requestUtils, POST );

		await page.goto( `/?p=${ id }&preview=true` );

		await expect(
			page.locator( '.wp-block-dev-test-hero .hero__headline' )
		).toHaveText( 'Theme hero' );
		await expect(
			page.locator( '.wp-block-plugin-dev-test-note .note__title' )
		).toHaveText( 'Plugin note' );
	} );

	test( 'run the hooks of both instances', async ( { page } ) => {
		const response = await page.request.get( '/' );
		const html = await response.text();

		expect( response.status() ).toBe( 200 );
		expect( html ).toContain(
			'<meta name="plugin-dev-test-hook" content="Hello from plugin-dev-test">'
		);
		expect( html ).toContain(
			'<meta name="dev-test-hook" content="Hello from a service">'
		);
	} );
} );
