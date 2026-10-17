/**
 * Editor extension of the note block: a panel of the plugin's own framework instance.
 */
import { registerPanel } from '@webcodingcat/wp-app-framework';
import { PanelBody } from '@wordpress/components';

registerPanel( 'note', ( { attributes } ) => (
	<PanelBody title="Note summary" initialOpen={ false }>
		<p className="plugin-dev-test-note-summary">{ `Tone: ${ attributes.tone }` }</p>
	</PanelBody>
) );
