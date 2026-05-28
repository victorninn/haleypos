@extends('layouts.app')
@section('title', 'Edit child')
@section('subtitle', $child->name)
@section('content')
@include('children._form', ['child' => $child])
@endsection
