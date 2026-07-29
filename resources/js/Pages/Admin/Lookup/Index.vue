<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppModal from '@/Components/AppModal.vue';
import RichEditor from '@/Components/RichEditor.vue';
import { usePermission } from '@/composables/usePermission';
import { useSweetAlert } from '@/composables/useSweetAlert';

const props = defineProps({
    type: String,
    fields: Array,
    items: Object,
});
const { can, hasRole } = usePermission();
const { confirmDelete } = useSweetAlert();

const modal = ref(false);
const editing = ref(null);

const singular = computed(() => props.type === 'categories'
    ? 'category'
    : props.type.slice(0, -1));
const title = computed(() => props.type.charAt(0).toUpperCase() + props.type.slice(1));

const blank = () => Object.fromEntries(props.fields.map((f) => [f.name, '']));
const form = useForm(blank());

const allowed = (action) => props.type === 'permissions'
    ? hasRole('super-admin')
    : can(`${props.type}.${action}`);

const openCreate = () => {
    editing.value = null;
    form.defaults(blank());
    form.reset();
    form.clearErrors();
    modal.value = true;
};

const openEdit = (item) => {
    editing.value = item;
    form.defaults(Object.fromEntries(
        props.fields.map((f) => [f.name, item[f.name] || '']),
    ));
    form.reset();
    modal.value = true;
};

const submit = () => {
    const options = {
        preserveScroll: true,
        onSuccess: () => { modal.value = false; },
    };
    if (editing.value) {
        form.put(route(`${props.type}.update`, editing.value.id), options);
    } else {
        form.post(route(`${props.type}.store`), options);
    }
};

const destroy = async (item) => {
    if ((await confirmDelete(`Remove “${item.name}”?`)).isConfirmed) {
        router.delete(route(`${props.type}.destroy`, item.id), { preserveScroll: true });
    }
};
</script>

<template>
    <Head :title="title" />
    <AdminLayout>
        <header class="page-head">
            <div>
                <span class="eyebrow">Catalog structure</span>
                <h1>{{ title }}</h1>
                <p>Shape a consistent, discoverable library vocabulary.</p>
            </div>
            <button v-if="allowed('create')" class="btn btn-primary" @click="openCreate">
                <i class="bi bi-plus-lg"></i> Add {{ singular }}
            </button>
        </header>

        <section class="panel">
            <div class="panel-head">
                <div>
                    <span class="eyebrow">{{ items.total }} records</span>
                    <h2>All {{ type }}</h2>
                </div>
                <span class="round-icon">
                    <i class="bi" :class="type === 'authors'
                        ? 'bi-feather'
                        : type === 'tags'
                            ? 'bi-tags'
                            : type === 'permissions'
                                ? 'bi-shield-check'
                                : 'bi-collection'"></i>
                </span>
            </div>

            <div v-if="items.data.length" class="lookup-grid">
                <article v-for="item in items.data" :key="item.id" class="lookup-card">
                    <span class="lookup-monogram">{{ item.name.charAt(0) }}</span>
                    <div class="lookup-copy">
                        <h3>{{ item.name }}</h3>
                        <div
                            v-if="item.bio || item.description"
                            class="clamp-copy"
                            v-html="item.bio || item.description"
                        ></div>
                        <small>{{ item.books_count ?? item.roles_count ?? 0 }} linked records</small>
                    </div>
                    <div class="lookup-actions">
                        <button v-if="allowed('update')" class="icon-btn" @click="openEdit(item)">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button v-if="allowed('delete')" class="icon-btn danger-btn" @click="destroy(item)">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </article>
            </div>

            <div v-else class="empty-state">
                <i class="bi bi-inboxes"></i>
                <h3>No {{ type }} yet</h3>
                <p>Create the first record to organize your collection.</p>
            </div>
        </section>

        <AppModal
            :open="modal"
            :title="`${editing ? 'Edit' : 'Add'} ${singular}`"
            :kicker="title"
            @close="modal = false"
        >
            <form @submit.prevent="submit">
                <label v-for="field in fields" :key="field.name">
                    {{ field.label }}
                    <RichEditor
                        v-if="field.type === 'editor'"
                        v-model="form[field.name]"
                    />
                    <input
                        v-else
                        v-model="form[field.name]"
                        :class="{ invalid: form.errors[field.name] }"
                    >
                    <small v-if="form.errors[field.name]" class="error">
                        {{ form.errors[field.name] }}
                    </small>
                </label>
                <div class="modal-actions">
                    <button type="button" class="btn btn-light" @click="modal = false">Cancel</button>
                    <button class="btn btn-primary" :disabled="form.processing">
                        Save {{ singular }}
                    </button>
                </div>
            </form>
        </AppModal>
    </AdminLayout>
</template>
