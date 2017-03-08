/**
 * Get the singular or plural name depending on the count value.
 * 
 * @param {number} count 
 * @param {string} singularName 
 * @param {string} pluralName 
 * 
 * @returns {string}
 */
export default function singularOrPlural(count, singularName, pluralName) {
	return parseInt(count) == 1 ? singularName : pluralName
}