@extends('layouts.app')
@section('title', 'Edit package')
@section('subtitle', $package->name)
@section('content') @include('packages._form', ['package' => $package]) @endsection
