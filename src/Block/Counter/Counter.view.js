/**
 * Front-end behavior of the counter block: each click on the button counts up.
 */
import { BlockView } from '@webcodingcat/wp-app-framework/view';

export default class Counter extends BlockView {
	mount() {
		const value = this.find( '.counter__value' );
		this.listen( this.find( '.counter__button' ), 'click', () => {
			value.textContent = String( Number( value.textContent ) + 1 );
		} );
	}
}
