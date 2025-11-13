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

            <!-- Mensajes de error generales -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>⚠️ Por favor corrige los siguientes errores:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Mensaje de éxito -->
            @if (session('success'))
                <div class="alert alert-success">
                    <strong>✓ {{ session('success') }}</strong>
                </div>
            @endif

            <form action="{{ route('register.store') }}" method="POST" id="registerForm">
                @csrf

                <!-- Paso 1: Información Personal -->
                <div class="form-step active" id="step1">
                    <div class="form-row">
                        <div class="form-group {{ $errors->has('nombre') ? 'has-error' : '' }}">
                            <label for="nombre">Nombre</label>
                            <input type="text" id="nombre" name="nombre" placeholder="Juan" required value="{{ old('nombre') }}">
                            @error('nombre')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group {{ $errors->has('apellido') ? 'has-error' : '' }}">
                            <label for="apellido">Apellido</label>
                            <input type="text" id="apellido" name="apellido" placeholder="Pérez" required value="{{ old('apellido') }}">
                            @error('apellido')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group {{ $errors->has('telefono') ? 'has-error' : '' }}">
                            <label for="telefono">Teléfono</label>
                            <input type="tel" id="telefono" name="telefono" placeholder="+57 300 123 4567" value="{{ old('telefono') }}">
                            @error('telefono')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group {{ $errors->has('fecha_nacimiento') ? 'has-error' : '' }}">
                            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required value="{{ old('fecha_nacimiento') }}" max="{{ date('Y-m-d', strtotime('-16 years')) }}">
                            @error('fecha_nacimiento')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                            <small style="display: block; margin-top: 5px; color: #718096; font-size: 12px;">
                                Debes tener al menos 16 años para registrarte
                            </small>
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn btn-next" onclick="nextStep(2)">Siguiente</button>
                    </div>
                </div>

                <!-- Paso 2: Información de Cuenta -->
                <div class="form-step" id="step2">
                    <div class="form-group {{ $errors->has('correo') ? 'has-error' : '' }}">
                        <label for="correo">Correo Electrónico</label>
                        <input type="email" id="correo" name="correo" placeholder="tu@email.com" required value="{{ old('correo') }}">
                        @error('correo')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" placeholder="••••••••" required oninput="checkPasswordStrength()">
                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        <div class="password-strength">
                            <div class="password-strength-bar" id="passwordStrength"></div>
                        </div>
                        <p class="strength-text" id="strengthText"></p>
                        
                        <!-- Requisitos de contraseña -->
                        <div class="password-requirements">
                            <strong style="font-size: 13px; color: #4a5568;">Requisitos de contraseña:</strong>
                            <ul id="passwordChecks">
                                <li id="check-length">Mínimo 8 caracteres</li>
                                <li id="check-upper">Al menos una mayúscula</li>
                                <li id="check-lower">Al menos una minúscula</li>
                                <li id="check-number">Al menos un número</li>
                            </ul>
                        </div>
                    </div>

                    <div class="form-group {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
                        <label for="password_confirmation">Confirmar Contraseña</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required oninput="checkPasswordMatch()">
                        <span class="error-message" id="passwordMatchError" style="display: none;">Las contraseñas no coinciden</span>
                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn btn-back" onclick="prevStep(1)">Atrás</button>
                        <button type="submit" class="btn btn-submit" id="submitBtn">Crear Cuenta</button>
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

// Si hay errores, ir al paso correcto
@if ($errors->any())
    @if ($errors->has('correo') || $errors->has('password') || $errors->has('password_confirmation'))
        currentStep = 2;
        document.getElementById('step1').classList.remove('active');
        document.getElementById('step2').classList.add('active');
    @endif
@endif

function nextStep(step) {
    const currentStepElement = document.getElementById(`step${currentStep}`);
    const inputs = currentStepElement.querySelectorAll('input[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value) {
            isValid = false;
            input.style.borderColor = '#f56565';
            setTimeout(() => input.style.borderColor = '#e2e8f0', 2000);
        }
    });

    if (!isValid) {
        alert('Por favor completa todos los campos obligatorios');
        return;
    }

    // Validar fecha de nacimiento
    const fechaNacimiento = document.getElementById('fecha_nacimiento');
    const hoy = new Date();
    const fechaIngresada = new Date(fechaNacimiento.value);
    
    if (fechaIngresada >= hoy) {
        alert('La fecha de nacimiento debe ser anterior a hoy');
        fechaNacimiento.style.borderColor = '#f56565';
        setTimeout(() => fechaNacimiento.style.borderColor = '#e2e8f0', 2000);
        return;
    }

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
        document.getElementById('password_confirmation').style.borderColor = '#f56565';
        return;
    }
    
    if (pass.length < 8) {
        e.preventDefault();
        alert('La contraseña debe tener al menos 8 caracteres');
        document.getElementById('password').style.borderColor = '#f56565';
        return;
    }
});

function checkPasswordStrength() {
    const password = document.getElementById('password').value;
    const bar = document.getElementById('passwordStrength');
    const text = document.getElementById('strengthText');
    
    // Verificar requisitos individuales
    const hasLength = password.length >= 8;
    const hasUpper = /[A-Z]/.test(password);
    const hasLower = /[a-z]/.test(password);
    const hasNumber = /[0-9]/.test(password);
    
    // Actualizar lista de requisitos
    document.getElementById('check-length').className = hasLength ? 'valid' : 'invalid';
    document.getElementById('check-upper').className = hasUpper ? 'valid' : 'invalid';
    document.getElementById('check-lower').className = hasLower ? 'valid' : 'invalid';
    document.getElementById('check-number').className = hasNumber ? 'valid' : 'invalid';
    
    // Calcular fuerza
    let strength = 0;
    if (hasLength) strength++;
    if (hasLower && hasUpper) strength++;
    if (hasNumber) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;

    bar.className = 'password-strength-bar';
    if (strength >= 4) {
        bar.classList.add('strong');
        text.textContent = '✓ Contraseña fuerte';
        text.style.color = '#48bb78';
    } else if (strength >= 2) {
        bar.classList.add('medium');
        text.textContent = '⚠ Contraseña media';
        text.style.color = '#ed8936';
    } else if (strength >= 1) {
        bar.classList.add('weak');
        text.textContent = '✗ Contraseña débil';
        text.style.color = '#f56565';
    } else {
        text.textContent = '';
    }
    
    checkPasswordMatch();
}

function checkPasswordMatch() {
    const pass = document.getElementById('password').value;
    const confirm = document.getElementById('password_confirmation').value;
    const errorMsg = document.getElementById('passwordMatchError');
    
    if (confirm.length > 0) {
        if (pass !== confirm) {
            errorMsg.style.display = 'block';
            document.getElementById('password_confirmation').style.borderColor = '#f56565';
        } else {
            errorMsg.style.display = 'none';
            document.getElementById('password_confirmation').style.borderColor = '#48bb78';
        }
    } else {
        errorMsg.style.display = 'none';
        document.getElementById('password_confirmation').style.borderColor = '#e2e8f0';
    }
}
</script>
</body>
</html>