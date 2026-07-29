<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch, onMounted, onUnmounted } from 'vue';
import { useSweetAlert } from '@/composables/useSweetAlert';

const page = usePage();
const { toast } = useSweetAlert();

const user = computed(() => page.props.auth?.user);

const mobileOpen = ref(false);
const isNavigating = ref(false);

const startNavigation = () => { isNavigating.value = true; };
const stopNavigation = () => { isNavigating.value = false; };

onMounted(() => {
    router.on('start', startNavigation);
    router.on('finish', stopNavigation);
});

onUnmounted(() => {
    router.off('start', startNavigation);
    router.off('finish', stopNavigation);
});

watch(() => page.props.flash, (value) => {
    if (value?.success) toast(value.success);
    if (value?.error) toast(value.error, 'error');
}, { deep: true, immediate: true });

router.on('start', () => { mobileOpen.value = false; });

const nav = [
    { label: 'Dashboard', icon: 'bi-grid-1x2', route: 'member.dashboard' },
    { label: 'My bookmarks', icon: 'bi-bookmark-heart', route: 'member.bookmarks.index' },
    { label: 'Browse catalog', icon: 'bi-search', route: 'public.books.index' },
];

const active = (name) => route().current(name);
</script>

<template>
    <div class="member-shell" :class="{ 'is-navigating': isNavigating }">
        <div class="page-loader">
            <span></span>
            <strong>Turning the page</strong>
        </div>

        <header class="member-topbar">
            <div class="topbar-inner">
                <Link :href="route('home')" class="brand">
                    <span class="brand-mark"><i class="bi bi-book-half"></i></span>
                    <strong>Folio</strong>
                    <span class="brand-suffix">Reader</span>
                </Link>
                <div class="topbar-spacer"></div>
                <Link :href="route('member.profile.edit')" class="user-chip">
                    <span class="avatar">{{ user?.name?.charAt(0) ?? '?' }}</span>
                    <span>
                        <strong>{{ user?.name }}</strong>
                        <small>Manage profile</small>
                    </span>
                    <i class="bi bi-gear"></i>
                </Link>
                <Link :href="route('logout')" method="post" as="button" class="btn btn-light">
                    <i class="bi bi-box-arrow-right"></i> Sign out
                </Link>
                <button class="mobile-toggle" type="button" @click="mobileOpen = true">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </header>

        <div class="member-body">
            <aside class="member-side" :class="{ open: mobileOpen }">
                <button class="mobile-close" type="button" @click="mobileOpen = false">
                    <i class="bi bi-x-lg"></i>
                </button>
                <span class="side-label">Your shelf</span>
                <nav>
                    <Link
                        v-for="item in nav"
                        :key="item.route"
                        :href="route(item.route)"
                        class="side-row"
                        :class="{ active: active(item.route) }"
                    >
                        <i class="bi" :class="item.icon"></i>
                        <span>{{ item.label }}</span>
                    </Link>
                </nav>
                <div class="side-foot">
                    <small>Signed in as <strong>{{ user?.name }}</strong></small>
                </div>
            </aside>

            <main class="member-main">
                <slot />
            </main>
        </div>
    </div>
</template>
