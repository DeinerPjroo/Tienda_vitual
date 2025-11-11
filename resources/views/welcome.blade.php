<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles -->
        <style>
            /* Estilos base simplificados */
            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }

            body {
                font-family: 'Instrument Sans', sans-serif;
                background-color: #FDFDFC;
                color: #1b1b18;
                display: flex;
                padding: 1.5rem;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                flex-direction: column;
            }

            header {
                width: 100%;
                max-width: 56rem;
                font-size: 0.875rem;
                margin-bottom: 1.5rem;
            }

            nav {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 1rem;
            }

            a {
                display: inline-block;
                padding: 0.5rem 1.25rem;
                border: 1px solid rgba(25, 20, 0, 0.21);
                color: #1b1b18;
                border-radius: 0.125rem;
                font-size: 0.875rem;
                line-height: 1.5;
                text-decoration: none;
                transition: all 0.15s;
            }

            a:hover {
                border-color: rgba(25, 21, 1, 0.29);
            }

            .border-transparent {
                border-color: transparent;
            }

            .border-transparent:hover {
                border-color: rgba(25, 20, 0, 0.21);
            }

            @media (min-width: 1024px) {
                body {
                    padding: 2rem;
                }
            }

            @media (max-width: 1023px) {
                header {
                    max-width: 335px;
                }
            }
        </style>
    </head>
    <body>
        <header>
            @if (Route::has('login'))
                <nav>
                    @auth
                        <a href="{{ url('/dashboard') }}">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="border-transparent">
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>
    </body>
</html>
