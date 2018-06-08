<script>
import _extend from 'lodash/extend';
import constants from 'prontogioco/constants';

export default {
	name: 'PgPromotePage',

	created() {
		_extend(this, constants);
	}
}
</script>

<template>
<div class="pg-promote-page">
	<pg-navbar />

	<div class="jumbotron jumbotron-fluid mb-0">
		<div class="container text-center">
			<div class="row">
				<div class="col-10 col-lg-9 mx-auto">
					<h1 class="jumbotron-title">Promuovi la tua attività, è gratis!</h1>
					<p class="jumbotron-text">Sei il proprietario di un'attività inerente al mondo del gioco e vuoi promuoverla? La tua attività è già presente su {{ APP_NAME }} e vorresti ampliare le informazioni fornite ai visitatori? In entrambi i casi, <strong>sei nel <span class="text-nowrap">posto giusto!</span></strong></p>	
				</div>
			</div>
		</div>
	</div>

	<div class="container">
		<div class="section">
			<div class="row my-5">
				<div class="col-md-10 mx-md-auto col-lg-9">
					<p class="mb-0">Aggiungendo o rivendicando un'attività ti permetterà di arrivare a futuri clienti inserendo le informazioni basilari come gli orari di apertura, foto, numero e tipologia di macchine, benefit, descrizione della tua sala ed altro ancora.</p>
				</div>
			</div>

			<figure class="figure">
			  <img src="/img/promote/example.png" class="figure-img img-fluid rounded" alt="Sala d'esempio con richiami alle zone modificabili">
			</figure>

			<div class="row">
				<div class="col-md-10 mx-md-auto col-lg-9">
					<p>Qui puoi vedere l'aspetto della pagina di una sala d'esempio con informazioni aggiuntive, come foto <span class="text-nowrap"><span class="badge badge-pill badge-primary">1</span>,</span> jackpot personalizzati <span class="badge badge-pill badge-primary">2</span>, dettagli e servizi <span class="badge badge-pill badge-primary">3</span> e contatti <span class="badge badge-pill badge-primary">4</span>.</p>
				</div>
			</div>

			<div class="row mb-5">
				<div class="col-md-10 mx-md-auto col-lg-9">
					<p>Più informazioni la tua pagina offre, più potenziali clienti potrebbero essere interessati a frequentare la tua sala. Ad esempio, specificare gli orari di apertura permette a chi fa una vita notturna di sapere se la sala è aperta anche in orari meno canonici.</p>
					<p>{{ APP_NAME }} viene consultato dai giocatori che vogliono decidere dove passare il proprio tempo libero, e questi potrebbero scegliere proprio la tua sala!</p>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-10 mx-md-auto col-lg-9">
				<div class="section mb-5">
					<h2 class="section-title text-center">Quanto costa?</h2>
					<p>Assolutamente nulla! {{ APP_NAME }} offre diversi livelli di personalizzazione; <strong>aggiungere un'attività o rivendicarla per inserire le informazioni essenziali è assolutamente gratuito</strong>; non spenderai niente, a parte pochi minuti del tuo tempo.</p>
					<p>Se invece desideri evidenziare la tua attività e farla trovare più facilmente di quelle dei tuoi concorrenti, <a :href="`mailto:${EMAIL_VENUES}`"><strong>scrivici</strong></a> per provare uno dei nostri piani premium a prezzi di lancio molto vantaggiosi.</p>
				</div>

				<div class="section my-5">
					<h2 class="section-title text-center">Che aspetti? Inizia subito!</h2>
					<p>Se sei ancora indeciso, non dovresti esserlo. Aggiungere o rivendicare la tua attività porterà soltanto benefici. Il nostro staff si occuperà di tutto il necessario per aggiornare i dati, e in futuro potrai anche modificarli in autonomia!</p>
					<p>Se hai già trovato la tua attività su {{ APP_NAME }}, fai click su “<strong>Rivendica attività</strong>” nella pagina con i dettagli per contattarci. Altrimenti scrivi all'indirizzo <a :href="`mailto:${EMAIL_VENUES}`"><strong>{{ EMAIL_VENUES }}</strong></a> ricordandoti di specificare il nome dell'attività e l'indirizzo. <strong>In entrambi i casi sarai ricontattato prima possibile dal nostro staff.</strong></p>
					<p class="text-center my-5">
						<a :href="`mailto:${EMAIL_VENUES}`" class="btn btn-lg btn-primary">Contattaci</a>
					</p>
				</div>
			</div>
		</div>
	</div>

	<pg-page-footer />
</div>
</template>