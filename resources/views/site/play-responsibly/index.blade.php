@extends('site.layout')

@section('body_class', 'page-play-responsibly')
@section('title', "Gioca responsabilmente")

@section('content')
	@include('site.components.navbar', [
		'class' => 'navbar-light',
		'show_search' => false
	])

	<div class="container my-5">
		<div class="row">
			<div class="col-md-10 ml-md-auto mr-md-auto">
				<div class="text-center">
					<h1><strong>Gioca senza esagerare</strong></h1>
					<h3 class="mb-4">Evita gli eccessi: fai in modo che il gioco resti un piacere.</h3>
				</div>
				<p>Il gioco &egrave;  un'esperienza piacevole e positiva che non rappresenta alcun rischio per la  maggior parte delle persone.<br>
				<br>
				Ma tra milioni di giocatori, ci sono anche persone per le quali giocare non &egrave;  pi&ugrave; solo un divertimento, ma &egrave; diventato o pu&ograve; diventare un problema.<br>
				Il gioco, quando non &egrave; considerato un divertimento, pu&ograve; provocare <strong>conseguenze  negative</strong> nella vita di una persona e dei suoi familiari.<br>
				<br>
				Abitudini di gioco non corrette possono causare problemi lavorativi, familiari  e finanziari.<br>
				<br>
				<strong>{{ config('app.name') }}</strong>, consapevole che il gioco pu&ograve; rappresentare un problema per  una piccola parte di giocatori in termini di gioco eccessivo e dipendenza dal  gioco, <strong>promuove insieme all'<a href="https://www.agenziadoganemonopoli.gov.it/portale/">Agenzia delle Dogane e dei Monopoli</a></strong> il <strong><a href="/it/chi-siamo/comunita/gioco-responsabile">Gioco Responsabile</a></strong> con l'obiettivo di  offrirti divertimento e intrattenimento.<br>
				<br>
				Questo sito contiene i consigli ed i suggerimenti necessari perch&eacute; il gioco  resti sempre un piacere senza rischi e fornisce le informazioni utili nel caso  sia diventato un problema.<br>
				<br>
				I siti di prodotto sono certificati per il gioco  responsabile dalla <strong><a href="http://www.gx4.com/">Global  Gambling Guidance Group (G4)</a></strong>. La Global Gambling  Guidance Group &egrave; un'organizzazione istituita da un gruppo internazionale di  esperti che affronta le tematiche legate al gioco responsabile con regole e  protocolli all'avanguardia in Europa. <br>
				<br>
				Con questo riconoscimento {{ config('app.name') }} conferma ancora di pi&ugrave; il proprio impegno  nella tutela dell'esperienza di gioco dei propri utenti.</p>

				<hr class="my-5">

				<div class="text-center">
					<h1><strong>Le regole</strong></h1>
					<h3 class="mb-4">Gioca e divertiti, ma ricorda queste regole.</h3>
				</div>
				<p>Per fare in modo che il gioco resti sempre un piacere, segui alcune regole elementari:</p>
				<ul>
					<li>Imposta i tuoi limiti di gioco e non superarli mai.</li>
					<li>Gioca solo la quantit&agrave; di denaro stabilita inizialmente.</li>
					<li>Smetti di giocare quando hai superato il limite di tempo stabilito inizialmente.</li>
					<li>Non giocare somme di denaro che non puoi permetterti di perdere.</li>
					<li>Evita di spendere al gioco il denaro destinato ad altri scopi.</li>
					<li>Se hai deciso di smettere di giocare, o di giocare meno, sforzati di mantenere questo proposito.</li>
					<li>Evita di spendere troppo spesso il resto per il gioco.</li>
					<li>Non giocare per rifarti quando perdi.</li>
					<li>Evita di considerare il gioco come una soluzione per i tuoi problemi e per le tue preoccupazioni.</li>
					<li>Non chiedere mai soldi in prestito per giocare.</li>
					<li>Considera che il denaro speso al gioco &egrave; il prezzo che paghi per il tuo divertimento.</li>
					<li>Non mentire ai tuoi cari sulle somme che hai perso al gioco o sul tempo dedicato al gioco.</li>
					<li>Chiedi aiuto se pensi che stai spendendo troppo o stai giocando troppo frequentemente.</li>
					<li>Non assentarti dal lavoro per andare a giocare.</li>
					<li>Non giocare quando ti senti depresso, solo, annoiato, teso o ansioso.</li>
				</ul>

				<hr class="my-5">

				<div class="text-center">
					<h1><strong>Regola del 18</strong></h1>
					<h3 class="mb-4">I giochi che prevedono vince in denaro sono dedicati ai pi&ugrave; grandi.</h3>
				</div>
				<p>IL GIOCO &Egrave; VIETATO AI MINORI DI 18 ANNI</p>
				<p>La Legge n.111, art.24, commi 19-22 del 15/07/2011 disciplina il divieto di partecipazione ai giochi con vincite in denaro per i minori.</p>

				<hr class="my-5">

				<div class="text-center">
					<h1><strong>Miti e credenze</strong></h1>
				</div>
				<p>
					<strong>MITO</strong>: &ldquo;se continuo a giocare la fortuna girer&agrave; e riguadagner&ograve; i soldi che ho perso finora: devo solo andare avanti a giocare&rdquo;<br>
					<strong>VERIT&Agrave;: ogni volta che giochi, l'esito &egrave; completamente indipendente dalle giocate precedenti: le tue probabilit&agrave; di vincere non cambiano nel tempo.</strong>
				</p>
				<p>
					<strong>MITO</strong>: &ldquo;ho quasi vinto: questo vuol dire che la prossima volta vincer&ograve;&rdquo;<br>
					<strong>VERIT&Agrave;: avvicinarsi alla vincita non significa che si sta per vincere: l'esito della prossima giocata non &egrave; influenzato dall'avere quasi vinto in precedenza.</strong>
				</p>
				<p>
					<strong>MITO</strong>: &ldquo;mentre sto giocando il tenere in mano un oggetto fortunato, incrociare le dita, ecc., aumentano le probabilit&agrave; che ho di vincere&rdquo;<br>
					<strong>VERIT&Agrave;: l'esito della giocata non dipende MAI dai riti scaramantici che si fanno mentre si gioca.</strong>
				</p>
				<p>
					<strong>MITO</strong>: &ldquo;la mia conoscenza e la mia abilit&agrave; nel gioco contribuiscono ad aumentare la probabilit&agrave; che ho di vincere&rdquo;<br>
					<strong>VERIT&Agrave;: nei giochi che dipendono solo dalla fortuna (come il Gratta e Vinci, il Lotto, le Lotterie) l'abilit&agrave; del giocatore non pu&ograve; influenzare in alcun modo l'esito della giocata.</strong>
				</p>
				<p>
					<strong>MITO</strong>: &ldquo;le vincite e le perdite tendono ad accadere in modo ciclico&rdquo;<br>
					<strong>VERIT&Agrave;: nei giochi non c'&egrave; ciclicit&agrave; nelle vincite e nelle perdite.</strong><br><br>
				</p>
				<p>Testi sviluppati in collaborazione con il Centro Interuniversitario per la Ricerca sulla Genesi e sullo sviluppo delle Motivazioni Prosociali e Antisociali.</p>

				<hr class="my-5">

				<div class="text-center">
					<h1><strong>Dove chiedere aiuto</strong></h1>
					<h3>A chi rivolgersi</h3>
				</div>

				<p>Se ritieni di avere un problema relativo al gioco o se hai un familiare che pensi abbia un problema con il gioco, puoi trovare aiuto nel servizio <strong>GiocaResponsabile</strong>.</p>
				<p>Il servizio &egrave; completamente gratuito e anonimo e mette a disposizione di coloro che hanno sviluppato problemi (psicologici, relazionali, legali) dovuti agli eccessi di gioco, ai loro famigliari e amici, la professionalit&agrave; di un team di psicologi ed esperti attraverso una serie di strumenti e piattaforme per l'assistenza:</p>
				<ul>
					<li>Il numero verde <strong>800.921.121</strong> &ndash; accessibile da tutta Italia da rete fissa o cellulare.</li>
					<li>Il sito <a href="http://www.giocaresponsabile.it"><strong>www.giocaresponsabile.it</strong></a> &ndash; per accedere alla chat, contattare il team di esperti via e-mail e trovare tutte le informazioni sui servizi territoriali che si occupano di problemi di gioco.</li>
				</ul>
				<p>Esclusiva e unica possibilit&agrave; di cura telefonica gratuita, per giocatori che non possono o non vogliono rivolgersi ad un servizio. Si tratta di un programma di terapia cognitivo-comportamentale, gestito nella tutela assoluta dell'anonimato, da un team di professionisti, della durata di 5 mesi circa.</p>
			</div>
		</div>
	</div>
@endsection