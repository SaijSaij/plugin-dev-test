/**
 * A shared module with the same name as one of the test theme: the instances never share it
 * (document-views.spec.js).
 */
const mounted = [];

/**
 * @param {string} name
 * @return {number} Place of the view in this instance, starting at 1.
 */
export function take( name ) {
	mounted.push( name );
	return mounted.length;
}
