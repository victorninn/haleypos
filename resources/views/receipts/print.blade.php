@extends('layouts.base')
@section('title', 'Receipt '.$receipt->receipt_number)
@section('body')
<div class="p-8">
    @include('receipts._body', ['receipt' => $receipt])
    <div class="text-center mt-6 no-print">
        <button onclick="window.print()" class="btn btn-primary">Print</button>
        <a href="{{ route('receipts.show', $receipt) }}" class="btn btn-ghost">Back</a>
    </div>
</div>
<script>window.onload = () => setTimeout(() => window.print(), 250);</script>
@endsection
