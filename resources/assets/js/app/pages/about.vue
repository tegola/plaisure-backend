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
				<h2 class="font-weight-bold mb-3 text-center">Che cos'è {{ APP_NAME }}</h2>
				<p>{{ APP_NAME }} è una start-up che combina le capacità tecnologiche di web e di design da un lato, e l'esperienza nel settore gioco a livello italiano e mondiale dall'altro.</p>
				<p>Nel panorama mondiale del settore gioco mancava un servizio come {{ APP_NAME }}, dove si fanno incontrare i due attori della filiera &mdash; chi cerca e chi offre gioco lecito &mdash; garantendo un livello sempre più alto per gli ospiti delle case da gioco, che potranno scegliere, anche attraverso {{ APP_NAME }}, dove passare il proprio prezioso tempo libero.</p>
				<p>L'utente potrà consultare tutte le informazioni come ad esempio il numero di macchine, la tipologia di slot machines e VLT, di giochi live, gli orari di apertura, la ristorazione, le scommesse, ecc., i benefit e gli eventi che la sala da gioco ha da offrire.</p>
				<p>Il gestore, attraverso {{ APP_NAME }}, potrà comunicare con potenziali clienti con una semplicità senza precedenti nel settore gioco.</p>
				<p>{{ APP_NAME }} è sensibile al gioco responsabile dando una visibilità preferenziale alle case da gioco sicure e con personale qualificato con attestati di frequenza a corsi per contrastare il Gioco d'Azzardo Patologico (GAP).</p>

				<div class="text-center mt-5">
					<h3 class="font-weight-bold mb-3" id="contact">Contattaci</h3>
					<p>Scrivici a uno dei seguenti indirizzi. Sarà nostra cura risponderti al più presto.</p>
					<p>
						Per informazioni generiche:<br>
						<a :href="`mailto:${EMAIL_GENERIC}`"><strong>{{ EMAIL_GENERIC }}</strong></a>
					</p>
					<p>
						Per aggiungere o rivendicare un'attività:<br>
						<a :href="`mailto:${EMAIL_VENUES}`"><strong>{{ EMAIL_VENUES }}</strong></a>
					</p>
					<p>
						Per segnalare un errore:<br>
						<a :href="`mailto:${EMAIL_REPORT}`"><strong>{{ EMAIL_REPORT }}</strong></a>
					</p>
				</div>
			</div>
		</div>
	</div>

	<pg-page-footer />
</div>
</template>