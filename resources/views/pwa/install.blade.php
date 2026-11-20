<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#142D4E">
    <meta name="description" content="Install the Department of Roads app.">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <title>Install DoR App | Department of Roads</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            color: #263746;
            background: linear-gradient(145deg, #edf3f8, #f7f8fa);
            font-family: Inter, "Segoe UI", Arial, sans-serif;
        }
        .install-card {
            width: min(100%, 440px);
            padding: 38px 30px;
            text-align: center;
            background: #fff;
            border: 1px solid #e4eaf0;
            border-radius: 22px;
            box-shadow: 0 18px 55px rgba(20, 45, 78, .12);
        }
        .app-mark {
            width: 76px;
            height: 76px;
            display: grid;
            place-items: center;
            margin: 0 auto 22px;
            color: #fff;
            background: #142D4E;
            border-radius: 20px;
            font-size: 27px;
            font-weight: 800;
            letter-spacing: -2px;
        }
        h1 { margin: 0; color: #142D4E; font-size: 25px; }
        .department { margin: 9px 0 28px; color: #64748b; font-size: 14px; }
        .install-button, .open-link {
            width: 100%;
            min-height: 50px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 12px;
            color: #fff;
            background: #1769aa;
            font-size: 16px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }
        .install-button:hover, .open-link:hover { background: #142D4E; }
        .status {
            min-height: 22px;
            margin: 16px 0 0;
            color: #526477;
            font-size: 13px;
            line-height: 1.6;
        }
        .home-link {
            display: inline-block;
            margin-top: 22px;
            color: #1769aa;
            font-size: 14px;
            text-decoration: none;
        }
        .home-link:hover { text-decoration: underline; }
        [hidden] { display: none !important; }
        @media (max-width: 480px) {
            body { padding: 16px; }
            .install-card { padding: 32px 22px; }
        }
    </style>
</head>
<body>
    <main class="install-card">
        <div class="app-mark" aria-hidden="true">DoR</div>
        <h1>Install DoR App</h1>
        <p class="department">Department of Roads · Government of Nepal</p>

        <button type="button" class="install-button" id="installButton">
            Install DoR App
        </button>

        <p class="status" id="installStatus" role="status" aria-live="polite"></p>

        <a class="open-link" id="openAppLink" href="/" hidden>Open DoR App</a>
        <a class="home-link" href="/">Department of Roads Website</a>
    </main>

    <script>
        let deferredInstallPrompt = null;

        const installButton = document.getElementById('installButton');
        const installStatus = document.getElementById('installStatus');
        const openAppLink = document.getElementById('openAppLink');

        const isStandalone = window.matchMedia('(display-mode: standalone)').matches
            || window.navigator.standalone === true;

        const isIOS = /iphone|ipad|ipod/i.test(navigator.userAgent)
            || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

        if (isStandalone) {
            installButton.hidden = true;
            openAppLink.hidden = false;
        } else if (isIOS) {
            installStatus.textContent = 'Safari मा Share खोल्नुहोस् र Add to Home Screen छान्नुहोस्।';
        }

        window.addEventListener('beforeinstallprompt', (event) => {
            event.preventDefault();
            deferredInstallPrompt = event;
            installButton.hidden = false;
            installStatus.textContent = '';
        });

        window.addEventListener('appinstalled', () => {
            deferredInstallPrompt = null;
            installButton.hidden = true;
            openAppLink.hidden = false;
            installStatus.textContent = 'DoR App installed.';
        });

        installButton.addEventListener('click', async () => {
            if (deferredInstallPrompt) {
                deferredInstallPrompt.prompt();
                const choice = await deferredInstallPrompt.userChoice;
                deferredInstallPrompt = null;

                if (choice.outcome === 'accepted') {
                    installStatus.textContent = 'Installation started.';
                } else {
                    installStatus.textContent = 'Installation was cancelled.';
                }
                return;
            }

            if (isIOS) {
                installStatus.textContent = 'Safari: Share → Add to Home Screen.';
            } else {
                installStatus.textContent = 'Browser menu खोल्नुहोस् र Install app छान्नुहोस्।';
            }
        });

        if ('serviceWorker' in navigator && window.isSecureContext) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js', {
                    scope: '/',
                    updateViaCache: 'none'
                }).catch(error => console.warn('DoR service worker:', error));
            });
        }
    </script>
</body>
</html>
