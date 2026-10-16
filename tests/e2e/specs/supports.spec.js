/**
 * The plugin's block support next to the test theme's: the same attribute name
 * ("surface"), but each instance adds only its own support to its own blocks.
 */
const { test, expect } = require( '../fixtures' );

const {
	block,
	cleanUp,
	createDraft,
	openPostWith,
	requireTestTheme,
	storedContent,
} = require( '../helpers' );

test.describe( 'Block support of the plugin', () => {
	test.beforeEach( async ( { requestUtils } ) => {
		await requireTestTheme( requestUtils );
	} );

	test.afterEach( async ( { requestUtils } ) => {
		await cleanUp( requestUtils );
	} );

	test( 'puts its prefixed classes on the plugin block only', async ( {
		page,
		requestUtils,
	} ) => {
		const id = await createDraft(
			requestUtils,
			[
				block( 'plugin-dev-test/note', { title: 'Plain' } ),
				block( 'plugin-dev-test/note', {
					title: 'Tinted',
					surface: 'tinted',
				} ),
				// The theme's own support (if this theme release has it) does not know "tinted".
				block( 'dev-test/hero', {
					subline: 'Theme',
					surface: 'tinted',
				} ),
			].join( '\n\n' )
		);

		await page.goto( `/?p=${ id }&preview=true` );
		const notes = page.locator( '.wp-block-plugin-dev-test-note' );
		const classes = async ( locator ) =>
			( await locator.getAttribute( 'class' ) ).split( /\s+/ );
		expect( await classes( notes.nth( 0 ) ) ).toContain(
			'pdt-surface-plain'
		);
		expect( await classes( notes.nth( 1 ) ) ).toContain(
			'pdt-surface-tinted'
		);
		await expect( notes.nth( 1 ) ).toHaveCSS(
			'background-color',
			'rgb(255, 244, 214)'
		);
		const hero = page.locator( '.wp-block-dev-test-hero' );
		expect(
			( await classes( hero ) ).filter( ( name ) =>
				name.includes( 'tinted' )
			)
		).toEqual( [] );
	} );

	test( 'adds its attribute and control to the plugin block only', async ( {
		admin,
		editor,
		page,
		requestUtils,
	} ) => {
		const id = await openPostWith(
			{ admin, editor, requestUtils },
			block( 'plugin-dev-test/note', { title: 'Note' } )
		);
		const types = await page.evaluate( () => ( {
			note: window.wp.blocks.getBlockType( 'plugin-dev-test/note' )
				.attributes,
			hero: window.wp.blocks.getBlockType( 'dev-test/hero' ).attributes,
		} ) );
		expect( types.note.surface ).toEqual( {
			type: 'string',
			enum: [ 'plain', 'tinted' ],
			default: 'plain',
		} );
		expect( types.hero.surface?.enum ?? [] ).not.toContain( 'tinted' );

		const note = editor.canvas.locator(
			'[data-type="plugin-dev-test/note"]'
		);
		await expect( note ).toHaveClass( /pdt-surface-plain/ );
		await note.locator( '.note__title' ).click();
		await editor.openDocumentSettingsSidebar();
		await page
			.getByRole( 'region', { name: 'Editor settings' } )
			.getByRole( 'tab', { name: 'Styles' } )
			.click();
		await page
			.getByRole( 'combobox', { name: 'Shade' } )
			.selectOption( 'tinted' );
		await expect( note ).toHaveClass( /pdt-surface-tinted/ );

		await editor.saveDraft();
		expect( await storedContent( requestUtils, id ) ).toContain(
			'<!-- wp:plugin-dev-test/note {"title":"Note","surface":"tinted"} /-->'
		);
	} );
} );
