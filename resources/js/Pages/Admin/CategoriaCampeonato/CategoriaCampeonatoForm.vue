<template>
    <div class="m-2" v-if="ready">
        <form @submit.prevent="submit">
            <div class="mb-4">
                <InputLabel for="campeonato_id" value="Campeonato Id" class="required"/>
                <TextInput
                    id="campeonato_id"
                    class="w-full"
                    v-model="form.campeonato_id"
                    :disabled="readOnly"
                />
                <InputError :message="errors.campeonato_id"/>
            </div>

            <div class="w-full pt-4 mt-4 border-t border-gray-200">
                <div class="flex justify-center" v-if="readOnly">
                    <button
                        type="button"
                        class="px-4 py-2 text-white bg-red-600 rounded-md hover:bg-red-700"
                        @click="close"
                    >
                        <i class="mr-1 fa fa-close"></i> Sair
                    </button>
                </div>
                <div class="flex justify-center space-x-2" v-if="!readOnly">
                    <button
                        type="submit"
                        class="px-4 py-2 text-white rounded-md bg-blue-500 hover:bg-blue-500-hover"
                        :disabled="processing"
                    >
                        <i v-if="!processing" class="mr-1 fa fa-check"></i>
                        <i v-else class="mr-1 fa fa-spinner fa-spin"></i>
                        {{ processing ? 'Salvando...' : 'Salvar' }}
                    </button>
                    <button
                        type="button"
                        class="px-4 py-2 text-white bg-red-600 rounded-md hover:bg-red-700"
                        @click="close"
                    >
                        <i class="mr-1 fa fa-close"></i> Cancelar
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import { inject, onMounted, ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import { useToast } from 'vue-toastification';

const props = defineProps({
    data: {
        type: Object,
        default: null,
        required: false
    }
});

const emit = defineEmits(['close']);
const acao = ref('/admin/categorias-campeonato/');
const events = inject('events');
const toast = useToast();
const errors = ref({});
const processing = ref(false);
const ready = ref(false);
const readOnly = ref(false);

const form = ref({
    campeonato_id: '',
});

function submit() {
    processing.value = true;
    axios.post(acao.value, form.value)
        .then(response => {
            events.emit('table-reload');
            handleSuccess();
            processing.value = false;
        })
        .catch(error => {
            processing.value = false;

            if (error.response) {
                const data = error.response.data;

                if (data.errors) {
                    errors.value = data.errors;
                }
                const message = data.message || "Ocorreu um erro ao salvar  Categoria Campeonato.";
                handleError(message);
            } else {
                handleError("Erro de conexão com o servidor.");
            }
        })
        .finally(() => {
            processing.value = false;
        });
}

function handleSuccess() {
    if(props.data?.id) {
        toast.success(" Categoria Campeonato editado com sucesso!");
    } else {
        toast.success(" Categoria Campeonato criado com sucesso!");
    }
    close();
}

function handleError(msg) {
    toast.error(msg);
}

const loadData = async () => {
    try {
        const response = await axios.get(`/admin/categorias-campeonato/${props.data.id}`);
        Object.keys(form.value).forEach(key => {
            if (response.data[key] !== undefined) {
                form.value[key] = response.data[key];
            }
        });
        readOnly.value = Boolean(props.data.readOnly);
    } catch (err) {
        console.error('Error loading data:', err);
        toast.error('Não foi possível recuperar os dados do  Categoria Campeonato.');
    } finally {
        ready.value = true;
    }
}

const close = () => {
    events.emit('popup-close', true);
}

onMounted(async () => {
    if (props.data?.id) {
        acao.value = `/admin/categorias-campeonato/${props.data.id}`;
        await loadData();
    } else {
        ready.value = true;
    }
});
</script>
