@extends('site.layout')

@section('body_class', 'page-edit-venue')
@section('title', $venue->exists ? 'Modifica attività' : 'Aggiungi attività')

@section('content')
	<pg-navbar variant="dark"></pg-navbar>
	<pg-venue-editor venue-id="{{ $venue->id_hashed }}"></pg-venue-editor>
	@include('site.components.footer')
@endsection