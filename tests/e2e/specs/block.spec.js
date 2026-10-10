/**
 * The block of the plugin in the editor and on the front end.
 */
const { test, expect } = require( '../fixtures' );

const {
	block,
	cleanUp,
	openPostWith,
	storedContent,
	trackRenderRequests,
} = require( '../helpers' );

test.describe( 'Plugin block', () => {
	test.afterEach( async ( { requestUtils } ) => {
		await cleanUp( requestUtils );
	} );

	test( 'edits inline, re-renders through the plugin route and stores only the block comment', async ( {
		admin,
		editor,
		page,
		requestUtils,
	} ) => {
		const renders = trackRenderRequests( page );
		const id = await openPostWith(
			{ admin, editor, requestUtils },
			block( 'plugin-dev-test/note', { title: 'Hello' } )
		);

		const note = editor.canvas.locator(
			'[data-type="plugin-dev-test/note"]'
		);
		await expect( note ).toHaveClass( /note--info/ );
		await expect.poll( () => renders.length ).toBe( 1 );

		const title = note.locator( '.note__title' );
		await title.click();
		await page.keyboard.press( 'End' );
		await page.keyboard.type( ' & welcome' );
		const text = note.locator( '.note__text' );
		await text.click();
		await page.keyboard.press( 'ControlOrMeta+b' );
		await page.keyboard.type( 'bold' );
		await page.keyboard.press( 'ControlOrMeta+b' );
		await page.waitForTimeout( 1000 );
		expect( renders ).toHaveLength( 1 );

		// A non-inline attribute re-renders once; the panel of the plugin's editor script reflects it.
		// Without content controls the inspector has no tabs.
		await editor.openDocumentSettingsSidebar();
		await page
			.getByRole( 'combobox', { name: 'Tone' } )
			.selectOption( 'warning' );
		await expect( note ).toHaveClass( /note--warning/ );
		await expect.poll( () => renders.length ).toBe( 2 );
		await page.getByRole( 'button', { name: 'Note summary' } ).click();
		await expect(
			page.locator( '.plugin-dev-test-note-summary' )
		).toHaveText( 'Tone: warning' );

		await editor.saveDraft();
		const content = await storedContent( requestUtils, id );
		const match = content.match(
			/^<!-- wp:plugin-dev-test\/note (\{.*\}) \/-->$/
		);
		expect( match ).not.toBeNull();
		expect( JSON.parse( match[ 1 ] ) ).toEqual( {
			title: 'Hello & welcome',
			text: '<strong>bold</strong>',
			tone: 'warning',
		} );

		await page.goto( `/?p=${ id }&preview=true` );
		const front = page.locator( '.wp-block-plugin-dev-test-note' );
		await expect( front ).toHaveClass( /note--warning/ );
		await expect( front.locator( '.note__title' ) ).toHaveText(
			'Hello & welcome'
		);
		await expect( front.locator( '.note__text strong' ) ).toHaveText(
			'bold'
		);
		await expect( front.locator( '[data-field]' ) ).toHaveCount( 0 );
	} );
} );
