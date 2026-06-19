<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const services = ref([]);
const cabinets = ref([]);
const tickets = ref([]);

const serviceId = ref('');
const cabinetId = ref('');
const message = ref('');
const loading = ref(false);

const cabinetsDuService = computed(() => {
    if (!serviceId.value) return [];
    return cabinets.value.filter(c => Number(c.service_id) === Number(serviceId.value));
});

async function loadInitialData() {
    const [servicesRes, cabinetsRes] = await Promise.all([
        axios.get('/api/services'),
        axios.get('/api/cabinets'),
    ]);

    services.value = servicesRes.data.data;
    cabinets.value = cabinetsRes.data.data;
}

async function loadTicketsByService() {
    tickets.value = [];
    cabinetId.value = '';
    message.value = '';

    if (!serviceId.value) return;

    const cabinet = cabinets.value.find(c => Number(c.service_id) === Number(serviceId.value));
    cabinetId.value = cabinet?.id ?? '';

    loading.value = true;

    try {
        const res = await axios.get(
            `/api/tickets?statut=EN_ATTENTE,APPELE,EN_CONSULTATION&service_id=${serviceId.value}`
        );

        tickets.value = res.data.data;
    } finally {
        loading.value = false;
    }
}

async function appelerTicket(ticket) {
    if (!cabinetId.value) {
        message.value = 'Aucun cabinet actif n’est lié à ce service.';
        return;
    }

    const res = await axios.post(`/api/tickets/${ticket.id}/call`, {
        cabinet_id: cabinetId.value,
    });

    message.value = res.data.message;
    await loadTicketsByService();
}

async function rappelerTicket(ticket) {
    const res = await axios.post(`/api/tickets/${ticket.id}/recall`);
    message.value = res.data.message;
    await loadTicketsByService();
}

async function changerStatut(ticket, statut) {
    const res = await axios.post(`/api/tickets/${ticket.id}/status`, { statut });
    message.value = res.data.message;
    await loadTicketsByService();
}

onMounted(loadInitialData);
</script>

<template>
    <div class="p-8 bg-gray-50 min-h-screen">
        <h1 class="text-3xl font-bold mb-6">
            Cabinet médecin — Appel des patients
        </h1>

        <div v-if="message" class="bg-blue-100 text-blue-700 p-4 rounded mb-4">
            {{ message }}
        </div>

        <div class="bg-white rounded shadow p-6 mb-8">
            <label class="font-bold">Service / Cabinet</label>

            <select
                v-model="serviceId"
                @change="loadTicketsByService"
                class="w-full border rounded p-3 mt-2"
            >
                <option value="">-- Sélectionner le service --</option>
                <option v-for="s in services" :key="s.id" :value="s.id">
                    {{ s.nom_service }}
                </option>
            </select>

            <div v-if="serviceId" class="mt-4 text-gray-700">
                Cabinet utilisé :
                <strong>
                    {{ cabinetsDuService[0]?.nom_cabinet ?? 'Aucun cabinet trouvé' }}
                </strong>
            </div>
        </div>

        <div v-if="!serviceId" class="bg-yellow-100 text-yellow-800 p-6 rounded text-xl">
            Veuillez sélectionner un service pour afficher les patients affectés.
        </div>

        <div v-else>
            <h2 class="text-2xl font-bold mb-4">
                Patients affectés au service
            </h2>

            <div v-if="loading" class="p-6 bg-white rounded shadow">
                Chargement des tickets...
            </div>

            <table v-else class="w-full border bg-white shadow rounded">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border p-2">Ticket</th>
                        <th class="border p-2">Patient</th>
                        <th class="border p-2">Service</th>
                        <th class="border p-2">Priorité</th>
                        <th class="border p-2">Statut</th>
                        <th class="border p-2">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-if="tickets.length === 0">
                        <td colspan="6" class="border p-6 text-center text-gray-500">
                            Aucun patient en attente pour ce service.
                        </td>
                    </tr>

                    <tr v-for="t in tickets" :key="t.id">
                        <td class="border p-2 font-bold">{{ t.numero_ticket }}</td>

                        <td class="border p-2">
                            {{ t.patient?.nom ?? 'Non identifié' }}
                        </td>

                        <td class="border p-2">
                            {{ t.service?.nom_service }}
                        </td>

                        <td class="border p-2">
                            <span v-if="t.priorite == 1" class="text-red-600 font-bold">
                                Urgent
                            </span>
                            <span v-else>Normal</span>
                        </td>

                        <td class="border p-2">
                            {{ t.statut }}
                        </td>

                        <td class="border p-2">
                            <div class="flex gap-2 flex-wrap">
                                <button
                                    v-if="t.statut === 'EN_ATTENTE'"
                                    @click="appelerTicket(t)"
                                    class="bg-green-600 text-white px-3 py-2 rounded"
                                >
                                    Appeler
                                </button>

                                <button
                                    v-if="t.statut === 'APPELE'"
                                    @click="rappelerTicket(t)"
                                    class="bg-yellow-500 text-white px-3 py-2 rounded"
                                >
                                    Rappeler
                                </button>

                                <button
                                    v-if="t.statut === 'APPELE'"
                                    @click="changerStatut(t, 'EN_CONSULTATION')"
                                    class="bg-blue-600 text-white px-3 py-2 rounded"
                                >
                                    Consultation
                                </button>

                                <button
                                    v-if="t.statut === 'EN_CONSULTATION'"
                                    @click="changerStatut(t, 'TERMINE')"
                                    class="bg-gray-700 text-white px-3 py-2 rounded"
                                >
                                    Terminer
                                </button>

                                <button
                                    v-if="t.statut !== 'TERMINE'"
                                    @click="changerStatut(t, 'ABSENT')"
                                    class="bg-red-600 text-white px-3 py-2 rounded"
                                >
                                    Absent
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>