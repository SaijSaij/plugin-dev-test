/**
 * The plugin's stylesheet assets/styles.css, compiled with Tailwind with the prefix
 * `pdt`, loaded on the front end and in the editor canvas next to the theme's.
 */
const { test, expect } = require( '../fixtures' );

const { block, cleanUp, createDraft, openPostWith } = require( '../helpers' );

const STYLESHEET = '#plugin-dev-test-styles-css';

/**
 * Expects the computed styles of the utilities in Note.twig.
 *
 * @param {import('@playwright/test').Locator} note
 */
async function expectStyled( note ) {
	await expect( note ).toHaveCSS( 'padding-top', '16px' );
	await expect( note ).toHaveCSS( 'border-inline-start-width', '4px' );
	await expect( note ).toHaveCSS( 'border-inline-start-style', 'solid' );
}

test.describe( 'Plugin stylesheet', () => {
	test.afterEach( async ( { requestUtils } ) => {
		await cleanUp( requestUtils );
	} );

	test( 'contains only prefixed utilities and theme variables', async ( {
		request,
	} ) => {
		const response = await request.get(
			'/wp-content/plugins/plugin-dev-test/build/styles.css'
		);
		expect( response.status() ).toBe( 200 );
		const css = await response.text();

		const classes = [ ...css.matchAll( /\.([a-z][\w\\:-]*)\{/g ) ].map(
			( match ) => match[ 1 ]
		);
		expect( classes.sort() ).toEqual( [
			// Plain CSS for the block support (src/Support/), prefixed by hand.
			'pdt-surface-tinted',
			'pdt\\:border',
			'pdt\\:border-s-4',
			'pdt\\:border-solid',
			'pdt\\:flex',
			'pdt\\:flex-col',
			'pdt\\:font-semibold',
			'pdt\\:gap-3',
			'pdt\\:leading-normal',
			'pdt\\:leading-tight',
			'pdt\\:m-0',
			'pdt\\:max-w-xl',
			'pdt\\:p-4',
			'pdt\\:p-6',
			'pdt\\:px-3',
			'pdt\\:py-1',
			'pdt\\:rounded-md',
			'pdt\\:rounded-sm',
			'pdt\\:self-start',
			'pdt\\:text-sm',
			'pdt\\:text-xl',
		] );
		expect( css ).toContain( '--pdt-spacing:' );
		expect( css ).not.toMatch( /--spacing:/ );
	} );

	test( 'styles the block on the front end and in the editor canvas', async ( {
		admin,
		editor,
		page,
		requestUtils,
	} ) => {
		const id = await createDraft(
			requestUtils,
			block( 'plugin-dev-test/note', { title: 'Styled' } )
		);
		await page.goto( `/?p=${ id }&preview=true` );
		await expect( page.locator( STYLESHEET ) ).toHaveCount( 1 );
		await expectStyled( page.locator( '.wp-block-plugin-dev-test-note' ) );

		await openPostWith(
			{ admin, editor, requestUtils },
			block( 'plugin-dev-test/note', { title: 'Styled' } )
		);
		const note = editor.canvas.locator(
			'[data-type="plugin-dev-test/note"]'
		);
		await expect( note.locator( '.note__title' ) ).toHaveText( 'Styled' );
		await expect( editor.canvas.locator( STYLESHEET ) ).toHaveCount( 1 );
		await expect( page.locator( STYLESHEET ) ).toHaveCount( 0 );
		await expectStyled( note );
	} );
} );
