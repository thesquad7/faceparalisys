@extends('layouts.app')

@section('title', 'First Page')

@section('content')
<div style="display: flex; justify-content: center; align-items: center; height: 100vh; text-align: center;">
    <button id="startButton" class="btn btn-primary shadow" style="padding: 20px; font-size: 20px;">Mulai</button>
</div>
<script>
    document.getElementById('startButton').addEventListener('click', function() {
        window.location.href = "{{ route('input') }}";
    });
</script>
@endsection