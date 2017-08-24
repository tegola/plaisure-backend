<footer class="footer text-center text-lg-left">
	<div class="container">
		<div class="row">
			<div class="col-sm-4 col-lg-2">
				<h3 class="footer-heading">Sale ed esercizi</h3>
				<ul class="list-unstyled">
					<li><a href="{{ route('site.venues.explore') }}">Ricerca</a></li>
					@foreach(config('constants.footer_explore_cities') as $name => $params)
						<li><a href="{{ route('site.venues.explore', $params) }}">Esercizi a {{ $name }}</a></li>
					@endforeach
					<li><a href="{{ route('site.promote') }}">Promuovi la tua attivit&agrave;</a></li>
				</ul>
			</div>
			<div class="col-sm-4 col-lg-2">
				<h3 class="footer-heading">Azienda</h3>
				<ul class="list-unstyled">
					<li><a href="{{ route('site.about.company') }}">Chi siamo</a></li>
					<li><a href="{{ route('site.about.contact') }}">Contatti</a></li>
				</ul>
			</div>
			<div class="col-sm-4 col-lg-2">
				<h3 class="footer-heading">Gioco responsabile</h3>
				<ul class="list-unstyled">
					<li><a href="{{ route('site.play-responsibly.index') }}#toofar">Gioca senza esagerare</a></li>
					<li><a href="{{ route('site.play-responsibly.index') }}#rules">Le regole</a></li>
					<li><a href="{{ route('site.play-responsibly.index') }}#myths">Miti e credenze</a></li>
					<li><a href="{{ route('site.play-responsibly.index') }}#help">Dove chiedere aiuto</a></li>
				</ul>
			</div>
			<div class="ml-lg-auto col-lg-5">
				<p>Informati sulle probabilit&agrave; di vincita e sul regolamento di gioco sul sito <a href="https://www.agenziadoganemonopoli.gov.it">agenziadoganemonopoli.gov.it</a>.</p>
				<ul class="list-inline footer-aams-logo-list">
					<li class="list-inline-item mb-3">
						<a href="https://www.agenziadoganemonopoli.gov.it/">
							<img src="{{ asset('img/footer-aams-1.svg') }}">
						</a>
					</li>
					<li class="list-inline-item mb-3">
						<a href="https://www.agenziadoganemonopoli.gov.it/portale/monopoli">
							<img src="{{ asset('img/footer-aams-2.svg') }}">
						</a>
					</li>
					<li class="list-inline-item mb-3">
						<span class="badge footer-age-badge" aria-hidden="true">18+</span>
						<span class="footer-age-text">Il gioco &egrave; vietato<br>ai minori di 18 anni</span>
					</li>
				</ul>
			</div>
		</div>
		<div class="text-center mt-3">
			@include('site.vectors.logo', ['text' => false, 'class' => 'footer-logo'])
			<p class="mb-0">
				Copyright {{ date('Y') }} {{ config('constants.company') }}<br>
				P. IVA {{ config('constants.partita_iva')}}
			</p>
		</div>
		<div class="row"></div>
	</div>
</footer>