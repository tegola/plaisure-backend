/**
 * Human-readable distance formatter;
 * 
 * - More tha 10km -> '15km'
 * - More than 1km -> '1.5km'
 * - Less than 1km -> '150m'
 * 
 * @param {number} distance
 * 
 * @returns {string}
 */
export default function formatDistance(distance) {
	if (!distance) return null;

	if (distance > 10) return Math.round(distance) + ' km';
	if (distance > 1) return distance.toFixed(1) + ' km';
	if (distance < 1) return Math.round(distance * 100) + ' m';
}