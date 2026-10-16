/**
 * Automatic excerpts with blocks of the theme and the plugin: each instance
 * adds its own blocks to the allowed excerpt blocks.
 */
const { test, expect } = require( '../fixtures' );

const {
	block,
	cleanUp,
	createPost,
	requireTestTheme,
} = require( '../helpers' );

test.describe( 'Excerpt', () => {
	test.beforeEach( async ( { requestUtils } ) => {
		await requireTestTheme( requestUtils );
	} );

	test.afterEach( async ( { requestUtils } ) => {
		await cleanUp( requestUtils );
	} );

	test( 'contains the excerpt fields of theme and plugin blocks in document order', async ( {
		requestUtils,
	} ) => {
		const id = await createPost( requestUtils, {
			title: 'Excerpt',
			status: 'publish',
			content: [
				block( 'plugin-dev-test/note', {
					title: 'Note first',
					text: 'Not in the excerpt',
				} ),
				block( 'dev-test/hero', { headline: 'Hero second' } ),
				block( 'plugin-dev-test/note', { title: 'Note third' } ),
			].join( '\n\n' ),
		} );

		const post = await requestUtils.rest( {
			path: `/wp/v2/posts/${ id }`,
		} );

		expect( post.excerpt.rendered.trim() ).toBe(
			'<p>Note first Hero second Note third</p>'
		);
	} );
} );
