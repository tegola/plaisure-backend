export default function singularOrPlural(count, singularName, pluralName) {
	return parseInt(count) == 1 ? singularName : pluralName;
}