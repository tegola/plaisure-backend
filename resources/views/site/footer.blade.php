{{-- Icons --}}
@include('site.icons.defs')

{{-- FIXME: Pass region per site and language per user locale --}}
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('constants.google_maps_api_key') }}&language=it&region=IT" defer></script>
<script src="{{ asset('js/app/main.js') }}"></script>
@yield('scripts')

<script src="https://use.typekit.net/qwv3xzz.js"></script>
<script>try{Typekit.load({ async: true });}catch(e){}</script>