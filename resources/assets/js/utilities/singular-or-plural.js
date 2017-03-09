/**
 * Get the singular or plural name depending on the count value.
 * 
 * @param {number} count
 * @param {string} singular
 * @param {string} plural
 * 
 * @returns {string}
 */
export default function singularOrPlural(count, singular, plural) {
	return parseInt(count) == 1 ? singular : plural;
}