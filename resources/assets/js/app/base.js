import $ from 'jquery';

// Support for showing geolocation controls
$('html').addClass(navigator.geolocation ? 'has-geolocation' : 'no-geolocation');

// Start tooltips
$('[data-toggle="tooltip"]').tooltip();