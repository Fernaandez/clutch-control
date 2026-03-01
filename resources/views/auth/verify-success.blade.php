<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Correu Verificat - Clutch Control</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            neon: '#0CE1B5',
                            dark: '#1A1A1A',
                            black: '#0A0A0A',
                            surface: '#121212'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-brand-black min-h-screen flex flex-col justify-center items-center px-6 py-12">
    <div class="w-full max-w-md bg-brand-surface border border-brand-dark p-8 rounded-2xl shadow-2xl text-center relative overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-2 bg-brand-neon rounded-b-xl shadow-[0_0_30px_rgba(12,225,181,0.8)]"></div>

        <div class="flex justify-center mb-6 mt-4">
            <div class="p-4 border-2 border-brand-neon rounded-full shadow-[0_0_15px_rgba(12,225,181,0.2)] bg-brand-neon/10 text-brand-neon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-12 h-12">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
        </div>

        <h2 class="text-3xl font-black text-white uppercase tracking-tight mb-4">Correu <span class="text-brand-neon">Verificat!</span></h2>

        <div class="mb-8 text-sm text-gray-400 leading-relaxed">
            La teva adreça de correu s'ha verificat amb èxit. Ja formes part oficialment de la comunitat.
            <br><br>
            Pots tancar aquesta pestanya i tornar a l'aplicació per donar-li gas.
        </div>

        <button onclick="window.close()" class="w-full bg-brand-neon text-brand-black px-6 py-4 rounded-xl font-black uppercase tracking-wider text-sm hover:bg-white hover:text-black transition duration-300 shadow-[0_0_15px_rgba(12,225,181,0.3)]">
            Tancar Finestra
        </button>
    </div>
</body>
</html>
