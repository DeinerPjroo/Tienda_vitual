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
                <div>
                    <h2>Únete a nosotros</h2>
                    <p>Crea tu cuenta y descubre un mundo de estilo</p>
                </div>
            </div>
        </div>

        <div class="register-form-section">
            <div class="logo">
                <div class="logo-icon">BL</div>
                <span class="logo-text">BeLuxe</span>
            </div>

            <h1>Crear Cuenta</h1>
            <p>Completa tus datos para registrarte</p>

            <!-- Progress Indicator -->
            <div class="progress-indicator">
                <div class="progress-line"></div>
                <div class="progress-line-fill" id="progressFill" style="width: 33.33%"></div>
                
                <div class="step-indicator">
                    <div class="step-number active" id="stepNum1">1</div>
                    <span class="step-label">Personal</span>
                </div>
                <div class="step-indicator">
                    <div class="step-number" id="stepNum2">2</div>
                    <span class="step-label">Cuenta</span>
                </div>
                <div class="step-indicator">
                    <div class="step-number" id="stepNum3">3</div>
                    <span class="step-label">Confirmar</span>
                </div>
            </div>

            {{-- @if ($errors->any())
            <div class="alert alert-error">
                {{ $errors->first() }}
            </div>
            @endif --}}

            <form action="{{ route('register') }}" method="POST" id="registerForm">
                @csrf

                <!-- Step 1: Información Personal -->
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

                <!-- Step 2: Información de Cuenta -->
                <div class="form-step" id="step2">
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" placeholder="tu@email.com" required value="{{ old('email') }}">
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
                        <button type="button" class="btn btn-next" onclick="nextStep(3)">Siguiente</button>
                    </div>
                </div>

                <!-- Step 3: Preferencias y Confirmación -->
                <div class="form-step" id="step3">
                    <div class="form-group">
                        <label for="genero_preferencia">¿Qué te interesa?</label>
                        <select id="genero_preferencia" name="genero_preferencia" required>
                            <option value="">Selecciona una opción</option>
                            <option value="mujer">Ropa de Mujer</option>
                            <option value="hombre">Ropa de Hombre</option>
                            <option value="ambos">Ambos</option>
                        </select>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="newsletter" name="newsletter" checked>
                        <label for="newsletter">
                            Quiero recibir ofertas exclusivas y novedades
                        </label>
                    </div>

                  

                    <div class="btn-group">
                        <button type="button" class="btn btn-back" onclick="prevStep(2)">Atrás</button>
                        <button type="submit" class="btn btn-submit">Crear Cuenta</button>
                    </div>
                </div>
            </form>

            <div class="divider">o regístrate con</div>

            <div class="social-login">
                <!-- Botones sociales temporalmente deshabilitados para evitar rutas no definidas -->
                <button class="social-btn" disabled title="Próximamente">
                    <svg width="20" height="20" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Google
                </button>

                <button class="social-btn" disabled title="Próximamente">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#1877F2">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    Facebook
                </button>
            </div>

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
                    setTimeout(() => {
                        input.style.borderColor = '#e2e8f0';
                    }, 2000);
                }
            });

            if (!isValid) return;

            // Validar contraseñas en paso 2
            if (currentStep === 2) {
                const password = document.getElementById('password').value;
                const confirmation = document.getElementById('password_confirmation').value;
                if (password !== confirmation) {
                    document.getElementById('password_confirmation').style.borderColor = '#f56565';
                    setTimeout(() => {
                        document.getElementById('password_confirmation').style.borderColor = '#e2e8f0';
                    }, 2000);
                    return;
                }
            }

            document.getElementById(`step${currentStep}`).classList.remove('active');
            document.getElementById(`step${step}`).classList.add('active');

            document.getElementById(`stepNum${currentStep}`).classList.remove('active');
            document.getElementById(`stepNum${currentStep}`).classList.add('completed');
            document.getElementById(`stepNum${step}`).classList.add('active');

            const progress = (step / 3) * 100;
            document.getElementById('progressFill').style.width = progress + '%';

            currentStep = step;
        }

        function prevStep(step) {
            document.getElementById(`step${currentStep}`).classList.remove('active');
            document.getElementById(`step${step}`).classList.add('active');

            document.getElementById(`stepNum${currentStep}`).classList.remove('active');
            document.getElementById(`stepNum${step}`).classList.remove('completed');
            document.getElementById(`stepNum${step}`).classList.add('active');

            const progress = (step / 3) * 100;
            document.getElementById('progressFill').style.width = progress + '%';

            currentStep = step;
        }

        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthBar = document.getElementById('passwordStrength');
            const strengthText = document.getElementById('strengthText');
            
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            strengthBar.className = 'password-strength-bar';
            
            if (strength >= 4) {
                strengthBar.classList.add('strong');
                strengthText.textContent = 'Contraseña fuerte';
                strengthText.style.color = '#48bb78';
            } else if (strength >= 2) {
                strengthBar.classList.add('medium');
                strengthText.textContent = 'Contraseña media';
                strengthText.style.color = '#ed8936';
            } else if (strength >= 1) {
                strengthBar.classList.add('weak');
                strengthText.textContent = 'Contraseña débil';
                strengthText.style.color = '#f56565';
            } else {
                strengthText.textContent = '';
            }
        }

        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const terms = document.getElementById('terms').checked;
            if (!terms) {
                e.preventDefault();
                alert('Debes aceptar los términos y condiciones');
            }
        });
    </script>
</body>
</html>