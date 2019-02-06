<script>
import _extend from 'lodash/extend';
import constants from '@/constants';

export default {
	name: 'PgPromotePage',

	meta() {
		return {
			title: this.$t('pages.promote.meta_title')
		};
	},

	created() {
		_extend(this, constants);
	}
};
</script>

<template>
	<div class="pg-promote-page">
		<pg-navbar />

		<div class="jumbotron jumbotron-fluid mb-0">
			<div class="container text-center">
				<div class="row">
					<div class="col-10 col-lg-9 mx-auto">
						<h1 class="jumbotron-title">{{ $t('pages.promote.hero.title') }}</h1>
						<i18n tag="p" class="jumbotron-text" path="pages.promote.hero.paragraph">
							<span place="name">{{ APP_NAME }}</span>
							<strong place="strong">{{ $t('pages.promote.hero.paragraph_strong') }}</strong>
						</i18n>
					</div>
				</div>
			</div>
		</div>

		<div class="container">
			<div class="section">
				<div class="row my-5">
					<div class="col-md-10 mx-md-auto col-lg-9">
						<p class="mb-0">{{ $t('pages.promote.example.paragraph1') }}</p>
					</div>
				</div>

				<figure class="figure">
					<img src="/img/promote/example.png" class="figure-img img-fluid rounded">
				</figure>

				<div class="row">
					<div class="col-md-10 mx-md-auto col-lg-9">
						<i18n tag="p" path="pages.promote.example.paragraph2">
							<span v-for="n in 4" :key="n" :place="n" class="badge badge-pill badge-primary">{{ n }}</span>
						</i18n>
					</div>
				</div>

				<div class="row mb-5">
					<div class="col-md-10 mx-md-auto col-lg-9">
						<p>{{ $t('pages.promote.example.paragraph3') }}</p>
						<p>{{ $t('pages.promote.example.paragraph4', { name: APP_NAME }) }}</p>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-10 mx-md-auto col-lg-9">
					<div class="section mb-5">
						<h2 class="section-title text-center">{{ $t('pages.promote.cost.title') }}</h2>
						<i18n tag="p" path="pages.promote.cost.paragraph1">
							<span place="name">{{ APP_NAME }}</span>
							<strong place="strong">{{ $t('pages.promote.cost.paragraph1_strong') }}</strong>
						</i18n>
						<i18n tag="p" path="pages.promote.cost.paragraph2">
							<a :href="`mailto:${EMAIL_VENUES}`" place="contact">{{ $t('pages.promote.cost.paragraph2_contact') }}</a>
						</i18n>
					</div>

					<div class="section my-5">
						<h2 class="section-title text-center">{{ $t('pages.promote.start.title') }}</h2>
						<p>{{ $t('pages.promote.start.paragraph1') }}</p>
						<i18n tag="p" path="pages.promote.start.paragraph2">
							<span place="name">{{ APP_NAME }}</span>
							<strong place="strong1">{{ $t('pages.promote.start.paragraph2_strong1') }}</strong>
							<a :href="`mailto:${EMAIL_VENUES}`" place="email"><strong>{{ EMAIL_VENUES }}</strong></a>
							<strong place="strong2">{{ $t('pages.promote.start.paragraph2_strong2') }}</strong>
						</i18n>
						<p class="text-center my-5">
							<a :href="`mailto:${EMAIL_VENUES}`" class="btn btn-lg btn-primary">{{ $t('pages.promote.contact') }}</a>
						</p>
					</div>
				</div>
			</div>
		</div>

		<pg-page-footer />
	</div>
</template>