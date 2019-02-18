<script>
import extend from 'lodash/extend';
import constants from '@/constants';
import { APP_LOCALE_REGION } from '@/constants';

export default {
	name: 'PgPageFooter',

	data() {
		return {
			APP_LOCALE_REGION
		};
	},

	computed: {
		year() {
			return (new Date()).getFullYear();
		}
	},

	beforeCreate() {
		extend(this, constants);
	}
};
</script>

<template>
	<footer class="pg-footer">
		<div class="container">
			<ul class="list-inline">
				<li class="list-inline-item">
					<router-link :to="{ name: 'venues.explore' }">{{ $t('components.footer.search') }}</router-link>
				</li>
				<li class="list-inline-item">
					<router-link :to="{ name: 'promote' }">{{ $t('components.footer.promote') }}</router-link>
				</li>
				<li class="list-inline-item">
					<router-link :to="{ name: 'about' }">{{ $t('components.footer.about') }}</router-link>
				</li>
				<li class="list-inline-item">
					<router-link :to="{ name: 'play-responsibly' }">{{ $t('components.footer.responsible_gaming') }}</router-link>
				</li>
			</ul>

			<div v-if="APP_LOCALE_REGION == 'GB'" class="my-5">
				<ul class="list-inline mb-0">
					<li class="list-inline-item mb-3">
						<a href="https://www.agenziadoganemonopoli.gov.it/">
							<img class="pg-footer__aams-img" src="/img/footer-aams-1.svg">
						</a>
					</li>
					<li class="list-inline-item">
						<a href="https://www.agenziadoganemonopoli.gov.it/portale/monopoli">
							<img class="pg-footer__aams-img" src="/img/footer-aams-2.svg">
						</a>
					</li>
					<li class="list-inline-item">
						<span class="badge pg-footer__age-badge" aria-hidden="true">18+</span>
						<span class="pg-footer__age-text">
							Il gioco è vietato<br>
							ai minori di 18 anni
						</span>
					</li>
				</ul>
				<p class="small">Informati sulle probabilità di vincita e sul regolamento di gioco sul sito <a href="https://www.agenziadoganemonopoli.gov.it">agenziadoganemonopoli.gov.it</a></p>
			</div>

			<pg-logo :text="false" class="pg-footer__logo" />
			<p class="mb-0">
				{{ $t('components.footer.copyright', { year, company: COMPANY_NAME }) }}<br>
				{{ $t('components.footer.vat', { number: COMPANY_VAT_NUMBER }) }}
			</p>
		</div>
	</footer>
</template>