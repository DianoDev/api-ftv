<template>
    <LayoutPrincipal>
        <div class="page-content">
            <div class="flex items-center justify-between mb-4 w-100">
                <h2 class="text-2xl font-semibold text-primary"></h2>
                <div class="flex">
                    <popup-button
                        id="novo-categorias-campeonato"
                        title="Novo  Categoria Campeonato"
                        size="xl"
                        component="CategoriaCampeonatoForm"
                        variant="secondary"
                    >
                        <i class="mr-2 fa fa-plus"></i>
                        Novo  Categoria Campeonato
                    </popup-button>
                </div>
            </div>
            <div>
                <datatable
                    id="categorias-campeonato"
                    :columns="columns"
                    :source="source"
                ></datatable>
            </div>
        </div>
    </LayoutPrincipal>
</template>

<script setup>
import { ref, inject } from 'vue';
import Datatable from '@/Components/datatable/Datatable.vue';
import LayoutPrincipal from '@/Layouts/LayoutPrincipal.vue';
import PopupButton from '@/Components/PopupButton.vue';
import { useToast } from 'vue-toastification';

const toast = useToast();
const events = inject('events');
const source = ref('/admin/categorias-campeonato/list');

const columns = ref([
    {name: 'campeonato_id', title: 'Campeonato Id', width: '20%', sort: 'campeonato_id', nowrap: true},
    {
        name: 'id',
        title: 'Ações',
        width: '10%',
        nowrap: true,
        contentClass: 'text-center',
        headerClass: 'text-center',
        template: 'dropdown',
        formatter: (val, row) => [
            {
                type: 'modal',
                icon: 'fa-edit',
                dataSize: 'xl',
                dataComponent: 'CategoriaCampeonatoForm',
                dataTitle: 'Editar  Categoria Campeonato',
                dataJson: { id: row.id },
                text: 'Editar'
            },
            {
                type: 'delete',
                icon: 'fa-trash',
                text: 'Remover',
                deleteUrl: `/admin/categorias-campeonato/${row.id}`,
                dataTitle: 'Confirmação de Remoção',
                dataMessage: 'Você deseja realmente excluir este registro?'
            }
        ]
    }
]);
</script>
