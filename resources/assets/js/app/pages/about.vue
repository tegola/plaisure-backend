<script>
import _extend from 'lodash/extend';
import constants from 'prontogioco/constants';

export default {
	name: 'PgAboutPage',

	created() {
		_extend(this, constants);
	}
}
</script>

<template>
<div class="pg-about-page">
	<pg-navbar />

	<div class="container my-5">
		<div class="row section">
			<div class="col-md-10 mx-md-auto col-lg-8">
				<h2 class="font-weight-bold mb-3 text-center">{{ $t('about.company.title', { name: APP_NAME }) }}</h2>
				<p>{{ $t('about.company.paragraph1', { name: APP_NAME }) }}</p>
				<p>{{ $t('about.company.paragraph2', { name: APP_NAME }) }}</p>
				<p>{{ $t('about.company.paragraph3') }}</p>
				<p>{{ $t('about.company.paragraph4', { name: APP_NAME }) }}</p>
				<p>{{ $t('about.company.paragraph5', { name: APP_NAME }) }}</p>

				<div class="text-center mt-5">
					<h3 class="font-weight-bold mb-3" id="contact">{{ $t('about.contact.title') }}</h3>
					<p>{{ $t('about.contact.intro') }}</p>
					<p>
						{{ $t('about.contact.info') }}<br>
						<a :href="`mailto:${EMAIL_GENERIC}`"><strong>{{ EMAIL_GENERIC }}</strong></a>
					</p>
					<p>
						{{ $t('about.contact.venues') }}<br>
						<a :href="`mailto:${EMAIL_VENUES}`"><strong>{{ EMAIL_VENUES }}</strong></a>
					</p>
					<p>
						{{ $t('about.contact.report') }}<br>
						<a :href="`mailto:${EMAIL_REPORT}`"><strong>{{ EMAIL_REPORT }}</strong></a>
					</p>
				</div>
			</div>
		</div>
	</div>

	<pg-page-footer />
</div>
</template>