<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica el teu compte - Clutch Control</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #0a0a0f;
            color: #e2e8f0;
        }
        .wrapper {
            max-width: 600px;
            margin: 40px auto;
            background-color: #0a0a0f;
        }
        /* HEADER */
        .header {
            background: linear-gradient(135deg, #0d1117 0%, #111827 100%);
            border-bottom: 2px solid #0ce1b5;
            padding: 32px 40px;
            text-align: center;
        }
        .logo {
            font-size: 28px;
            font-weight: 900;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: #ffffff;
        }
        .logo span {
            color: #0ce1b5;
        }
        .logo-sub {
            font-size: 11px;
            color: #64748b;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            margin-top: 4px;
        }
        /* BODY */
        .content {
            background-color: #111827;
            border: 1px solid #1e293b;
            border-top: none;
            padding: 48px 40px;
        }
        .greeting {
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 16px;
        }
        .text {
            font-size: 15px;
            color: #94a3b8;
            line-height: 1.7;
            margin-bottom: 16px;
        }
        .text strong {
            color: #e2e8f0;
        }
        /* CTA BUTTON */
        .btn-wrapper {
            text-align: center;
            margin: 36px 0;
        }
        .btn {
            display: inline-block;
            background-color: #0ce1b5;
            color: #0a0a0f !important;
            font-size: 15px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 16px 40px;
            border-radius: 10px;
            text-decoration: none;
            box-shadow: 0 0 30px rgba(12, 225, 181, 0.4);
        }
        /* DIVIDER */
        .divider {
            border: none;
            border-top: 1px solid #1e293b;
            margin: 32px 0;
        }
        .fallback {
            font-size: 12px;
            color: #475569;
            line-height: 1.6;
        }
        .fallback a {
            color: #0ce1b5;
            word-break: break-all;
        }
        /* FOOTER */
        .footer {
            background-color: #0d1117;
            border: 1px solid #1e293b;
            border-top: none;
            padding: 24px 40px;
            text-align: center;
        }
        .footer-text {
            font-size: 12px;
            color: #475569;
            line-height: 1.6;
        }
        .footer-brand {
            color: #0ce1b5;
            font-weight: 700;
        }
        /* ALERT BOX */
        .alert {
            background-color: rgba(12, 225, 181, 0.05);
            border-left: 3px solid #0ce1b5;
            border-radius: 6px;
            padding: 14px 18px;
            margin: 24px 0;
            font-size: 13px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- HEADER -->
        <div class="header">
            <div class="logo">CLUTCH <span>CONTROL</span></div>
            <div class="logo-sub">🏍️ La teva app de motos</div>
        </div>

        <!-- CONTENT -->
        <div class="content">
            <p class="greeting">Hola, {{ $userName }}! 👋</p>

            <p class="text">
                Gràcies per registrar-te a <strong>Clutch Control</strong>.<br>
                Per activar el teu compte i poder gestionar les teves motos, rutes i quedades, necessites verificar la teva adreça de correu electrònic.
            </p>

            <div class="btn-wrapper">
                <a href="{{ $verificationUrl }}" class="btn">
                    ✅ Verificar el meu compte
                </a>
            </div>

            <div class="alert">
                ⏳ Aquest enllaç és vàlid durant <strong>60 minuts</strong>. Si expira, pots sol·licitar-ne un de nou des de l'aplicació.
            </div>

            <p class="text">
                Si no has creat cap compte a Clutch Control, pots ignorar aquest correu sense cap problema.
            </p>

            <hr class="divider">

            <p class="fallback">
                Si el botó no funciona, copia i enganxa aquest enllaç al teu navegador:<br>
                <a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a>
            </p>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <p class="footer-text">
                Enviat per <span class="footer-brand">Clutch Control</span> &mdash; La teva plataforma de gestió de motos.<br>
                Si tens qualsevol problema, contacta amb nosaltres a <a href="mailto:clutchcontrolapp@gmail.com" style="color:#0ce1b5;">clutchcontrolapp@gmail.com</a>
            </p>
        </div>
    </div>
</body>
</html>
