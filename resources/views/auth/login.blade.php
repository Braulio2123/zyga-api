@extends('layouts.app')

@section('content')
<div class="app-container">
    <div class="card">
        <h1 class="title">Zyga</h1>
        <p class="subtitle">Iniciar sesión en la PWA</p>

        <form id="loginForm">
            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="ejemplo@correo.com"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="********"
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary" id="btnLogin">
                Iniciar sesión
            </button>
        </form>

        <div id="messageBox" class="message"></div>
    </div>
</div>

<script>
    const loginForm = document.getElementById('loginForm');
    const btnLogin = document.getElementById('btnLogin');
    const messageBox = document.getElementById('messageBox');

    function showMessage(type, text) {
        messageBox.className = 'message ' + type;
        messageBox.textContent = text;
    }

    loginForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        btnLogin.disabled = true;
        btnLogin.textContent = 'Ingresando...';
        messageBox.className = 'message';
        messageBox.textContent = '';

        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;

        try {
            const response = await fetch('http://127.0.0.1:8000/api/v1/auth/login', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email: email,
                    password: password
                })
            });

            const data = await response.json();

            if (!response.ok) {
                const errorText = data.message || 'Error al iniciar sesión.';
                showMessage('error', errorText);
                btnLogin.disabled = false;
                btnLogin.textContent = 'Iniciar sesión';
                return;
            }

            const token = data.data.access_token;
            const user = data.data.user;

            localStorage.setItem('zyga_token', token);
            localStorage.setItem('zyga_user', JSON.stringify(user));

            showMessage('success', 'Inicio de sesión correcto. Redirigiendo...');

            setTimeout(() => {
                window.location.href = '/dashboard';
            }, 800);

        } catch (error) {
            showMessage('error', 'No se pudo conectar con la API.');
            btnLogin.disabled = false;
            btnLogin.textContent = 'Iniciar sesión';
        }
    });
</script>
@endsection