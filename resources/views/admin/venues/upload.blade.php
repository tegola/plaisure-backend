@extends('admin.layout')

@section('title', 'Carica esercizi')

@section('content')

<div class="container">
	<div class="page-header">
		<h3>Carica esercizi</h3>
	</div>

	<p>Qui puoi caricare il file CSV contenenti gli esercizi. Questo file sarà usato per aggiornare il database, ma solo esercizio per esercizio, e manualmente, nella sezione <a href="{{ route('admin.venues.maintain') }}">Manutenzione esercizi</a>.</p>
	<p>Ricorda che deve esserci la riga di intestazione, e che l'ordine dei campi è il seguente: <code>codice censimento esercizio</code>, <code>denominazione</code>, <code>indirizzo</code>, <code>comune e provincia</code>, <code>tipologia esercizio</code>, <code>superficie del locale in mq</code>, <code>codice iscrizione soggetto</code>, <code>tipologia apparecchio</code>.</p>

	<br>
	<form method="post" class="form-horizontal well" enctype="multipart/form-data">
		<div class="form-group">
			<label class="col-sm-3 control-label">File corrente</label>
			<div class="col-sm-9">
				<p class="form-control-static">
					@if ($file_current)
						{{ $file_current }}
					@else
						<span class="text-muted">(Nessuno)</span>
					@endif
				</p>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label">Nuovo file</label>
			<div class="col-sm-9">
				<input type="file" class="form-control-static" name="venues">
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-3 col-sm-9">
				<button type="submit" class="btn btn-primary">Carica</button>
			</div>
		</div>
	</form>
</div>

@endsection