@extends('layouts.app')
@section('title', 'Receipt')
@section('subtitle', $receipt->receipt_number)

@section('topbar-actions')
<a href="{{ route('receipts.print', $receipt) }}" target="_blank" class="btn btn-primary">Print receipt</a>
@endsection

@section('content')
@include('receipts._body', ['receipt' => $receipt])
@endsection
