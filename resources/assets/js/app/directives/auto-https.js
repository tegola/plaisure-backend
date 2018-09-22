export default {
	bind(el, binding, vnode) {
		const prefix = 'https://';
		const re = new RegExp('^http(s?)://', 'i');

		let handler = function(e) {
			const value = e.target.value;

			if (value.length > prefix.length && !re.test(value)) {
				e.target.value = prefix + value;
				vnode.elm.dispatchEvent(new CustomEvent('input'));
			}
		};

		el.addEventListener('input', handler);
	}
};