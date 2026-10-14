/**
 * The plugin's document view: reads its own view data and takes a place in its own shared module order.js,
 * next to the test theme's document view with the same names (document-views.spec.js).
 */
import { DocumentView } from '@saij/wp-app-framework/view';
import { take } from '#shared/order';

export default class PluginDocumentMarker extends DocumentView {
	mount() {
		this.element.dataset.pluginDevTestDocument =
			this.data( 'document' ).greeting;
		this.element.dataset.pluginDevTestOrder = String(
			take( 'plugin-document' )
		);
	}
}
