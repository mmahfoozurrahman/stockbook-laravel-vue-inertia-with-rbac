<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppModal from '@/Components/AppModal.vue';
import { usePermission } from '@/composables/usePermission';
import { useSweetAlert } from '@/composables/useSweetAlert';

const props = defineProps({
    users: Object,
    roles: Array,
});
const { can } = usePermission();
const { confirmDelete } = useSweetAlert();

const modal = ref(false);
const editing = ref(null);

const blank = () => ({ name: '', email: '', password: '', roles: [] });
const form = useForm(blank());

const openCreate = () => {
    editing.value = null;
    form.defaults(blank());
    form.reset();
    form.clearErrors();
    modal.value = true;
};

const openEdit = (user) => {
    editing.value = user;
    form.defaults({
        name: user.name,
        email: user.email,
        password: '',
        roles: user.roles.map((r) => r.id),
    });
    form.reset();
    modal.value = true;
};

const submit = () => {
    const options = { onSuccess: () => { modal.value = false; } };
    if (editing.value) {
        form.put(route('admin.users.update', editing.value.id), options);
    } else {
        form.post(route('admin.users.store'), options);
    }
};

const destroy = async (user) => {
    if ((await confirmDelete(`Remove ${user.name}?`)).isConfirmed) {
        router.delete(route('admin.users.destroy', user.id));
    }
};
</script>

<template>
    <Head title="Users" />
    <AdminLayout>
        <header class="page-head">
            <div>
                <span class="eyebrow">Administration</span>
                <h1>People</h1>
                <p>Give the right people access to the right shelves.</p>
            </div>
            <button
                v-if="can('users.create')"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-person-plus"></i> Add member
            </button>
        </header>

        <section class="panel">
            <div class="panel-head">
                <div>
                    <span class="eyebrow">{{ users.total }} members</span>
                    <h2>Your library team</h2>
                </div>
                <span class="round-icon"><i class="bi bi-people"></i></span>
            </div>

            <div class="table-responsive">
                <table class="table app-table">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Roles</th>
                            <th>Joined</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users.data" :key="user.id">
                            <td>
                                <div class="member-cell">
                                    <span class="avatar">{{ user.name.charAt(0) }}</span>
                                    <div>
                                        <strong>{{ user.name }}</strong>
                                        <small>{{ user.email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span v-for="role in user.roles" :key="role.id" class="soft-chip">
                                    {{ role.name }}
                                </span>
                                <span v-if="!user.roles.length" class="muted">No role</span>
                            </td>
                            <td>{{ new Date(user.created_at).toLocaleDateString() }}</td>
                            <td>
                                <div class="table-actions">
                                    <button
                                        v-if="can('users.update')"
                                        class="icon-btn"
                                        @click="openEdit(user)"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button
                                        v-if="can('users.delete')"
                                        class="icon-btn danger-btn"
                                        @click="destroy(user)"
                                    >
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <AppModal
            :open="modal"
            :title="editing ? 'Edit team member' : 'Invite team member'"
            kicker="People & access"
            @close="modal = false"
        >
            <form @submit.prevent="submit">
                <div class="form-grid">
                    <label>
                        Full name
                        <input v-model="form.name">
                        <small class="error">{{ form.errors.name }}</small>
                    </label>
                    <label>
                        Email address
                        <input v-model="form.email" type="email">
                        <small class="error">{{ form.errors.email }}</small>
                    </label>
                </div>

                <label>
                    {{ editing ? 'New password (optional)' : 'Temporary password' }}
                    <input
                        v-model="form.password"
                        type="password"
                        autocomplete="new-password"
                    >
                    <small class="error">{{ form.errors.password }}</small>
                </label>

                <fieldset>
                    <legend>Assigned roles</legend>
                    <div class="choice-grid">
                        <label v-for="role in roles" :key="role.id" class="choice-card">
                            <input v-model="form.roles" type="checkbox" :value="role.id">
                            <span>
                                <strong>{{ role.name }}</strong>
                                <small>{{ role.description }}</small>
                            </span>
                        </label>
                    </div>
                </fieldset>

                <div class="modal-actions">
                    <button type="button" class="btn btn-light" @click="modal = false">Cancel</button>
                    <button class="btn btn-primary" :disabled="form.processing">Save member</button>
                </div>
            </form>
        </AppModal>
    </AdminLayout>
</template>
