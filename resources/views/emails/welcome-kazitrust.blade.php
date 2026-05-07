<!DOCTYPE html>
<html lang="fr" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Bienvenue sur KaziTrust</title>
    <!--[if mso]>
    <noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
    <![endif]-->
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', Helvetica, Arial, sans-serif;
            background-color: #F1F5F9;
            color: #1E293B;
            -webkit-font-smoothing: antialiased;
        }
        a { color: inherit; text-decoration: none; }
        img { display: block; border: 0; }
    </style>
</head>
<body style="background-color:#F1F5F9; padding: 40px 16px;">

{{-- ── Wrapper ──────────────────────────────────────────────── --}}
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" border="0"
       style="max-width:600px; width:100%;">

    {{-- ── HEADER ──────────────────────────────────────────── --}}
    <tr>
        <td style="
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            border-radius: 16px 16px 0 0;
            padding: 40px 48px 36px;
            text-align: center;
        ">
            {{-- Logo / Brand --}}
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr><td align="center">
                <table cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td style="
                        background: linear-gradient(135deg, #2563EB, #1D4ED8);
                        border-radius: 12px;
                        width: 48px; height: 48px;
                        text-align: center; vertical-align: middle;
                        box-shadow: 0 0 24px rgba(37,99,235,0.5);
                        padding: 12px;
                    ">
                        <img src="https://raw.githubusercontent.com/twbs/icons/main/icons/shield-check.svg"
                             width="24" height="24" alt="shield"
                             style="filter: brightness(0) invert(1);">
                    </td>
                    <td style="padding-left: 12px; vertical-align: middle; text-align: left;">
                        <span style="
                            font-family: 'Courier New', monospace;
                            font-size: 20px; font-weight: 700;
                            color: #E2E8F0; letter-spacing: -0.5px;
                        ">Kazi<span style="color:#2563EB;">Trust</span></span>
                    </td>
                </tr>
                </table>
            </td></tr>
            </table>

            {{-- Titre --}}
            <p style="
                margin-top: 32px;
                font-size: 13px; font-weight: 600;
                letter-spacing: 2px; text-transform: uppercase;
                color: #3B82F6;
            ">Bienvenue à bord 🎉</p>

            <h1 style="
                margin-top: 10px;
                font-size: 30px; font-weight: 800;
                color: #F8FAFC; line-height: 1.2;
                letter-spacing: -0.8px;
            ">Votre espace est prêt,<br>{{ $userName }} !</h1>

            <p style="
                margin-top: 14px;
                font-size: 15px; color: #94A3B8; line-height: 1.6;
            ">
                <strong style="color:#E2E8F0;">{{ $companyName }}</strong> a maintenant accès
                à KaziTrust.<br>14 jours d'essai gratuit — aucune carte bancaire requise.
            </p>

            {{-- CTA principal --}}
            <table cellpadding="0" cellspacing="0" border="0" style="margin: 32px auto 0;">
            <tr><td align="center">
                <a href="{{ $loginUrl }}" style="
                    display: inline-block;
                    background: #2563EB;
                    color: #ffffff;
                    font-size: 15px; font-weight: 700;
                    padding: 14px 36px;
                    border-radius: 10px;
                    letter-spacing: 0.2px;
                    box-shadow: 0 0 30px rgba(37,99,235,0.45);
                ">Accéder à mon espace →</a>
            </td></tr>
            </table>
        </td>
    </tr>

    {{-- ── CORPS ───────────────────────────────────────────── --}}
    <tr>
        <td style="
            background: #FFFFFF;
            padding: 48px 48px 40px;
        ">

            {{-- Intro --}}
            <p style="font-size: 15px; color: #475569; line-height: 1.7; margin-bottom: 36px;">
                Merci de nous avoir rejoint. KaziTrust vous permet d'analyser la fiabilité
                de vos partenaires commerciaux en temps réel, grâce à l'intelligence artificielle
                et aux données télécom Nokia. Voici tout ce que vous pouvez faire dès aujourd'hui :
            </p>

            {{-- Fonctionnalités --}}
            <table width="100%" cellpadding="0" cellspacing="0" border="0">

                {{-- Feature 1 --}}
                <tr>
                    <td style="padding-bottom: 20px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td width="52" valign="top">
                                <div style="
                                    width: 44px; height: 44px;
                                    background: #EFF6FF;
                                    border-radius: 10px;
                                    text-align: center;
                                    line-height: 44px;
                                    font-size: 20px;
                                ">🔍</div>
                            </td>
                            <td style="padding-left: 16px; vertical-align: top;">
                                <p style="font-size: 15px; font-weight: 700; color: #1E293B; margin-bottom: 4px;">
                                    Analyse de confiance en temps réel
                                </p>
                                <p style="font-size: 13px; color: #64748B; line-height: 1.6;">
                                    Obtenez un score de fiabilité instantané sur n'importe quel numéro
                                    mobile grâce à nos données télécom enrichies par l'IA.
                                </p>
                            </td>
                        </tr>
                        </table>
                    </td>
                </tr>

                {{-- Divider --}}
                <tr><td style="border-top: 1px solid #F1F5F9; margin-bottom: 20px; padding-bottom: 20px;"></td></tr>

                {{-- Feature 2 --}}
                <tr>
                    <td style="padding-bottom: 20px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td width="52" valign="top">
                                <div style="
                                    width: 44px; height: 44px;
                                    background: #EFF6FF;
                                    border-radius: 10px;
                                    text-align: center;
                                    line-height: 44px;
                                    font-size: 20px;
                                ">🔌</div>
                            </td>
                            <td style="padding-left: 16px; vertical-align: top;">
                                <p style="font-size: 15px; font-weight: 700; color: #1E293B; margin-bottom: 4px;">
                                    Intégration API ultra-simple
                                </p>
                                <p style="font-size: 13px; color: #64748B; line-height: 1.6;">
                                    Connectez vos applications en quelques lignes de code grâce
                                    à vos clés API dédiées et nos webhooks en temps réel.
                                </p>
                            </td>
                        </tr>
                        </table>
                    </td>
                </tr>

                <tr><td style="border-top: 1px solid #F1F5F9; margin-bottom: 20px; padding-bottom: 20px;"></td></tr>

                {{-- Feature 3 --}}
                <tr>
                    <td style="padding-bottom: 20px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td width="52" valign="top">
                                <div style="
                                    width: 44px; height: 44px;
                                    background: #EFF6FF;
                                    border-radius: 10px;
                                    text-align: center;
                                    line-height: 44px;
                                    font-size: 20px;
                                ">📊</div>
                            </td>
                            <td style="padding-left: 16px; vertical-align: top;">
                                <p style="font-size: 15px; font-weight: 700; color: #1E293B; margin-bottom: 4px;">
                                    Dashboard & logs complets
                                </p>
                                <p style="font-size: 13px; color: #64748B; line-height: 1.6;">
                                    Suivez chaque analyse, consultez l'historique complet
                                    et pilotez vos coûts depuis un tableau de bord centralisé.
                                </p>
                            </td>
                        </tr>
                        </table>
                    </td>
                </tr>

                <tr><td style="border-top: 1px solid #F1F5F9; margin-bottom: 20px; padding-bottom: 20px;"></td></tr>

                {{-- Feature 4 --}}
                <tr>
                    <td>
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td width="52" valign="top">
                                <div style="
                                    width: 44px; height: 44px;
                                    background: #EFF6FF;
                                    border-radius: 10px;
                                    text-align: center;
                                    line-height: 44px;
                                    font-size: 20px;
                                ">🛡️</div>
                            </td>
                            <td style="padding-left: 16px; vertical-align: top;">
                                <p style="font-size: 15px; font-weight: 700; color: #1E293B; margin-bottom: 4px;">
                                    Sécurité & conformité garanties
                                </p>
                                <p style="font-size: 13px; color: #64748B; line-height: 1.6;">
                                    Clés API chiffrées, logs d'audit complets et infrastructure
                                    pensée pour les exigences B2B les plus strictes.
                                </p>
                            </td>
                        </tr>
                        </table>
                    </td>
                </tr>

            </table>

            {{-- Bloc "Prochaines étapes" --}}
            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                   style="
                       margin-top: 36px;
                       background: #F8FAFC;
                       border: 1px solid #E2E8F0;
                       border-radius: 12px;
                       padding: 0;
                       overflow: hidden;
                   ">
                <tr>
                    <td style="padding: 24px 28px;">
                        <p style="font-size: 13px; font-weight: 700; color: #2563EB; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 16px;">
                            Vos prochaines étapes
                        </p>
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="padding-bottom: 10px;">
                                    <table cellpadding="0" cellspacing="0" border="0"><tr>
                                        <td style="width:22px; color:#2563EB; font-weight:700; font-size:13px; vertical-align:top; padding-top:1px;">01</td>
                                        <td style="padding-left:10px; font-size:13px; color:#475569; line-height:1.5;">
                                            <a href="{{ $loginUrl }}" style="color:#2563EB; font-weight:600;">Connectez-vous</a>
                                            à votre espace de gestion
                                        </td>
                                    </tr></table>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 10px;">
                                    <table cellpadding="0" cellspacing="0" border="0"><tr>
                                        <td style="width:22px; color:#2563EB; font-weight:700; font-size:13px; vertical-align:top; padding-top:1px;">02</td>
                                        <td style="padding-left:10px; font-size:13px; color:#475569; line-height:1.5;">
                                            Créez votre première <strong style="color:#1E293B;">application</strong> et générez vos clés API
                                        </td>
                                    </tr></table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <table cellpadding="0" cellspacing="0" border="0"><tr>
                                        <td style="width:22px; color:#2563EB; font-weight:700; font-size:13px; vertical-align:top; padding-top:1px;">03</td>
                                        <td style="padding-left:10px; font-size:13px; color:#475569; line-height:1.5;">
                                            Lancez votre première
                                            <a href="{{ $docsUrl }}" style="color:#2563EB; font-weight:600;">requête d'analyse</a>
                                            et constatez le résultat
                                        </td>
                                    </tr></table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            {{-- Support --}}
            <p style="margin-top: 32px; font-size: 14px; color: #64748B; line-height: 1.7;">
                Une question ? Notre équipe est disponible sur
                <a href="{{ $supportUrl }}" style="color: #2563EB; font-weight: 600;">le support</a>
                ou directement par email. Nous répondons sous 24h.
            </p>

            <p style="margin-top: 24px; font-size: 14px; color: #1E293B; line-height: 1.6;">
                À très vite,<br>
                <strong>L'équipe KaziTrust</strong>
            </p>

        </td>
    </tr>

    {{-- ── FOOTER ───────────────────────────────────────────── --}}
    <tr>
        <td style="
            background: #F8FAFC;
            border-top: 1px solid #E2E8F0;
            border-radius: 0 0 16px 16px;
            padding: 28px 48px;
            text-align: center;
        ">
            <p style="font-size: 12px; color: #94A3B8; line-height: 1.7;">
                Vous recevez cet email car vous venez de créer un compte sur
                <strong style="color:#64748B;">KaziTrust</strong>.<br>
                © {{ date('Y') }} KaziTrust · Tous droits réservés.
            </p>
            <p style="margin-top: 10px;">
                <a href="{{ $loginUrl }}" style="font-size: 12px; color: #94A3B8; margin: 0 10px;">Connexion</a>
                <a href="{{ $docsUrl }}" style="font-size: 12px; color: #94A3B8; margin: 0 10px;">Documentation</a>
                <a href="{{ $supportUrl }}" style="font-size: 12px; color: #94A3B8; margin: 0 10px;">Support</a>
            </p>
        </td>
    </tr>

</table>
</td></tr>
</table>

</body>
</html>