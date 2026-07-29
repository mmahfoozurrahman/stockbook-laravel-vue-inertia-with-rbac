<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppModal from '@/Components/AppModal.vue';
import { usePermission } from '@/composables/usePermission';
import { useSweetAlert } from '@/composables/useSweetAlert';

const props = defineProps({
    roles: Array,
    permissionGroups: Object,
});
const { can } = usePermission();
const { confirmDelete } = useSweetAlert();

const modal = ref(false);
const editing = ref(null);
const form = useForm({ name: '', description: '', permissions: [] });

const openCreate = () => {
    editing.value = null;
    form.defaults({ name: '', description: '', permissions: [] });
    form.reset();
    modal.value = true;
};

const openEdit = (role) => {
    editing.value = role;
    form.defaults({
        name: role.name,
        description: role.description || '',
        permissions: role.permissions.map((p) => p.id),
    });
    form.reset();
    modal.value = true;
};

const submit = () => {
    const options = { onSuccess: () => { modal.value = false; } };
    if (editing.value) {
        form.put(route('admin.roles.update', editing.value.id), options);
    } else {
        form.post(route('admin.roles.store'), options);
    }
};

const destroy = async (role) => {
    if ((await confirmDelete(`Delete ${role.name}?`)).isConfirmed) {
        router.delete(route('admin.roles.destroy', role.id));
    }
};

const toggleGroup = (list) => {
    const ids = list.map((p) => p.id);
    const all = ids.every((id) => form.permissions.includes(id));
    form.permissions = all
        ? form.permissions.filter((id) => !ids.includes(id))
        : [...new Set([...form.permissions, ...ids])];
};
</script>

<template>
    <Head title="Roles" />
    <AdminLayout>
        <header class="page-head">
            <div>
                <span class="eyebrow">Access control</span>
                <h1>Roles</h1>
                <p>Compose clear responsibilities from precise permissions.</p>
            </div>
            <button
                v-if="can('roles.create')"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg"></i> Create role
            </button>
        </header>

        <section class="role-grid">
            <article v-for="role in roles" :key="role.id" class="panel role-card">
                <div class="role-icon">
                    <i class="bi" :class="role.slug === 'super-admin'
                        ? 'bi-shield-lock'
                        : role.slug === 'editor'
                            ? 'bi-pencil-square'
                            : 'bi-person'"></i>
                </div>
                <div>
                    <span class="eyebrow">{{ role.users_count }} members</span>
                    <h2>{{ role.name }}</h2>
                    <p>{{ role.description || 'A custom library role.' }}</p>
                </div>
                <div class="role-summary">
                    <span><strong>{{ role.permissions.length }}</strong> permissions</span>
                    <span class="status-pill published">
                        {{ role.slug === 'super-admin' ? 'System' : 'Custom' }}
                    </span>
                </div>
                <div class="chip-row">
                    <span
                        v-for="permission in role.permissions.slice(0, 4)"
                        :key="permission.id"
                        class="soft-chip"
                    >
                        {{ permission.slug }}
                    </span>
                    <span v-if="role.permissions.length > 4" class="soft-chip">
                        +{{ role.permissions.length - 4 }}
                    </span>
                </div>
                <div class="card-actions">
                    <button
                        v-if="can('roles.update')"
                        class="btn btn-soft"
                        @click="openEdit(role)"
                    >
                        <i class="bi bi-sliders"></i> Configure
                    </button>
                    <button
                        v-if="can('roles.delete') && role.slug !== 'super-admin'"
                        class="icon-btn danger-btn"
                        @click="destroy(role)"
                    >
                        <i class="bi bi-trash3"></i>
                    </button>
                </div>
            </article>
        </section>

        <AppModal
            :open="modal"
            :title="editing ? 'Configure role' : 'Create role'"
            kicker="Access composition"
            size="wide"
            @close="modal = false"
        >
            <form @submit.prevent="submit">
                <div class="form-grid">
                    <label>
                        Role name
                        <input v-model="form.name">
                    </label>
                    <label>
                        Description
                        <input v-model="form.description">
                    </label>
                </div>

                <div class="permission-groups">
                    <section
                        v-for="(permissions, group) in permissionGroups"
                        :key="group"
                        class="permission-group"
                    >
                        <header>
                            <div>
                                <strong>{{ group }}</strong>
                                <small>{{ permissions.length }} permissions</small>
                            </div>
                            <button type="button" class="text-link" @click="toggleGroup(permissions)">
                                Toggle all
                            </button>
                        </header>
                        <div class="choice-grid">
                            <label
                                v-for="permission in permissions"
                                :key="permission.id"
                                class="choice-card compact"
                            >
                                <input
                                    v-model="form.permissions"
                                    type="checkbox"
                                    :value="permission.id"
                                    :disabled="editing?.slug === 'super-admin'"
                                >
                                <span>
                                    <strong>{{ permission.name }}</strong>
                                    <small>{{ permission.slug }}</small>
                                </span>
                            </label>
                        </div>
                    </section>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-light" @click="modal = false">Cancel</button>
                    <button class="btn btn-primary" :disabled="form.processing">Save role</button>
                </div>
            </form>
        </AppModal>
    </AdminLayout>
</template>
