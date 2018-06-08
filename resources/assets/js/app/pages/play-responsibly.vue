<script>
import _extend from 'lodash/extend';
import constants from 'prontogioco/constants';

export default {
	name: 'PgPlayResponsiblyPage',

	created() {
		_extend(this, constants);
	}
}
</script>

<template>
<div class="pg-play-responsibly-page">
	<pg-navbar />

	<div class="container my-5">
		<div class="row">
			<div class="col-md-10 ml-md-auto mr-md-auto">
				<div class="text-center">
					<h1 id="toofar"><strong>Gioca senza esagerare</strong></h1>
					<h3 class="mb-4">Evita gli eccessi: fai in modo che il gioco resti un piacere.</h3>
				</div>
				<p>Il gioco è un'esperienza piacevole e positiva che non rappresenta alcun rischio per la maggior parte delle persone.<br>
				<br>
				Ma tra milioni di giocatori, ci sono anche persone per le quali giocare non è più solo un divertimento, ma è diventato o può diventare un problema.<br>
				Il gioco, quando non è considerato un divertimento, può provocare <strong>conseguenze negative</strong> nella vita di una persona e dei suoi familiari.<br>
				<br>
				Abitudini di gioco non corrette possono causare problemi lavorativi, familiari e finanziari.<br>
				<br>
				<strong>{{ APP_NAME }}</strong>, consapevole che il gioco può rappresentare un problema per una piccola parte di giocatori in termini di gioco eccessivo e dipendenza dal gioco, <strong>promuove insieme all'<a href="https://www.agenziadoganemonopoli.gov.it/portale/">Agenzia delle Dogane e dei Monopoli</a></strong> il <strong>Gioco Responsabile</strong> con l'obiettivo di offrirti divertimento e intrattenimento.<br>
				<br>
				Questo sito contiene i consigli ed i suggerimenti necessari perché il gioco resti sempre un piacere senza rischi e fornisce le informazioni utili nel caso sia diventato un problema.<br>
				<br>
				I siti di prodotto sono certificati per il gioco responsabile dalla <strong><a href="http://www.gx4.com/">Global Gambling Guidance Group (G4)</a></strong>. La Global Gambling Guidance Group è un'organizzazione istituita da un gruppo internazionale di esperti che affronta le tematiche legate al gioco responsabile con regole e protocolli all'avanguardia in Europa. <br>
				<br>
				Con questo riconoscimento {{ APP_NAME }} conferma ancora di più il proprio impegno nella tutela dell'esperienza di gioco dei propri utenti.</p>

				<hr class="my-5">

				<div class="text-center">
					<h1 id="rules"><strong>Le regole</strong></h1>
					<h3 class="mb-4">Gioca e divertiti, ma ricorda queste regole.</h3>
				</div>
				<p>Per fare in modo che il gioco resti sempre un piacere, segui alcune regole elementari:</p>
				<ul>
					<li>Imposta i tuoi limiti di gioco e non superarli mai.</li>
					<li>Gioca solo la quantità di denaro stabilita inizialmente.</li>
					<li>Smetti di giocare quando hai superato il limite di tempo stabilito inizialmente.</li>
					<li>Non giocare somme di denaro che non puoi permetterti di perdere.</li>
					<li>Evita di spendere al gioco il denaro destinato ad altri scopi.</li>
					<li>Se hai deciso di smettere di giocare, o di giocare meno, sforzati di mantenere questo proposito.</li>
					<li>Evita di spendere troppo spesso il resto per il gioco.</li>
					<li>Non giocare per rifarti quando perdi.</li>
					<li>Evita di considerare il gioco come una soluzione per i tuoi problemi e per le tue preoccupazioni.</li>
					<li>Non chiedere mai soldi in prestito per giocare.</li>
					<li>Considera che il denaro speso al gioco è il prezzo che paghi per il tuo divertimento.</li>
					<li>Non mentire ai tuoi cari sulle somme che hai perso al gioco o sul tempo dedicato al gioco.</li>
					<li>Chiedi aiuto se pensi che stai spendendo troppo o stai giocando troppo frequentemente.</li>
					<li>Non assentarti dal lavoro per andare a giocare.</li>
					<li>Non giocare quando ti senti depresso, solo, annoiato, teso o ansioso.</li>
				</ul>

				<hr class="my-5">

				<div class="text-center">
					<h1><strong>Regola del 18</strong></h1>
					<h3 class="mb-4">I giochi che prevedono vincite in denaro sono dedicati ai più grandi.</h3>
				</div>
				<p>IL GIOCO È VIETATO AI MINORI DI 18 ANNI</p>
				<p>La Legge n.111, art.24, commi 19-22 del 15/07/2011 disciplina il divieto di partecipazione ai giochi con vincite in denaro per i minori.</p>

				<hr class="my-5">

				<div class="text-center">
					<h1 id="myths"><strong>Miti e credenze</strong></h1>
				</div>
				<p>
					<strong>MITO</strong>: “se continuo a giocare la fortuna girerà e riguadagnerò i soldi che ho perso finora: devo solo andare avanti a giocare”<br>
					<strong>VERITÀ: ogni volta che giochi, l'esito è completamente indipendente dalle giocate precedenti: le tue probabilità di vincere non cambiano nel tempo.</strong>
				</p>
				<p>
					<strong>MITO</strong>: “ho quasi vinto: questo vuol dire che la prossima volta vincerò”<br>
					<strong>VERITÀ: avvicinarsi alla vincita non significa che si sta per vincere: l'esito della prossima giocata non è influenzato dall'avere quasi vinto in precedenza.</strong>
				</p>
				<p>
					<strong>MITO</strong>: “mentre sto giocando il tenere in mano un oggetto fortunato, incrociare le dita, ecc., aumentano le probabilità che ho di vincere”<br>
					<strong>VERITÀ: l'esito della giocata non dipende MAI dai riti scaramantici che si fanno mentre si gioca.</strong>
				</p>
				<p>
					<strong>MITO</strong>: “la mia conoscenza e la mia abilità nel gioco contribuiscono ad aumentare la probabilità che ho di vincere”<br>
					<strong>VERITÀ: nei giochi che dipendono solo dalla fortuna (come il Gratta e Vinci, il Lotto, le Lotterie) l'abilità del giocatore non può influenzare in alcun modo l'esito della giocata.</strong>
				</p>
				<p>
					<strong>MITO</strong>: “le vincite e le perdite tendono ad accadere in modo ciclico”<br>
					<strong>VERITÀ: nei giochi non c'è ciclicità nelle vincite e nelle perdite.</strong><br><br>
				</p>
				<p>Testi sviluppati in collaborazione con il Centro Interuniversitario per la Ricerca sulla Genesi e sullo sviluppo delle Motivazioni Prosociali e Antisociali.</p>

				<hr class="my-5">

				<div class="text-center">
					<h1 id="help"><strong>Dove chiedere aiuto</strong></h1>
					<h3>A chi rivolgersi</h3>
				</div>

				<p>Se ritieni di avere un problema relativo al gioco o se hai un familiare che pensi abbia un problema con il gioco, puoi trovare aiuto nel servizio <strong>GiocaResponsabile</strong>.</p>
				<p>Il servizio è completamente gratuito e anonimo e mette a disposizione di coloro che hanno sviluppato problemi (psicologici, relazionali, legali) dovuti agli eccessi di gioco, ai loro famigliari e amici, la professionalità di un team di psicologi ed esperti attraverso una serie di strumenti e piattaforme per l'assistenza:</p>
				<ul>
					<li>Il numero verde <strong>800.921.121</strong> – accessibile da tutta Italia da rete fissa o cellulare.</li>
					<li>Il sito <a href="http://www.giocaresponsabile.it"><strong>www.giocaresponsabile.it</strong></a> – per accedere alla chat, contattare il team di esperti via e-mail e trovare tutte le informazioni sui servizi territoriali che si occupano di problemi di gioco.</li>
				</ul>
				<p>Esclusiva e unica possibilità di cura telefonica gratuita, per giocatori che non possono o non vogliono rivolgersi ad un servizio. Si tratta di un programma di terapia cognitivo-comportamentale, gestito nella tutela assoluta dell'anonimato, da un team di professionisti, della durata di 5 mesi circa.</p>
			</div>
		</div>
	</div>

	<pg-page-footer />
</div>
</template>