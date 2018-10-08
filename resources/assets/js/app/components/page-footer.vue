<script>
import _extend from 'lodash/extend';
import constants from 'prontogioco/constants';
import { cityPresets } from 'prontogioco/app/static';

export default {
	name: 'PgPageFooter',

	data() {
		return {
			presets: cityPresets.map(preset => ({
				city: preset.query,
				route: {
					name: 'venues.explore',
					query: preset
				}
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
							<router-link :to="preset.route">{{ $t('components.footer.venues.in', { city: preset.city }) }}</router-link>
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