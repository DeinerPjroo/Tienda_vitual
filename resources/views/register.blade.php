<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - BeLuxe</title>
    <link rel="stylesheet" href="{{ asset('css/Register.css') }}">
</head>
<body>
    <div class="register-container">
        <div class="register-image">
            <div class="image-content">
                <h2>Únete a nosotros</h2>
                <p>Crea tu cuenta y descubre un mundo de estilo</p>
            </div>
        </div>

        <div class="register-form-section">
            <div class="logo">
                <div class="logo-icon">BL</div>
                <span class="logo-text">BeLuxe</span>
            </div>

            <h1>Crear Cuenta</h1>
            <p>Completa tus datos para registrarte</p>

            <form action="{{ route('register') }}" method="POST" id="registerForm">
                @csrf

                <!-- Paso 1: Información Personal -->
                <div class="form-step active" id="step1">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" id="nombre" name="nombre" placeholder="Juan" required value="{{ old('nombre') }}">
                        </div>
                        <div class="form-group">
                            <label for="apellido">Apellido</label>
                            <input type="text" id="apellido" name="apellido" placeholder="Pérez" required value="{{ old('apellido') }}">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="tel" id="telefono" name="telefono" placeholder="+57 300 123 4567" required value="{{ old('telefono') }}">
                        </div>
                        <div class="form-group">
                            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required value="{{ old('fecha_nacimiento') }}">
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn btn-next" onclick="nextStep(2)">Siguiente</button>
                    </div>
                </div>

                <!-- Paso 2: Información de Cuenta -->
                <div class="form-step" id="step2">
                    <div class="form-group">
                        <label for="correo">Correo Electrónico</label>
                        <input type="email" id="correo" name="correo" placeholder="tu@email.com" required value="{{ old('correo') }}">
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" placeholder="••••••••" required oninput="checkPasswordStrength()">
                        <div class="password-strength">
                            <div class="password-strength-bar" id="passwordStrength"></div>
                        </div>
                        <p class="strength-text" id="strengthText"></p>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirmar Contraseña</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required>
                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn btn-back" onclick="prevStep(1)">Atrás</button>
                        <button type="submit" class="btn btn-submit">Crear Cuenta</button>
                    </div>
                </div>
            </form>

            <div class="login-link">
                ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión aquí</a>
            </div>
        </div>
    </div>

<script>
let currentStep = 1;

function nextStep(step) {
    const currentStepElement = document.getElementById(`step${currentStep}`);
    const inputs = currentStepElement.querySelectorAll('input[required], select[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value) {
            isValid = false;
            input.style.borderColor = '#f56565';
            setTimeout(() => input.style.borderColor = '#e2e8f0', 2000);
        }
    });

    if (!isValid) return;

    document.getElementById(`step${currentStep}`).classList.remove('active');
    document.getElementById(`step${step}`).classList.add('active');
    currentStep = step;
}

function prevStep(step) {
    document.getElementById(`step${currentStep}`).classList.remove('active');
    document.getElementById(`step${step}`).classList.add('active');
    currentStep = step;
}

// Validación final al enviar el formulario
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const pass = document.getElementById('password').value;
    const confirm = document.getElementById('password_confirmation').value;
    if (pass !== confirm) {
        e.preventDefault();
        alert('Las contraseñas no coinciden');
    }
});

function checkPasswordStrength() {
    const password = document.getElementById('password').value;
    const bar = document.getElementById('passwordStrength');
    const text = document.getElementById('strengthText');
    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;

    bar.className = 'password-strength-bar';
    if (strength >= 4) {
        bar.classList.add('strong');
        text.textContent = 'Contraseña fuerte';
        text.style.color = '#48bb78';
    } else if (strength >= 2) {
        bar.classList.add('medium');
        text.textContent = 'Contraseña media';
        text.style.color = '#ed8936';
    } else if (strength >= 1) {
        bar.classList.add('weak');
        text.textContent = 'Contraseña débil';
        text.style.color = '#f56565';
    } else {
        text.textContent = '';
    }
}
</script>
</body>
</html>
