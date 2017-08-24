@extends('site.layout')

@section('body_class', 'page-promote')
@section('title', "Promuovi la tua attività")

@section('content')
@include('site.components.navbar')

<div class="container my-5">
	<h1 class="display-4">Promuovi la tua attività</h1>
	<p class="lead">Sei il proprietario di un'attività inerente al mondo del gioco e vuoi promuoverla? La tua attività è già presente su ProntoGioco e vorresti ampliare le informazioni fornite ai visitatori? In entrambi i casi, sei nel posto giusto!</p>

	<p>&lt;Spiegare a che serve promuovere&gt;</p>

	<h2>Quali sono i benefici?</h2>
	<p>Innanzitutto&hellip;</p>
	<p>[Screenshot di attività senza dati] e [Screenshot di attività piena di dati]</p>

	<h2>Quanto costa?</h2>
	<p>Assolutamente nulla! ProntoGioco offre diversi livelli di personalizzazione, e uno di questi è completamente gratuito; ti consente di aggiungere le informazioni essenziali senza spendere un centesimo, a parte pochi minuti del tuo tempo.</p>
	<p>&lt;Continuare ad allungare il brodo&gt;</p>

	<h2>Che aspetti?</h2>
	<p>Se sei ancora indeciso, non dovresti esserlo. Rivendicare la tua attività porterà soltanto benefici. Il nostro staff si occuperà di tutto il necessario per aggiornare i dati, e in futuro potrai anche modificarli autonomamente!</p>
	<p>Se hai già trovato la tua attività su ProntoGioco, fai click su “Rivendica attività” nella pagina con i dettagli per contattarci. Altrimenti scrivici menzionando il nome dell’attività e l’indirizzo.</p>
	<p>In entrambi i casi sarai ricontattato prima possibile dal nostro staff.</p>

	<p class="text-center">
		<a href="mailto:{{ config('constants.email') }}?subject={{ rawurlencode('Rivendicazione attività') }}" class="btn btn-lg btn-primary">Scrivici</a>
	</p>
</div>
@endsection