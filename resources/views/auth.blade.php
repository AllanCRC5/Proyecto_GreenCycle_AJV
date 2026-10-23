@extends('layouts.app')

@section('title', 'Iniciar sesión · ' . config('app.name'))

@section('content')
    <section id="auth-forms" class="auth">
        <p id="auth-msg" class="status-msg" role="status" aria-live="polite"></p>

        <form id="form-login" class="auth__form" novalidate>
            <h1>Iniciar sesión</h1>
            <label for="login-email">Correo</label>
            <input id="login-email" name="email" type="email" autocomplete="email" required>
            <label for="login-password">Contraseña</label>
            <input id="login-password" name="password" type="password" autocomplete="current-password" required>
            <button type="submit" class="btn">Entrar</button>
            <p>¿No tienes cuenta? <button type="button" class="link" data-show="register">Regístrate</button></p>
        </form>

        <form id="form-register" class="auth__form" hidden novalidate>
            <h1>Crear cuenta</h1>
            <label for="reg-name">Nombre</label>
            <input id="reg-name" name="name" type="text" autocomplete="name" required>
            <label for="reg-email">Correo</label>
            <input id="reg-email" name="email" type="email" autocomplete="email" required>
            <label for="reg-password">Contraseña (mínimo 8 caracteres)</label>
            <input id="reg-password" name="password" type="password" autocomplete="new-password" required>
            <label for="reg-confirm">Repite la contraseña</label>
            <input id="reg-confirm" name="password_confirmation" type="password" autocomplete="new-password" required>
            <button type="submit" class="btn">Registrarme</button>
            <p>¿Ya tienes cuenta? <button type="button" class="link" data-show="login">Inicia sesión</button></p>
        </form>
    </section>
@endsection