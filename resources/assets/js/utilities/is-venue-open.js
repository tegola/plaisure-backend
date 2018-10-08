/**
 * Determines if a venue is open depending on the set of the business hours
 * passed.
 *
 * @param  {Array} hours
 * @return {Boolean}
 */
export default function (hours) {
	if (!hours || !hours.length) return false;

	const now = new Date();
	const time = now.toTimeString().split(' ')[0];
	const today = hours[now.getDay()];
	const yesterday = hours[new Date(now.getDate() - 1).getDay()];

	// Find a match in today's normal hours
	if (today.length == 2) {
		if (today[0] <= time && today[1] >= time) return true;
	} else if (today.length == 4) {
		if (today[0] <= time && today[1] >= time ||
			today[2] <= time && today[3] >= time) return true;
	}

	// Find a match in today's inverted hours, meaning the closing
	// time is in late night, and so is smaller than the opening
	// time
	if (today.length == 2) {
		if (today[1] < today[0] && today[0] <= time) return true;
	} else if (today.length == 4) {
		if (today[3] < today[0] && today[0] <= time) return true;
	}

	// Find a match in yesterday's hours by getting the previous
	// week day
	if (today.length == 2) {
		if (yesterday[1] < yesterday[0] && yesterday[1] >= time) return true;
	} else if (today.length == 4) {
		if (yesterday[3] < yesterday[0] && yesterday[3] >= time) return true;
	}

	return false;
}