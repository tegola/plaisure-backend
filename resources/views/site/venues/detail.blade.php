@extends('site.layout')

@section('body_class', 'page-detail')

@section('title')
{{ $venue->name }}
&ndash;
{{ $venue->categories->count() ? "{$venueCategoryString} a" : '' }}
{{ $venue->address_city }}
@endsection

@section('description', $venue->description ?: null)

@section('head')
{!! $venue->structuredData() !!}
@endsection