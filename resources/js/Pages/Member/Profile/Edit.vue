<script setup>
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import MemberLayout from '@/Layouts/MemberLayout.vue';

const loading = ref(false);

const startNavigation = () => { loading.value = true; };
const stopNavigation = () => { loading.value = false; };

onMounted(() => {
    router.on('start', startNavigation);
    router.on('finish', stopNavigation);
});

onUnmounted(() => {
    router.off('start', startNavigation);
    router.off('finish', stopNavigation);
});

const profile = useForm({
    name: usePage().props.auth.user.name,
    email: usePage().props.auth.user.email,
    profile_photo: null,
});
const password = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const saveProfile = () => profile.post(route('profile.update'), {
    forceFormData: true,
    preserveScroll: true,
});

const savePassword = () => password.put(route('profile.password'), {
    preserveScroll: true,
    onSuccess: () => password.reset(),
});
</script>

<template>
    <Head title="Profile" />
    <MemberLayout>
        <header class="page-head">
            <div>
                <span class="eyebrow">Personal settings</span>
                <h1>Your profile</h1>
                <p>Keep your identity and sign-in details current.</p>
            </div>
            <span class="profile-status">
                <i class="bi bi-shield-check"></i> Account protected
            </span>
        </header>

        <section v-if="loading" class="profile-grid">
            <aside class="panel profile-card">
                <div class="skeleton-avatar" style="width: 80px; height: 80px; border-radius: 12px; margin: 0 auto 12px;"></div>
                <div class="skeleton-line" style="width: 60%; margin: 0 auto 6px;"></div>
                <div class="skeleton-line short" style="width: 50%; margin: 0 auto 16px;"></div>
                <div class="chip-row justify-content-center">
                    <div v-for="n in 2" :key="n" class="skeleton-pill" style="width: 70px;"></div>
                </div>
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(103, 80, 54, 0.08);">
                    <div class="skeleton-line" style="width: 80%; margin: 0 auto;"></div>
                </div>
            </aside>

            <div class="settings-stack">
                <div class="panel">
                    <div class="panel-head">
                        <div>
                            <span class="eyebrow">Identity</span>
                            <h2>Profile details</h2>
                        </div>
                        <span class="round-icon skeleton-icon"></span>
                    </div>
                    <div class="form-grid">
                        <label>
                            <div class="skeleton-line short" style="width: 40%; margin-bottom: 8px;"></div>
                            <div class="skeleton-line" style="height: 46px; border-radius: 12px;"></div>
                        </label>
                        <label>
                            <div class="skeleton-line short" style="width: 40%; margin-bottom: 8px;"></div>
                            <div class="skeleton-line" style="height: 46px; border-radius: 12px;"></div>
                        </label>
                    </div>
                    <div style="margin-top: 16px;">
                        <div class="skeleton-line" style="height: 80px; border-radius: 12px;"></div>
                    </div>
                    <div class="modal-actions" style="margin-top: 20px;">
                        <div class="skeleton-line" style="width: 150px; height: 40px; border-radius: 8px;"></div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-head">
                        <div>
                            <span class="eyebrow">Security</span>
                            <h2>Change password</h2>
                        </div>
                        <span class="round-icon skeleton-icon"></span>
                    </div>
                    <label>
                        <div class="skeleton-line short" style="width: 40%; margin-bottom: 8px;"></div>
                        <div class="skeleton-line" style="height: 46px; border-radius: 12px;"></div>
                    </label>
                    <div class="form-grid" style="margin-top: 16px;">
                        <label>
                            <div class="skeleton-line short" style="width: 40%; margin-bottom: 8px;"></div>
                            <div class="skeleton-line" style="height: 46px; border-radius: 12px;"></div>
                        </label>
                        <label>
                            <div class="skeleton-line short" style="width: 40%; margin-bottom: 8px;"></div>
                            <div class="skeleton-line" style="height: 46px; border-radius: 12px;"></div>
                        </label>
                    </div>
                    <div class="modal-actions" style="margin-top: 20px;">
                        <div class="skeleton-line" style="width: 150px; height: 40px; border-radius: 8px;"></div>
                    </div>
                </div>
            </div>
        </section>

        <section v-else class="profile-grid">
            <aside class="panel profile-card">
                <span class="profile-avatar">{{ $page.props.auth.user.name.charAt(0) }}</span>
                <h2>{{ $page.props.auth.user.name }}</h2>
                <p>{{ $page.props.auth.user.email }}</p>
                <div class="chip-row justify-content-center">
                    <span v-for="role in $page.props.auth.roles" :key="role" class="soft-chip">
                        {{ role }}
                    </span>
                </div>
                <div class="profile-note">
                    <i class="bi bi-stars"></i>
                    <span>Your profile appears beside activity and team records.</span>
                </div>
            </aside>

            <div class="settings-stack">
                <form class="panel" @submit.prevent="saveProfile">
                    <div class="panel-head">
                        <div>
                            <span class="eyebrow">Identity</span>
                            <h2>Profile details</h2>
                        </div>
                        <span class="round-icon"><i class="bi bi-person"></i></span>
                    </div>
                    <div class="form-grid">
                        <label>
                            Full name
                            <input v-model="profile.name">
                            <small class="error">{{ profile.errors.name }}</small>
                        </label>
                        <label>
                            Email address
                            <input v-model="profile.email" type="email">
                            <small class="error">{{ profile.errors.email }}</small>
                        </label>
                    </div>
                    <label class="upload-box">
                        <input
                            type="file"
                            accept="image/*"
                            @change="profile.profile_photo = $event.target.files[0]"
                        >
                        <i class="bi bi-person-bounding-box"></i>
                        <span>
                            <strong>Update profile photo</strong>
                            <small>Square JPG or PNG, up to 2 MB</small>
                        </span>
                    </label>
                    <div class="modal-actions">
                        <button class="btn btn-primary" :disabled="profile.processing">
                            Save profile
                        </button>
                    </div>
                </form>

                <form class="panel" @submit.prevent="savePassword">
                    <div class="panel-head">
                        <div>
                            <span class="eyebrow">Security</span>
                            <h2>Change password</h2>
                        </div>
                        <span class="round-icon"><i class="bi bi-key"></i></span>
                    </div>
                    <label>
                        Current password
                        <input
                            v-model="password.current_password"
                            type="password"
                            autocomplete="current-password"
                        >
                        <small class="error">{{ password.errors.current_password }}</small>
                    </label>
                    <div class="form-grid">
                        <label>
                            New password
                            <input
                                v-model="password.password"
                                type="password"
                                autocomplete="new-password"
                            >
                        </label>
                        <label>
                            Confirm password
                            <input
                                v-model="password.password_confirmation"
                                type="password"
                                autocomplete="new-password"
                            >
                        </label>
                    </div>
                    <small class="error">{{ password.errors.password }}</small>
                    <div class="modal-actions">
                        <button class="btn btn-primary" :disabled="password.processing">
                            Update password
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </MemberLayout>
</template>
