@extends('mail/layout')

@section('content')
	<p><strong>{{ __('emails.common.greeting_name', ['name' => $notifiable->name]) }}</strong></p>

	<p>{{ __('emails.billing_upcoming.intro', ['date' => $subscription->current_period_ends_at->isoFormat('L')]) }}</p>

	<table class="table">
		<tbody>
			{{-- Product --}}
			<tr>
				<td>
					<div><strong>{{ __("data.subscriptions.{$subscription->name}")}}</strong></div>
					<div class="small">{{ __('emails.billing_upcoming.type_monthly') }}</div>
				</td>
				<td class="text-right align-middle">
					<strong>{{ strtoupper($subscription->currency) }} {{ number_format($subscription->price, 2) }}</strong>
				</td>
			</tr>

			{{-- Venue --}}
			<tr class="bg-light">
				<td colspan="2" class="px-0 py-0">
					<table class="table-borderless">
						<tr>
							@if ($venue->photos->count())
								<td class="align-middle">
									<img src="{{ $venue->photos->first()->thumbnail_url }}" class="rounded" style="width: 100px">
								</td>
							@endif
							<td class="align-middle small" @if (!$venue->photos->count()) colspan="2" @endif>
								<div><strong>{{ $venue->name }}</strong></div>
								<div>{{ $venue->address_line1 }}</div>
								<div>{{ implode(', ', [$venue->address_city, $venue->address_province, $venue->address_postcode, $venue->country]) }}</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>

	<p>
		{!!
			__('emails.billing_upcoming.outro', [
				'login_link' => "<a href='{$loginUrl}'>" . config('app.name') . "</a>",
				'contact_link' => "<a href='mailto:{$supportEmail}'>" . __('emails.billing_upcoming.outro_contact') . "</a>"
			])
		!!}
	</p>

	@include('mail/components/salutation')
@endsection