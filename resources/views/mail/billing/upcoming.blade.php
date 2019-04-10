@extends('mail/layout')

@section('content')
	<p><strong>{{ __('emails.common.greeting_name', ['name' => $notifiable->name]) }}</strong></p>

	<p>Nei prossimi giorni sarà rinnovato l'abbonamento mensile per la tua attività</p>

	<p>DATA</p>

	<table class="table">
		<tbody>
			{{-- Product --}}
			<tr>
				<td>
					<div><strong>Premium subscription</strong></div>
					<div class="small">Abbonamento mensile</div>
				</td>
				<td class="text-right align-middle">
					<strong>39,00 €</strong>
				</td>
			</tr>

			{{-- Venue --}}
			<tr class="bg-light">
				<td colspan="2" class="px-0 py-0">
					<table class="table-borderless">
						<tr>
							<td class="align-middle">
								@if ($venue->photos->count())
									<img src="{{ $venue->photos->first()->resized_url }}" class="rounded" style="width: 100px">
								@endif
							</td>
							<td class="align-middle small">
								<div><strong>{{ $venue->name }}</strong></div>
								<div>{{ $venue->address_line1 }}</div>
								<div>{{ implode(', ', [$venue->address_city, $venue->address_province, $venue->address_postcode, $venue->country]) }}</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>

		{{-- Total --}}
		<tfoot>
			<tr>
				<td class="text-right">Totale</td>
				<td class="text-right"><strong>39,00 €</strong></td>
			</tr>
			<tr>
				<td class="py-0"></td>
				<td class="py-0"></td>
			</tr>
		</tfoot>
	</table>

	<p>Per modificare o annullare l'abbonamento, accedi su {{ config('app.name') }}. Se hai domande, contattaci. </p>

	@include('mail/components/salutation')

	<pre>{{ json_encode($invoice) }}</pre>
	{{--
	<p>{{ __('emails.reset_password.intro') }}</p>

	@component('mail/components/action-button', ['url' => $url])
		{{ __('emails.reset_password.action') }}
	@endcomponent

	<p>{{ __('emails.reset_password.outro') }}</p>

	

	@include('mail/components/action-footer', [
		'action' => __('emails.reset_password.action'),
		'url' => $url
	])
	--}}
@endsection