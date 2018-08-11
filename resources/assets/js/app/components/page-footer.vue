<script>
import _extend from 'lodash/extend';
import { stringify } from 'qs';
import constants from 'prontogioco/constants';

const presets = [
	{
		'query': 'Milano',
		'c_lat': 45.462734,
		'c_lng': 9.177732,
		'ne_lat': 45.535689,
		'ne_lng': 9.290346,
		'sw_lat': 45.389779,
		'sw_lng': 9.065118
	},
	{
		'query': 'Bologna',
		'c_lat': 44.499118,
		'c_lng': 11.331685,
		'ne_lat': 44.556199,
		'ne_lng': 11.433717,
		'sw_lat': 44.442038,
		'sw_lng': 11.229654
	},
	{
		'query': 'Roma',
		'c_lat': 41.910071,
		'c_lng': 12.535998,
		'ne_lat': 42.050546,
		'ne_lng': 12.730289,
		'sw_lat': 41.769596,
		'sw_lng': 12.341707
	},
	{
		'query': 'Napoli',
		'c_lat': 40.85398565,
		'c_lng': 14.24660234999999,
		'ne_lat': 40.9159348,
		'ne_lng': 14.353714800000034,
		'sw_lat': 40.79203649999999,
		'sw_lng': 14.139489899999944
	},
	{
		'query': 'Palermo',
		'c_lat': 38.1404854,
		'c_lng': 13.357288550000021,
		'ne_lat': 38.2194316,
		'ne_lng': 13.447156599999971,
		'sw_lat': 38.0615392,
		'sw_lng': 13.267420500000071
	}
];

export default {
	name: 'PgPageFooter',

	data() {
		return {
			presets: presets.map(preset => ({
				city: preset.query,
				url: '/venues/explore?' + stringify(preset)
			}))
		};
	},

	computed: {
		year() {
			return (new Date()).getFullYear();
		}
	},

	beforeCreate() {
		_extend(this, constants);
	}
};
</script>

<template>
	<footer class="pg-footer text-center text-lg-left">
		<div class="container">
			<div class="row">
				<div class="col-sm-4 col-lg-2">
					<h3 class="pg-footer-heading">{{ $t('components.footer.venues.title') }}</h3>
					<ul class="list-unstyled">
						<li><router-link to="/venues/explore">{{ $t('components.footer.venues.search') }}</router-link></li>
						<li v-for="preset in presets" :key="preset.city">
							<router-link :to="preset.url">{{ $t('components.footer.venues.in', { city: preset.city }) }}</router-link>
						</li>
						<li><router-link to="/promote">{{ $t('components.footer.venues.promote') }}</router-link></li>
					</ul>
				</div>
				<div class="col-sm-4 col-lg-2">
					<h3 class="pg-footer-heading">{{ $t('components.footer.company.title') }}</h3>
					<ul class="list-unstyled">
						<li><router-link to="/about">{{ $t('components.footer.company.about') }}</router-link></li>
						<li><router-link to="/about#contact">{{ $t('components.footer.company.contact') }}</router-link></li>
					</ul>
				</div>
				<div class="col-sm-4 col-lg-2">
					<h3 class="pg-footer-heading">{{ $t('components.footer.gaming.title') }}</h3>
					<ul class="list-unstyled">
						<li><router-link to="/play-responsibly#toofar">{{ $t('components.footer.gaming.responsibly') }}</router-link></li>
						<li><router-link to="/play-responsibly#rules">{{ $t('components.footer.gaming.rules') }}</router-link></li>
						<li><router-link to="/play-responsibly#myths">{{ $t('components.footer.gaming.myths') }}</router-link></li>
						<li><router-link to="/play-responsibly#help">{{ $t('components.footer.gaming.help') }}</router-link></li>
					</ul>
				</div>
				<div class="ml-lg-auto col-lg-5">
					<i18n tag="p" path="components.footer.info">
						<a href="https://www.agenziadoganemonopoli.gov.it">agenziadoganemonopoli.gov.it</a>
					</i18n>
					<ul class="list-inline pg-footer-aams-logo-list">
						<li class="list-inline-item mb-3">
							<a href="https://www.agenziadoganemonopoli.gov.it/">
								<img src="/img/footer-aams-1.svg">
							</a>
						</li>
						<li class="list-inline-item mb-3">
							<a href="https://www.agenziadoganemonopoli.gov.it/portale/monopoli">
								<img src="/img/footer-aams-2.svg">
							</a>
						</li>
						<li class="list-inline-item mb-3">
							<span class="badge pg-footer-age-badge" aria-hidden="true">18+</span>
							<i18n class="pg-footer-age-text" path="components.footer.rating">
								<br place="break">
								<span place="age">18</span>
							</i18n>
						</li>
					</ul>
				</div>
			</div>
			<div class="text-center mt-3">
				<pg-logo :text="false" class="pg-footer-logo" />
				<p class="mb-0">
					{{ $t('components.footer.copyright', { year, company: COMPANY_NAME }) }}<br>
					{{ $t('components.footer.vat', { number: COMPANY_VAT_NUMBER }) }}
				</p>
			</div>
		</div>
	</footer>
</template>