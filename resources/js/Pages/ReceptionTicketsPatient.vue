<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const services = ref([]);
const loadingServiceId = ref(null);
const message = ref('');

async function loadServices() {
    const res = await axios.get('/api/services');
    services.value = res.data.data;
}

async function genererTicket(service) {
    message.value = '';
    loadingServiceId.value = service.id;

    try {
        const res = await axios.post('/api/kiosk/tickets', {
            service_id: service.id,
            priorite: 0,
        });

        message.value = res.data.message;

        window.open(res.data.print_url, '_blank');
    } finally {
        loadingServiceId.value = null;
    }
}

onMounted(loadServices);
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-white p-10">
        <div class="text-center mb-10">
            <a
                href="/"
                class="inline-flex items-center text-blue-600 font-medium mb-4"
            >
                ← Retour accueil
            </a>
            <h1 class="text-5xl font-black">
                BIENVENUE
            </h1>
            <p class="text-2xl text-slate-300 mt-3">
                Touchez le service souhaité pour prendre un ticket
            </p>
        </div>

        <div v-if="message" class="bg-green-500 text-white p-4 rounded-xl text-center text-xl mb-8">
            {{ message }}
        </div>

        <div class="grid grid-cols-3 gap-8">
            <button
                v-for="service in services"
                :key="service.id"
                @click="genererTicket(service)"
                class="bg-white text-slate-900 rounded-3xl p-10 shadow-xl hover:scale-105 transition text-center"
            >
                <div class="text-5xl font-black mb-4">
                    {{ service.code_service }}
                </div>

                <div class="text-2xl font-bold">
                    {{ service.nom_service }}
                </div>

                <div class="mt-6 text-blue-600 font-bold">
                    <span v-if="loadingServiceId === service.id">
                        Génération...
                    </span>
                    <span v-else>
                        Prendre un ticket
                    </span>
                </div>
            </button>
        </div>
    </div>
</template>