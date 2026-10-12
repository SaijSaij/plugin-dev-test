/**
 * Step 14: the plugin's view scripts, mounted by its own view runtime – next
 * to the theme's view scripts if the active test theme has them.
 */
const { test, expect } = require( '../fixtures' );

const {
	block,
	cleanUp,
	createDraft,
	openPostWith,
	requireTestTheme,
	restResponse,
} = require( '../helpers' );

/**
 * Whether the active test theme registers its FAQ block, which has a view script
 * (theme releases before view scripts have none).
 *
 * @param {Object} requestUtils
 * @return {Promise<boolean>} Theme block with view script registered.
 */
async function themeHasViews( requestUtils ) {
	const response = await restResponse( requestUtils, {
		path: '/wp/v2/block-types/dev-test/faq',
	} );
	return response.status === 200;
}

test.describe( 'View scripts', () => {
	test.beforeEach( async ( { requestUtils } ) => {
		await requireTestTheme( requestUtils );
	} );

	test.afterEach( async ( { requestUtils } ) => {
		await cleanUp( requestUtils );
	} );

	test( "mount with the plugin's view runtime next to the theme's", async ( {
		page,
		requestUtils,
	} ) => {
		const withTheme = await themeHasViews( requestUtils );
		const id = await createDraft(
			requestUtils,
			[
				block( 'plugin-dev-test/counter', {
					label: 'Count',
					start: 2,
				} ),
				...( withTheme
					? [
							block( 'dev-test/faq', {
								question: 'Theme FAQ',
								answer: 'Answer',
							} ),
						]
					: [] ),
			].join( '\n\n' )
		);

		await page.goto( `/?p=${ id }&preview=true` );
		await expect(
			page.locator( '#plugin-dev-test-wp-app-framework-view-js' )
		).toHaveCount( 1 );
		await expect(
			page.locator( '#plugin-dev-test-plugin-dev-test-counter-view-js' )
		).toHaveCount( 1 );
		const counter = page.locator( '.wp-block-plugin-dev-test-counter' );
		expect(
			JSON.parse(
				await counter.getAttribute( 'data-wp-app-framework-views' )
			)
		).toEqual( {
			slug: 'plugin-dev-test',
			views: [ { view: 'Block/Counter/Counter.view' } ],
		} );
		await counter.getByRole( 'button', { name: 'Count' } ).click();
		await counter.getByRole( 'button', { name: 'Count' } ).click();
		await expect( counter.locator( 'output' ) ).toHaveText( '4' );

		if ( withTheme ) {
			// The theme's view runtime mounts its own block, the plugin's not.
			await expect(
				page.locator( '#theme-dev-test-wp-app-framework-view-js' )
			).toHaveCount( 1 );
			const faq = page.locator( '.wp-block-dev-test-faq' );
			await faq.getByRole( 'button', { name: 'Theme FAQ' } ).click();
			await expect( faq.locator( '.faq__answer' ) ).toBeVisible();
		}
	} );

	test( 'run in the preview of the editor', async ( {
		admin,
		editor,
		page,
		requestUtils,
	} ) => {
		await openPostWith(
			{ admin, editor, requestUtils },
			block( 'plugin-dev-test/counter', { label: 'Count' } )
		);
		const counter = editor.canvas.locator(
			'[data-type="plugin-dev-test/counter"]'
		);
		await counter.locator( '.counter__button' ).click();
		await page
			.getByRole( 'toolbar', { name: 'Block tools' } )
			.getByRole( 'button', { name: 'Preview', exact: true } )
			.click();
		await expect(
			counter.locator( '.counter__button' )
		).not.toHaveAttribute( 'contenteditable' );
		await counter.locator( '.counter__button' ).click();
		await expect( counter.locator( 'output' ) ).toHaveText( '1' );
	} );
} );
