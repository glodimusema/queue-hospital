<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const services = ref([]);
const form = ref({
    nom_service: '',
    code_service: '',
    description: '',
    statut: 'ACTIF',
});

const message = ref('');

async function loadServices() {
    const res = await axios.get('/api/services');
    services.value = res.data.data;
}

async function saveService() {
    const res = await axios.post('/api/services', form.value);
    message.value = res.data.message;

    form.value = {
        nom_service: '',
        code_service: '',
        description: '',
        statut: 'ACTIF',
    };

    await loadServices();
}

onMounted(loadServices);
</script>

<template>
    <div class="p-8">
        <a href="/" class="text-blue-600">← Retour accueil</a>

        <h1 class="text-3xl font-bold my-6">Configuration des services</h1>

        <div v-if="message" class="bg-green-100 text-green-700 p-4 rounded mb-4">
            {{ message }}
        </div>

        <div class="bg-white rounded shadow p-6 mb-8">
            <div class="grid grid-cols-2 gap-4">
                <input v-model="form.nom_service" class="border p-2 rounded" placeholder="Nom du service">
                <input v-model="form.code_service" class="border p-2 rounded" placeholder="Code service">
                <input v-model="form.description" class="border p-2 rounded col-span-2" placeholder="Description">
            </div>

            <button @click="saveService" class="mt-4 bg-blue-600 text-white px-5 py-2 rounded">
                Enregistrer
            </button>
        </div>

        <table class="w-full border bg-white">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2">Code</th>
                    <th class="border p-2">Service</th>
                    <th class="border p-2">Description</th>
                    <th class="border p-2">Statut</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="s in services" :key="s.id">
                    <td class="border p-2 font-bold">{{ s.code_service }}</td>
                    <td class="border p-2">{{ s.nom_service }}</td>
                    <td class="border p-2">{{ s.description }}</td>
                    <td class="border p-2">{{ s.statut }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>