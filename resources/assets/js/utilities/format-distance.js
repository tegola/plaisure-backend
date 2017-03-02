export default function formatDistance(distance) {
	if (!distance) return null

	if (distance > 10) return Math.round(distance) + ' km'
	if (distance > 1) return distance.toFixed(1) + ' km'
	if (distance < 1) return Math.round(distance * 100) + ' m'
}