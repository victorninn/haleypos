@extends('layouts.app')
@section('title', 'New child')
@section('subtitle', 'Register a child to your playhouse')
@section('content')
@include('children._form', ['child' => $child])
@endsection
