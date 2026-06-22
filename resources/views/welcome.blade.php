<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Queue Hospital</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-100 min-h-screen">

<div class="max-w-5xl mx-auto py-12 px-6">
    <h1 class="text-4xl font-black mb-2">Queue Hospital</h1>
    <p class="text-slate-600 mb-10">Tableau d’accès rapide</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="/patient/tickets" class="bg-white p-8 rounded-2xl shadow hover:shadow-lg">
            <h2 class="text-2xl font-bold">Borne tickets</h2>
            <p class="text-slate-500 mt-2">Génération automatique des tickets</p>
        </a>

        <a href="/salle-attente" class="bg-white p-8 rounded-2xl shadow hover:shadow-lg">
            <h2 class="text-2xl font-bold">Salle d’attente</h2>
            <p class="text-slate-500 mt-2">Écran TV avec annonces vocales</p>
        </a>

        <a href="/cabinet/medecin" class="bg-white p-8 rounded-2xl shadow hover:shadow-lg">
            <h2 class="text-2xl font-bold">Cabinet médecin</h2>
            <p class="text-slate-500 mt-2">Appeler les patients</p>
        </a>

        <a href="/reception/tickets" class="bg-white p-8 rounded-2xl shadow hover:shadow-lg">
            <h2 class="text-2xl font-bold">Réception</h2>
            <p class="text-slate-500 mt-2">Création classique des tickets</p>
        </a>

        <a href="/admin/services" class="bg-white p-8 rounded-2xl shadow hover:shadow-lg">
            <h2 class="text-2xl font-bold">Services</h2>
            <p class="text-slate-500 mt-2">Configurer les services</p>
        </a>

        <a href="/admin/cabinets" class="bg-white p-8 rounded-2xl shadow hover:shadow-lg">
            <h2 class="text-2xl font-bold">Cabinets</h2>
            <p class="text-slate-500 mt-2">Configurer les cabinets</p>
        </a>
    </div>
</div>

</body>
</html>