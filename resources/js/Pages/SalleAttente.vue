<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';

const currentCall = ref(null);
const lastCalls = ref([]);
const now = ref(new Date());

let hideTimer = null;
let clockTimer = null;

const currentDate = computed(() => {
    return now.value.toLocaleDateString('fr-FR', {
        weekday: 'long',
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });
});

const currentTime = computed(() => {
    return now.value.toLocaleTimeString('fr-FR', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    });
});

function parler(message) {
    if (!window.speechSynthesis) return;

    window.speechSynthesis.cancel();

    const voice = new SpeechSynthesisUtterance(message);
    voice.lang = 'fr-FR';
    voice.rate = 0.85;
    voice.pitch = 1;
    voice.volume = 1;

    window.speechSynthesis.speak(voice);
}

function buildMessage(data) {
    const ticket = data.numero_ticket ?? '';
    const cabinet = data.cabinet ?? 'cabinet concerné';
    const service = data.service ?? '';

    return data.message || `Ticket ${ticket}, veuillez vous présenter au ${cabinet}. ${service}`;
}

function handleCall(data) {
    currentCall.value = data;

    lastCalls.value.unshift({
        ...data,
        heure: new Date().toLocaleTimeString('fr-FR', {
            hour: '2-digit',
            minute: '2-digit',
        }),
    });

    lastCalls.value = lastCalls.value.slice(0, 6);

    parler(buildMessage(data));

    if (hideTimer) {
        clearTimeout(hideTimer);
    }

    hideTimer = setTimeout(() => {
        currentCall.value = null;
    }, 15000);
}

onMounted(() => {
    console.log('Salle attente montée');
    console.log('Echo:', window.Echo);

    if (!window.Echo) {
        console.error('Echo non chargé');
        return;
    }

    window.Echo.channel('salle-attente')
        .listen('.ticket.called', (data) => {
            console.log('Événement reçu:', data);
            handleCall(data);
        });
});
// onMounted(() => {
//     clockTimer = setInterval(() => {
//         now.value = new Date();
//     }, 1000);

//     if (window.Echo) {
//         window.Echo.channel('salle-attente')
//             .listen('.ticket.called', (data) => {
//                 handleCall(data);
//             });
//     }
// });

onBeforeUnmount(() => {
    if (hideTimer) clearTimeout(hideTimer);
    if (clockTimer) clearInterval(clockTimer);

    if (window.Echo) {
        window.Echo.leave('salle-attente');
    }

    if (window.speechSynthesis) {
        window.speechSynthesis.cancel();
    }
});
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-white overflow-hidden">
        <!-- HEADER -->
        <header class="h-28 px-10 flex items-center justify-between bg-slate-900 border-b border-slate-700">
            <div>
                <a
                    href="/"
                    class="inline-flex items-center text-blue-600 font-medium mb-4"
                >
                    ← Retour accueil
                </a>
                <h1 class="text-4xl font-black tracking-wide">
                    HÔPITAL CIMAK
                </h1>
                <p class="text-slate-300 text-lg">
                    Système de gestion des files d’attente
                </p>
            </div>

            <div class="text-right">
                <div class="text-4xl font-bold">
                    {{ currentTime }}
                </div>
                <div class="text-slate-300 capitalize">
                    {{ currentDate }}
                </div>
            </div>
        </header>

        <!-- NOTIFICATION SUPERPOSÉE -->
        <transition name="zoom">
            <div
                v-if="currentCall"
                class="fixed inset-0 bg-black/85 flex items-center justify-center z-50 px-8"
            >
                <div class="bg-white text-slate-900 rounded-[2rem] shadow-2xl w-full max-w-4xl text-center overflow-hidden">
                    <div class="bg-red-600 text-white py-6">
                        <h2 class="text-5xl font-black tracking-wide">
                            PATIENT APPELÉ
                        </h2>
                    </div>

                    <div class="p-14">
                        <div class="text-8xl font-black text-slate-950 mb-8">
                            {{ currentCall.numero_ticket }}
                        </div>

                        <div class="text-4xl font-bold mb-4">
                            {{ currentCall.cabinet || 'Cabinet' }}
                        </div>

                        <div class="text-3xl text-slate-600 mb-8">
                            {{ currentCall.service || 'Service médical' }}
                        </div>

                        <div class="text-2xl bg-slate-100 rounded-2xl py-5 px-8">
                            Veuillez vous présenter maintenant.
                        </div>
                    </div>
                </div>
            </div>
        </transition>

        <!-- CONTENU PRINCIPAL -->
        <main class="p-10 grid grid-cols-12 gap-8 h-[calc(100vh-7rem)]">
            <!-- DERNIER APPEL -->
            <section class="col-span-7 bg-slate-900 rounded-3xl border border-slate-700 p-8 flex flex-col justify-center">
                <p class="text-slate-400 text-2xl mb-4">
                    Dernier ticket appelé
                </p>

                <div v-if="lastCalls.length" class="text-center">
                    <div class="text-9xl font-black text-yellow-400 mb-6">
                        {{ lastCalls[0].numero_ticket }}
                    </div>

                    <div class="text-4xl font-bold mb-3">
                        {{ lastCalls[0].cabinet }}
                    </div>

                    <div class="text-2xl text-slate-300">
                        {{ lastCalls[0].service }}
                    </div>
                </div>

                <div v-else class="text-center text-slate-400">
                    <div class="text-6xl mb-6">⏳</div>
                    <p class="text-3xl">
                        En attente du prochain appel
                    </p>
                </div>
            </section>

            <!-- HISTORIQUE -->
            <section class="col-span-5 bg-slate-900 rounded-3xl border border-slate-700 p-8">
                <h2 class="text-3xl font-black mb-6">
                    Derniers appels
                </h2>

                <div v-if="lastCalls.length" class="space-y-4">
                    <div
                        v-for="call in lastCalls"
                        :key="call.id"
                        class="bg-slate-800 rounded-2xl p-5 flex items-center justify-between border border-slate-700"
                    >
                        <div>
                            <div class="text-4xl font-black text-yellow-400">
                                {{ call.numero_ticket }}
                            </div>
                            <div class="text-slate-300 text-lg">
                                {{ call.cabinet }} — {{ call.service }}
                            </div>
                        </div>

                        <div class="text-slate-400 text-xl">
                            {{ call.heure }}
                        </div>
                    </div>
                </div>

                <div v-else class="text-slate-400 text-xl">
                    Aucun appel pour le moment.
                </div>
            </section>
        </main>

        <!-- BANDEAU BAS -->
        <footer class="fixed bottom-0 left-0 right-0 bg-yellow-400 text-slate-950 py-4 overflow-hidden">
            <div class="animate-marquee whitespace-nowrap text-2xl font-bold">
                Veuillez rester attentif à l’écran et à l’annonce vocale — Préparez votre ticket avant d’entrer au cabinet — Merci pour votre patience.
            </div>
        </footer>
    </div>
</template>

<style scoped>
.zoom-enter-active,
.zoom-leave-active {
    transition: all 0.35s ease;
}

.zoom-enter-from,
.zoom-leave-to {
    opacity: 0;
    transform: scale(0.9);
}

@keyframes marquee {
    from {
        transform: translateX(100%);
    }

    to {
        transform: translateX(-100%);
    }
}

.animate-marquee {
    display: inline-block;
    animation: marquee 25s linear infinite;
}
</style>