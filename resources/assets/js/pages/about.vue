<script>
import _extend from 'lodash/extend';
import constants from '@/constants';

export default {
	name: 'PgAboutPage',

	meta() {
		return {
			title: this.$t('pages.about.meta_title')
		};
	},

	created() {
		_extend(this, constants);
	}
};
</script>

<template>
	<div class="pg-about-page">
		<pg-navbar />

		<div class="container my-5">
			<div class="row section">
				<div class="col-md-10 mx-md-auto col-lg-8">
					<h2 class="font-weight-bold mb-3 text-center">{{ $t('pages.about.company.title', { name: APP_NAME }) }}</h2>
					<p>{{ $t('pages.about.company.paragraph1', { name: APP_NAME }) }}</p>
					<p>{{ $t('pages.about.company.paragraph2', { name: APP_NAME }) }}</p>
					<p>{{ $t('pages.about.company.paragraph3') }}</p>
					<p>{{ $t('pages.about.company.paragraph4', { name: APP_NAME }) }}</p>
					<p>{{ $t('pages.about.company.paragraph5', { name: APP_NAME }) }}</p>

					<div class="text-center mt-5">
						<h3 id="contact" class="font-weight-bold mb-3">{{ $t('pages.about.contact.title') }}</h3>
						<p>{{ $t('pages.about.contact.intro') }}</p>
						<p>
							{{ $t('pages.about.contact.info') }}<br>
							<a :href="`mailto:${EMAIL_GENERIC}`"><strong>{{ EMAIL_GENERIC }}</strong></a>
						</p>
						<p>
							{{ $t('pages.about.contact.venues') }}<br>
							<a :href="`mailto:${EMAIL_VENUES}`"><strong>{{ EMAIL_VENUES }}</strong></a>
						</p>
						<p>
							{{ $t('pages.about.contact.report') }}<br>
							<a :href="`mailto:${EMAIL_REPORT}`"><strong>{{ EMAIL_REPORT }}</strong></a>
						</p>
					</div>
				</div>
			</div>
		</div>

		<pg-page-footer />
	</div>
</template>