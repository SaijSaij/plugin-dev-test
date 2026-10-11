/**
 * plugin-dev-test/teaser, styled with the plugin's prefixed utilities: content
 * editors must see the same sizes, spacing and type in the editor canvas as on
 * the front end. The space between blocks is not compared here: it comes from
 * the block gap of the theme, which the test theme's suite checks. Without a
 * content size in the theme (theme.json settings.layout.contentSize), the width
 * of a block follows the page in one place and the canvas in the other, so the
 * test needs a theme that defines it (older releases of the test theme do not).
 */
const { test, expect } = require( '../fixtures' );

const {
	block,
	cleanUp,
	createDraft,
	measureBlocks,
	openPostWith,
} = require( '../helpers' );

const TEASER = {
	title: 'A <em>teaser</em> title',
	text: 'Some <strong>bold</strong> text that is long enough to wrap onto a second line of the teaser, at least on a narrow one.',
	linkLabel: 'Read more',
	linkUrl: 'https://example.com/',
};

test.describe( 'Editor and front end', () => {
	test.afterEach( async ( { requestUtils } ) => {
		await cleanUp( requestUtils );
	} );

	test( 'renders the block alike in the editor canvas and on the front end', async ( {
		admin,
		editor,
		page,
		requestUtils,
	} ) => {
		const content =
			block( 'plugin-dev-test/teaser', TEASER ) +
			block( 'plugin-dev-test/teaser', TEASER );

		const id = await createDraft( requestUtils, content );
		await page.goto( `/?p=${ id }&preview=true` );
		const contentSize = await page.evaluate( () =>
			window
				.getComputedStyle( document.body )
				.getPropertyValue( '--wp--style--global--content-size' )
		);
		test.skip(
			contentSize === '',
			'The active theme defines no content size (theme.json settings.layout.contentSize).'
		);
		const front = page.locator( '.wp-block-plugin-dev-test-teaser' );
		await expect( front ).toHaveCount( 2 );
		const expected = ( await measureBlocks( front ) ).blocks;

		await openPostWith( { admin, editor, requestUtils }, content );
		const teasers = editor.canvas.locator(
			'[data-type="plugin-dev-test/teaser"]'
		);
		await expect( teasers.nth( 1 ).locator( '.teaser__link' ) ).toHaveText(
			'Read more'
		);

		expect( ( await measureBlocks( teasers ) ).blocks ).toEqual( expected );
	} );
} );
