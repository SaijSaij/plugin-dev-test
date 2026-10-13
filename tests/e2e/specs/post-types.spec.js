/**
 * Post types and meta of the plugin: its own post type at the limit of 20
 * characters, its own meta on posts of WordPress, independent of the meta of
 * the theme.
 */
const { test, expect } = require( '../fixtures' );

const { cleanUp, restResponse, trackPost } = require( '../helpers' );

test.describe( 'Post types and meta of the plugin', () => {
	test.afterEach( async ( { requestUtils } ) => {
		await cleanUp( requestUtils );
	} );

	test( 'registers its post type and reads its meta through its own classes', async ( {
		requestUtils,
	} ) => {
		const type = await restResponse( requestUtils, {
			path: '/wp/v2/types/plugin_dev_test_note',
		} );
		expect( type.body ).toMatchObject( {
			slug: 'plugin_dev_test_note',
			name: 'Notes',
		} );

		const note = await requestUtils.rest( {
			method: 'POST',
			path: '/wp/v2/plugin_dev_test_note',
			data: {
				title: 'E2E note',
				meta: { plugin_dev_test_priority: 3 },
			},
		} );
		trackPost( note.id, 'plugin_dev_test_note' );
		const read = await restResponse( requestUtils, {
			path: `/plugin-dev-test/v1/posts/notes/${ note.id }`,
		} );
		expect( read.body ).toEqual( {
			class: 'Saij\\PluginDevTest\\Post\\Note',
			priority: 3,
		} );
	} );

	test( 'keeps its meta on posts apart from the meta of the theme', async ( {
		requestUtils,
	} ) => {
		const schema = await restResponse( requestUtils, {
			method: 'OPTIONS',
			path: '/wp/v2/posts',
		} );
		const keys = Object.keys(
			schema.body.schema.properties.meta.properties
		);
		expect( keys ).toContain( 'plugin_dev_test_stars' );

		const post = await requestUtils.rest( {
			method: 'POST',
			path: '/wp/v2/posts',
			data: {
				title: 'E2E rated',
				status: 'publish',
				meta: { plugin_dev_test_stars: 4 },
			},
		} );
		trackPost( post.id, 'posts' );
		const rating = await restResponse( requestUtils, {
			path: `/plugin-dev-test/v1/posts/rating/${ post.id }`,
		} );
		expect( rating.body ).toEqual( { stars: 4 } );
		// The plugin registers only its own key on posts; the theme's keys are named after its namespace.
		expect(
			keys.filter( ( key ) => key.startsWith( 'plugin_dev_test_' ) )
		).toEqual( [ 'plugin_dev_test_stars' ] );
	} );
} );
