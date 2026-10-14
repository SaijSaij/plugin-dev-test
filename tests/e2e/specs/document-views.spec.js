/**
 * Step 26: the plugin's document view next to the test theme's: view data and
 * shared modules with the same names stay separate per instance.
 */
const { test, expect } = require( '../fixtures' );

const { requireTestTheme, themeHasAssets } = require( '../helpers' );

test.describe( 'Document views', () => {
	test.beforeEach( async ( { requestUtils } ) => {
		await requireTestTheme( requestUtils );
	} );

	test( 'run with the view data and shared modules of their own instance', async ( {
		page,
		requestUtils,
	} ) => {
		await page.goto( '/' );

		const html = page.locator( 'html' );
		await expect( html ).toHaveAttribute(
			'data-plugin-dev-test-document',
			'Hello from the plugin'
		);
		await expect( html ).toHaveAttribute(
			'data-plugin-dev-test-order',
			'1'
		);
		await expect(
			page.locator( '#plugin-dev-test-document-document-view-js' )
		).toHaveCount( 1 );

		// Theme releases before document views (0.13) have none.
		if ( ! ( await themeHasAssets( requestUtils ) ) ) {
			return;
		}
		// The theme's document view also takes the first place, in its own module.
		await expect( html ).toHaveAttribute(
			'data-dev-test-document',
			'Hello from PHP'
		);
		await expect( html ).toHaveAttribute( 'data-dev-test-order', '1' );
	} );
} );
