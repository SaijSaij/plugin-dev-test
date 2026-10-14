/**
 * The log table of the plugin: rows saved and found through its own row
 * class, in a table of its own next to the log table of the test theme
 * (src/Table/).
 */
const { test, expect } = require( '../fixtures' );

const { restResponse } = require( '../helpers' );

test.describe( 'Tables of the plugin', () => {
	test( 'keeps its rows in a table of its own', async ( {
		requestUtils,
	} ) => {
		const message = `E2E line ${ Date.now() }`;
		const created = await restResponse( requestUtils, {
			method: 'POST',
			path: '/plugin-dev-test/v1/log',
			data: { message, count: 3 },
		} );
		expect( created.status ).toBe( 200 );
		try {
			expect( created.body.table ).toMatch( /plugin_dev_test_log$/ );
			const found = await restResponse( requestUtils, {
				path: '/plugin-dev-test/v1/log',
				params: { message },
			} );
			expect( found.body ).toEqual( {
				ids: [ created.body.id ],
				counts: [ 3 ],
			} );

			// The test theme names its log table after its own namespace; a release of the theme from before
			// tables has none.
			const theme = await restResponse( requestUtils, {
				path: '/dev-test/v1/tables/schema/log',
			} );
			if ( theme.status === 200 ) {
				expect( theme.body.name ).toBe(
					created.body.table.replace(
						'plugin_dev_test_log',
						'dev_test_log'
					)
				);
				expect( Object.keys( theme.body.columns ) ).not.toContain(
					'count'
				);
			} else {
				expect( theme.status ).toBe( 404 );
			}
		} finally {
			const deleted = await restResponse( requestUtils, {
				method: 'DELETE',
				path: `/plugin-dev-test/v1/log/${ created.body.id }`,
			} );
			expect( deleted.status ).toBe( 200 );
		}
	} );
} );
