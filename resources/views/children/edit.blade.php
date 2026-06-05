@extends('layouts.app')
@section('title', 'Edit child')
@section('subtitle', $child->name)
@section('content')
@include('children._form', ['child' => $child])


@if(auth()->user()?->isAdmin()) 
<div class="mt-6">
    <form method="POST" action="{{ route('children.destroy', $child) }}"
          onsubmit="return confirm('Are you sure you want to delete {{ addslashes($child->name) }}? This cannot be undone.')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Delete child</button>
    </form>
</div>
@endif
@endsection