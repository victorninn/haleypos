@extends('layouts.app')
@section('title', 'New package')
@section('content') @include('packages._form', ['package' => $package]) @endsection
