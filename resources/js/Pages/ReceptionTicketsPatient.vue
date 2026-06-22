<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const cabinets = ref([]);
const loadingCabinetId = ref(null);
const message = ref('');

async function loadCabinets() {
    const res = await axios.get('/api/cabinets-actifs');
    cabinets.value = res.data.data;
}

async function genererTicket(cabinet) {
    message.value = '';
    loadingCabinetId.value = cabinet.id;

    try {
        const res = await axios.post('/api/kiosk/tickets', {
            cabinet_id: cabinet.id,
            priorite: 0,
        });

        message.value = res.data.message;

        window.open(res.data.print_url, '_blank');
    } finally {
        loadingCabinetId.value = null;
    }
}

onMounted(loadCabinets);
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-white p-10">
        <div class="text-center mb-10">
            <a href="/" class="inline-flex items-center text-blue-400 font-medium mb-4">
                ← Retour accueil
            </a>

            <h1 class="text-5xl font-black">
                BIENVENUE
            </h1>

            <p class="text-2xl text-slate-300 mt-3">
                Touchez le cabinet souhaité pour prendre un ticket
            </p>
        </div>

        <div
            v-if="message"
            class="bg-green-500 text-white p-4 rounded-xl text-center text-xl mb-8"
        >
            {{ message }}
        </div>

        <div class="grid grid-cols-3 gap-8">
            <button
                v-for="cabinet in cabinets"
                :key="cabinet.id"
                @click="genererTicket(cabinet)"
                class="bg-white text-slate-900 rounded-3xl p-10 shadow-xl hover:scale-105 transition text-center"
            >
                <div class="text-4xl font-black mb-4 text-blue-700">
                    {{ cabinet.numero_cabinet }}
                </div>

                <div class="text-2xl font-bold">
                    {{ cabinet.nom_cabinet }}
                </div>

                <div class="text-lg text-slate-500 mt-2">
                    {{ cabinet.service?.nom_service }}
                </div>

                <div class="mt-6 text-blue-600 font-bold">
                    <span v-if="loadingCabinetId === cabinet.id">
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