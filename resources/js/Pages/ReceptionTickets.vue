<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const patients = ref([]);
const services = ref([]);
const cabinets = ref([]);
const tickets = ref([]);

const form = ref({
    patient_id: '',
    service_id: '',
    cabinet_id: '',
    priorite: 0,
});

const message = ref('');

async function loadData() {
    const [patientsRes, servicesRes, ticketsRes] = await Promise.all([
        axios.get('/api/patients'),
        axios.get('/api/services'),
        axios.get('/api/tickets'),
    ]);

    patients.value = patientsRes.data.data;
    services.value = servicesRes.data.data;
    tickets.value = ticketsRes.data.data;
}

async function loadCabinets() {
    form.value.cabinet_id = '';

    if (!form.value.service_id) {
        cabinets.value = [];
        return;
    }

    const res = await axios.get(`/api/cabinets?service_id=${form.value.service_id}`);
    cabinets.value = res.data.data;
}

async function createTicket() {
    message.value = '';

    const res = await axios.post('/api/tickets', form.value);

    message.value = res.data.message;

    form.value = {
        patient_id: '',
        service_id: '',
        cabinet_id: '',
        priorite: 0,
    };

    cabinets.value = [];

    await loadData();
}

onMounted(loadData);
</script>

<template>
    <div class="p-8">
        <h1 class="text-3xl font-bold mb-6">Réception — Création des tickets</h1>

        <div v-if="message" class="bg-green-100 text-green-700 p-4 rounded mb-4">
            {{ message }}
        </div>

        <div class="bg-white rounded shadow p-6 mb-8">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label>Patient</label>
                    <select v-model="form.patient_id" class="w-full border rounded p-2">
                        <option value="">Patient non identifié</option>
                        <option v-for="p in patients" :key="p.id" :value="p.id">
                            {{ p.nom }} {{ p.postnom }} {{ p.prenom }}
                        </option>
                    </select>
                </div>

                <div>
                    <label>Service</label>
                    <select v-model="form.service_id" @change="loadCabinets" class="w-full border rounded p-2">
                        <option value="">-- Choisir --</option>
                        <option v-for="s in services" :key="s.id" :value="s.id">
                            {{ s.nom_service }}
                        </option>
                    </select>
                </div>

                <div>
                    <label>Cabinet</label>
                    <select v-model="form.cabinet_id" class="w-full border rounded p-2">
                        <option value="">Non affecté</option>
                        <option v-for="c in cabinets" :key="c.id" :value="c.id">
                            {{ c.nom_cabinet }}
                        </option>
                    </select>
                </div>

                <div>
                    <label>Priorité</label>
                    <select v-model="form.priorite" class="w-full border rounded p-2">
                        <option :value="0">Normal</option>
                        <option :value="1">Urgent</option>
                    </select>
                </div>
            </div>

            <button
                @click="createTicket"
                class="mt-6 bg-blue-600 text-white px-6 py-3 rounded"
            >
                Générer le ticket
            </button>
        </div>

        <h2 class="text-2xl font-bold mb-4">Tickets du jour</h2>

        <table class="w-full border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2">Ticket</th>
                    <th class="border p-2">Patient</th>
                    <th class="border p-2">Service</th>
                    <th class="border p-2">Cabinet</th>
                    <th class="border p-2">Statut</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="t in tickets" :key="t.id">
                    <td class="border p-2 font-bold">{{ t.numero_ticket }}</td>
                    <td class="border p-2">
                        {{ t.patient?.nom ?? 'Non identifié' }}
                    </td>
                    <td class="border p-2">{{ t.service?.nom_service }}</td>
                    <td class="border p-2">{{ t.cabinet?.nom_cabinet ?? '-' }}</td>
                    <td class="border p-2">{{ t.statut }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>