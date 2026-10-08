@extends('layouts.app')

@section('title', 'Mi vivero · ' . config('app.name'))

@section('content')
    <section class="dashboard">
        <h1>Mi vivero</h1>
        {{-- Mensajes de carga, éxito y error. aria-live los anuncia a lectores de pantalla --}}
        <p id="status-msg" class="status-msg" role="status" aria-live="polite"></p>
        <div id="tree-grid" class="tree-grid"></div>
    </section>
@endsection