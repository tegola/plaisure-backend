/* global require, google */

import $ from 'jquery';
import Vue from 'vue';
import * as VueGoogleMaps from 'vue2-google-maps';
import Tether from 'tether';

import SearchForm from './components/search-form.vue';

// Register Tether as a global var before requiring bootstrap
window.Tether = Tether;

require('bootstrap');
require('bootstrap-3-typeahead');

// Support for showing geolocation controls
$('html').addClass(navigator.geolocation ? 'has-geolocation' : 'no-geolocation');

// Register Vue Google Maps
Vue.use(VueGoogleMaps, {
	load: {
		key: pg.constants.googleMapsApiKey
	}
});

// ALL PAGES ------------------------------------------------------------------
$(function(){
	$('[data-toggle="tooltip"]').tooltip();
});

// HOME -----------------------------------------------------------------------

var $homePage = $('.page-home .page-content');

if ($homePage) {
	new Vue({
		el: $homePage[0],

		components: {
			'pg-search-form': SearchForm
		},

		data: {
			lat: pg.lat,
			lng: pg.lng,
			what: pg.what,
			near: pg.near,

			mapOptions: {
				disableDefaultUI: true,
				scrollwheel: false,
				draggable: false,
				disableDoubleClickZoom: true,
				styles: [
					{ // Remove color
						'stylers': [{ 'saturation': -100 }, { 'gamma': 0.5 }]
					},
					{ // Remove labels
						'elementType': 'labels',
						'stylers': [{ 'visibility': 'off' }]
					},
					{ // Less visible highways
						'featureType': 'road.highway',
						'stylers': [{ 'lightness': 50 }]
					},
					{ // Thinner roads
						'featureType': 'road',
						'elementType': 'geometry.stroke',
						'stylers': [{ 'weight': 0.3 }]
					}
				]
			}
		},

		computed: {
			mapCenter() {
				if (this.lat && this.lng) {
					return {
						lat: this.lat,
						lng: this.lng
					};
				} else {
					// Default to italy
					return {
						lat: 41.2053112,
						lng: 8.0860841
					};
				}
			},
			mapZoom() {
				return (this.lat && this.lng) ? 15 : 5;
			}
		},
		methods: {
			onLocationUpdate(lat, lng, near) {
				this.lat = lat;
				this.lng = lng;
				this.near = near;
			}
		}
	});
}

// EXPLORE --------------------------------------------------------------------
/*
$(function(){
	// Stop if page was not found
	if (!$('.page-explore').length) {
		return;
	}

	var $form = $('.form-search');
	var lat = $form.find('[name=lat]').val();
	var lng = $form.find('[name=lng]').val();
	var coords = new google.maps.LatLng(lat, lng);
	var currentTooltip;
	var hoverTimer;

	// Build the map
	var map = new google.maps.Map($('.map')[0], {
		center: coords,
		zoom: 15,
		mapTypeControl: false,
		streetViewControl: false
	});

	// Add venue markers
	$('[data-lat][data-lng]').each(function(index, item){
		var data = $(item).data();
		var coords = new google.maps.LatLng(data.lat, data.lng);

		// Add marker
		var marker = new google.maps.Marker({
			position: coords,
			map: map
		});

		// Prepare the function to show tooltips
		var showTooltip = function(tooltipToShow){
			if (currentTooltip) {
				if (currentTooltip == tooltipToShow) {
					return;
				}	
				currentTooltip.close();
			}
			tooltipToShow.open(map, marker);
			currentTooltip = tooltipToShow;
		};

		// Prepare tooltip
		var tooltip = new google.maps.InfoWindow({
			content: [data.lat, data.lng].join(',')
		});

		// Show tooltip on marker and list item hover
		marker.addListener('click', function(){
			showTooltip(tooltip);
		});
		$(item).on('mouseenter', function(){
			clearTimeout(hoverTimer);
			hoverTimer = setTimeout(function(){
				showTooltip(tooltip);
			}, 400);
		});
		$(item).on('mouseleave', function(){
			clearTimeout(hoverTimer);
		});
	});

	// Make the "load more" button actually load inline
	$('[data-action="load-more"]').on('click', function(){
		var button = $(this);
		var resultsContainer = $('#results');
		var url = button.attr('href');

		$.get(url).then(function(data){
			// Update url
			history.pushState({}, '', url);

			// Update list
			var resultsHtml = $('#results', data)[0].innerHTML;
			resultsContainer.append(resultsHtml);

			// Update load more button
			var newButton = $('[data-action="load-more"]', data);
			if (!newButton || newButton.attr('href') == url) {
				button.remove();
			} else {
				button.attr('href', newButton.attr('href'));
			}

			// FIXME: Update map
		});

		return false;
	});
});
*/

// DETAIL ---------------------------------------------------------------------

/*
var $detailPage = $('.page-detail .page-content');

if ($detailPage) {
	var $map = $('.map');
	var coords = new google.maps.LatLng($map.data('lat'), $map.data('lng'));

	new google.maps.Map($map[0], {
		center: coords,
		zoom: 15,
		scrollwheel: false,
		mapTypeControl: false,
		streetViewControl: false
	});
}
*/