/**
 * Settings of the plugin's instance (src/Setting/): its own option, page and
 * template global, isolated from the settings of the theme.
 */
const { test, expect } = require( '../fixtures' );

const { restResponse } = require( '../helpers' );

const OPTION = 'plugin_dev_test_general';
const PAGE = '/wp-admin/options-general.php?page=plugin-dev-test-settings';

test.describe( 'Settings', () => {
	let stored;

	// The option of the plugin is restored after each test, as it was before.
	test.beforeEach( async ( { requestUtils } ) => {
		const response = await restResponse( requestUtils, {
			path: '/plugin-dev-test/v1/settings/raw',
		} );
		expect( response.status ).toBe( 200 );
		stored = response.body;
		await restResponse( requestUtils, {
			path: '/plugin-dev-test/v1/settings/raw',
			method: 'POST',
			data: { general: null },
		} );
	} );

	test.afterEach( async ( { requestUtils } ) => {
		await restResponse( requestUtils, {
			path: '/plugin-dev-test/v1/settings/raw',
			method: 'POST',
			data: { general: stored },
		} );
	} );

	test( 'store the group in an option named after the plugin', async ( {
		requestUtils,
	} ) => {
		const settings = await restResponse( requestUtils, {
			path: '/wp/v2/settings',
		} );
		expect( settings.body[ OPTION ] ).toEqual( {
			greeting: 'Hello from the plugin',
			enabled: true,
		} );

		const values = await restResponse( requestUtils, {
			path: '/plugin-dev-test/v1/settings',
			as: 'anonymous',
		} );
		// The template global holds only the groups of the plugin.
		expect( values.body ).toEqual( {
			greeting: 'Hello from the plugin',
			enabled: true,
			html: '<p class="plugin-settings">Hello from the plugin|no layout</p>\n',
		} );
	} );

	test( 'save on its own page under Settings without touching the theme', async ( {
		page,
		requestUtils,
	} ) => {
		const before = (
			await restResponse( requestUtils, { path: '/wp/v2/settings' } )
		).body;

		await page.goto( '/wp-admin/' );
		await expect(
			page.locator(
				'#menu-settings a[href$="page=plugin-dev-test-settings"]'
			)
		).toHaveText( 'Dev Test Plugin' );

		await page.goto( PAGE );
		await expect(
			page.getByRole( 'heading', { level: 1, name: 'Dev Test Plugin' } )
		).toBeVisible();
		// One group: no tabs.
		await expect( page.getByRole( 'tab' ) ).toHaveCount( 0 );
		await page.getByLabel( 'Greeting' ).fill( 'Changed greeting' );
		await page.getByLabel( 'Greeting' ).press( 'Tab' );
		await page.getByRole( 'button', { name: 'Save' } ).click();
		await expect( page.getByTestId( 'snackbar' ) ).toHaveText(
			'Settings saved.'
		);

		const after = (
			await restResponse( requestUtils, { path: '/wp/v2/settings' } )
		).body;
		expect( after[ OPTION ].greeting ).toBe( 'Changed greeting' );
		// Options of other instances (the test theme's, if active) are unchanged.
		for ( const [ name, value ] of Object.entries( before ) ) {
			if ( name !== OPTION && name.startsWith( 'theme_dev_test_' ) ) {
				expect( after[ name ] ).toEqual( value );
			}
		}

		const values = await restResponse( requestUtils, {
			path: '/plugin-dev-test/v1/settings',
			as: 'anonymous',
		} );
		expect( values.body.greeting ).toBe( 'Changed greeting' );
	} );
} );
