@extends('layouts.app')

@section('content')
<div class="app-container">
    <div class="dashboard-card">
        <h1 class="title">Dashboard Zyga</h1>
        <p class="subtitle">Vista base de la PWA después del login</p>

        <div class="info-box">
            <p><strong>Estado:</strong> <span id="sessionStatus">Verificando sesión...</span></p>
        </div>

        <div class="info-box">
            <p><strong>ID:</strong> <span id="userId">-</span></p>
            <p><strong>Email:</strong> <span id="userEmail">-</span></p>
        </div>

        <div class="info-box">
            <p><strong>Token:</strong></p>
            <p id="tokenPreview" class="muted">-</p>
        </div>

        <div class="actions">
            <button class="btn btn-secondary" id="btnValidateSession">
                Validar sesión con /api/v1/me
            </button>

            <button class="btn btn-danger" id="btnLogout">
                Cerrar sesión
            </button>
        </div>

        <div id="messageBox" class="message"></div>
    </div>
</div>

<script>
    const sessionStatus = document.getElementById('sessionStatus');
    const userId = document.getElementById('userId');
    const userEmail = document.getElementById('userEmail');
    const tokenPreview = document.getElementById('tokenPreview');
    const btnValidateSession = document.getElementById('btnValidateSession');
    const btnLogout = document.getElementById('btnLogout');
    const messageBox = document.getElementById('messageBox');

    function showMessage(type, text) {
        messageBox.className = 'message ' + type;
        messageBox.textContent = text;
    }

    function loadSessionFromStorage() {
        const token = localStorage.getItem('zyga_token');
        const user = localStorage.getItem('zyga_user');

        if (!token || !user) {
            window.location.href = '/login';
            return null;
        }

        const parsedUser = JSON.parse(user);

        sessionStatus.textContent = 'Sesión local encontrada';
        userId.textContent = parsedUser.id ?? '-';
        userEmail.textContent = parsedUser.email ?? '-';
        tokenPreview.textContent = token.substring(0, 35) + '...';

        return { token, user: parsedUser };
    }

    async function validateSession() {
        const session = loadSessionFromStorage();
        if (!session) return;

        try {
            const response = await fetch('http://127.0.0.1:8000/api/v1/me', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + session.token
                }
            });

            const data = await response.json();

            if (!response.ok) {
                showMessage('error', 'La sesión ya no es válida.');
                return;
            }

            showMessage('success', 'Sesión válida. Usuario autenticado correctamente.');
            sessionStatus.textContent = 'Sesión validada contra la API';
            userId.textContent = data.data.id ?? '-';
            userEmail.textContent = data.data.email ?? '-';

        } catch (error) {
            showMessage('error', 'No se pudo validar la sesión con la API.');
        }
    }

    async function logout() {
        const session = loadSessionFromStorage();
        if (!session) return;

        try {
            const response = await fetch('http://127.0.0.1:8000/api/v1/auth/logout', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + session.token
                }
            });

            localStorage.removeItem('zyga_token');
            localStorage.removeItem('zyga_user');

            window.location.href = '/login';

        } catch (error) {
            localStorage.removeItem('zyga_token');
            localStorage.removeItem('zyga_user');
            window.location.href = '/login';
        }
    }

    loadSessionFromStorage();

    btnValidateSession.addEventListener('click', validateSession);
    btnLogout.addEventListener('click', logout);
</script>
@endsection