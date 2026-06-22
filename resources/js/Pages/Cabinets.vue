<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const cabinets = ref([]);
const services = ref([]);

const form = ref({
    service_id: '',
    nom_cabinet: '',
    numero_cabinet: '',
    localisation: '',
    statut: 'ACTIF',
});

const message = ref('');

async function loadData() {
    const [cabinetsRes, servicesRes] = await Promise.all([
        axios.get('/api/cabinets'),
        axios.get('/api/services'),
    ]);

    cabinets.value = cabinetsRes.data.data;
    services.value = servicesRes.data.data;
}

async function saveCabinet() {
    try {
        const res = await axios.post('/api/cabinets', form.value);

        message.value = res.data.message;

        form.value = {
            service_id: '',
            nom_cabinet: '',
            numero_cabinet: '',
            localisation: '',
            statut: 'ACTIF',
        };

        await loadData();
    } catch (e) {
        console.error(e);
        alert('Erreur lors de l’enregistrement');
    }
}

onMounted(loadData);
</script>

<template>
    <div class="p-8">
        <a
            href="/"
            class="inline-flex items-center text-blue-600 font-medium mb-4"
        >
            ← Retour accueil
        </a>

        <h1 class="text-3xl font-bold mb-6">
            Configuration des cabinets
        </h1>

        <div
            v-if="message"
            class="bg-green-100 text-green-700 p-4 rounded mb-6"
        >
            {{ message }}
        </div>

        <!-- Formulaire -->
        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block mb-1 font-medium">
                        Service
                    </label>

                    <select
                        v-model="form.service_id"
                        class="w-full border rounded p-2"
                    >
                        <option value="">
                            -- Choisir --
                        </option>

                        <option
                            v-for="s in services"
                            :key="s.id"
                            :value="s.id"
                        >
                            {{ s.nom_service }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1 font-medium">
                        Nom du cabinet
                    </label>

                    <input
                        v-model="form.nom_cabinet"
                        class="w-full border rounded p-2"
                        placeholder="Cabinet Médecine Générale"
                    >
                </div>

                <div>
                    <label class="block mb-1 font-medium">
                        Numéro cabinet
                    </label>

                    <input
                        v-model="form.numero_cabinet"
                        class="w-full border rounded p-2"
                        placeholder="CAB-GEN-01"
                    >
                </div>

                <div>
                    <label class="block mb-1 font-medium">
                        Localisation
                    </label>

                    <input
                        v-model="form.localisation"
                        class="w-full border rounded p-2"
                        placeholder="Bloc A"
                    >
                </div>
            </div>

            <button
                @click="saveCabinet"
                class="mt-6 bg-blue-600 text-white px-5 py-2 rounded-lg"
            >
                Enregistrer
            </button>
        </div>

        <!-- Liste -->
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="border p-3 text-left">
                            Cabinet
                        </th>

                        <th class="border p-3 text-left">
                            Service
                        </th>

                        <th class="border p-3 text-left">
                            Numéro
                        </th>

                        <th class="border p-3 text-left">
                            Localisation
                        </th>

                        <th class="border p-3 text-left">
                            Statut
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="cabinet in cabinets"
                        :key="cabinet.id"
                    >
                        <td class="border p-3">
                            {{ cabinet.nom_cabinet }}
                        </td>

                        <td class="border p-3">
                            {{ cabinet.service?.nom_service }}
                        </td>

                        <td class="border p-3">
                            {{ cabinet.numero_cabinet }}
                        </td>

                        <td class="border p-3">
                            {{ cabinet.localisation }}
                        </td>

                        <td class="border p-3">
                            {{ cabinet.statut }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>