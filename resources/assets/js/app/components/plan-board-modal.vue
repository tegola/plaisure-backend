<script>
import BModal from 'bootstrap-vue/es/components/modal/modal';
import PgButton from 'prontogioco/app/components/button';

export default {
	name: 'PgPlanBoardModal',

	components: {
		BModal,
		PgButton
	},

	data() {
		return {
			open: true
		};
	}
};
</script>

<template>
	<b-modal
		v-model="open"
		lazy
		centered
		hide-header
		hide-footer
		no-fade
		size="lg">

		<div class="card-group">
			<div class="card">
				<div class="card-body">
					<p class="card-text mb-1 initialism text-nowrap">Per iniziare</p>
					<h3 class="card-title">Gratuito</h3>
					<p class="card-text lead">€0,00/mese</p>
				</div>
				<hr class="my-0">
				<div class="card-body">
					<ul class="mb-0">
						<li>Posizionamento normale nei risultati di ricerca</li>
						<li>Puoi caricare fino a <strong>5</strong> foto</li>
						<li>Mostra le attività vicine</li>
					</ul>
				</div>
			</div>

			<div class="card">
				<div class="card-body">
					<p class="card-text mb-1 initialism text-nowrap">Il preferito</p>
					<h3 class="card-title text-primary">Premium 1</h3>
					<p class="card-text lead">€39,00/mese</p>
				</div>
				<hr class="my-0">
				<div class="card-body">
					<ul class="mb-0">
						<li>Bonus di 5km nel posizionamento nei risultati di ricerca</li>
						<li>Mostra fino a <strong>20</strong> foto</li>
						<li>Nasconde le attività vicine</li>
					</ul>
				</div>
			</div>

			<div class="card">
				<div class="card-body">
					<p class="card-text mb-1 initialism text-nowrap">Da professionisti</p>
					<h3 class="card-title">Premium 2</h3>
					<p class="card-text lead">€59,00/mese</p>
				</div>
				<hr class="my-0">
				<div class="card-body">
					<ul class="mb-0">
						<li>Bonus di 10km nel posizionamento nei risultati di ricerca</li>
						<li>Puoi caricare fino a <strong>50</strong> foto</li>
						<li>Nasconde le attività vicine</li>
					</ul>
				</div>
			</div>
		</div>

	</b-modal>
</template>