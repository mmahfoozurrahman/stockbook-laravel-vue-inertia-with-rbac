<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { usePermission } from '@/composables/usePermission';
import { useSweetAlert } from '@/composables/useSweetAlert';

const page = usePage();
const { can, hasRole } = usePermission();
const { toast } = useSweetAlert();

const mobileOpen = ref(false);
const scroll = ref(0);
const showTop = ref(false);

const nav = computed(() => [
    { label: 'Dashboard', icon: 'bi-grid-1x2', route: 'admin.dashboard', show: true },
    { label: 'Books', icon: 'bi-journals', route: 'admin.books.index', show: can('books.view') },
    { label: 'Authors', icon: 'bi-feather', route: 'admin.authors.index', show: can('authors.view') },
    { label: 'Categories', icon: 'bi-collection', route: 'admin.categories.index', show: can('categories.view') },
    { label: 'Tags', icon: 'bi-tags', route: 'admin.tags.index', show: can('tags.view') },
    { label: 'Profile', icon: 'bi-person-circle', route: 'member.profile.edit', show: true },
    { label: 'Users', icon: 'bi-people', route: 'admin.users.index', show: can('users.view') },
    { label: 'Roles', icon: 'bi-person-badge', route: 'admin.roles.index', show: can('roles.view') },
    { label: 'Permissions', icon: 'bi-gear', route: 'admin.permissions.index', show: hasRole('super-admin') },
].filter((item) => item.show));

const active = (name) => {
    if (name.endsWith('.index')) {
        return route().current(`${name.slice(0, -'.index'.length)}.*`);
    }

    return route().current(name);
};

const updateScroll = () => {
    const max = document.documentElement.scrollHeight - innerHeight;
    scroll.value = max > 0 ? (scrollY / max) * 100 : 0;
    showTop.value = scrollY > 480;
};

onMounted(() => {
    addEventListener('scroll', updateScroll, { passive: true });
    updateScroll();
});

onUnmounted(() => removeEventListener('scroll', updateScroll));

watch(() => page.props.flash, (value) => {
    if (value?.success) toast(value.success);
    if (value?.error) toast(value.error, 'error');
}, { deep: true, immediate: true });

router.on('start', () => { mobileOpen.value = false; });
</script>

<template>
    <div class="reading-progress" :style="{ width: `${scroll}%` }"></div>
    <div class="page-loader">
        <span></span>
        <strong>Turning the page</strong>
    </div>
    <div class="app-shell">
        <button
            class="mobile-menu"
            type="button"
            aria-label="Open navigation"
            @click="mobileOpen = true"
        >
            <i class="bi bi-list"></i>
        </button>
        <aside class="sidebar" :class="{ open: mobileOpen }">
            <div>
                <div class="brand-row">
                    <div class="sidebar-heading">
                        <small>Workspace</small>
                        <strong>Folio Library</strong>
                    </div>
                    <button
                        class="sidebar-close"
                        type="button"
                        @click="mobileOpen = false"
                    >
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <nav class="admin-nav">
                    <Link
                        v-for="item in nav"
                        :key="item.route"
                        :href="route(item.route)"
                        class="nav-row"
                        :class="{ active: active(item.route) }"
                    >
                        <i class="bi" :class="item.icon"></i>
                        <span>{{ item.label }}</span>
                    </Link>
                </nav>
            </div>
            <div class="sidebar-footer">
                <Link :href="route('member.profile.edit')" class="user-card">
                    <img
                        v-if="page.props.auth.user.profile_photo"
                        :src="`/storage/${page.props.auth.user.profile_photo}`"
                        alt=""
                    >
                    <span v-else class="avatar">{{ page.props.auth.user.name.charAt(0) }}</span>
                    <span>
                        <strong>{{ page.props.auth.user.name }}</strong>
                        <small>{{ page.props.auth.roles[0]?.replace('-', ' ') }}</small>
                    </span>
                    <i class="bi bi-gear"></i>
                </Link>
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="logout-btn"
                >
                    <i class="bi bi-box-arrow-right"></i> Logout
                </Link>
            </div>
        </aside>
        <div
            v-if="mobileOpen"
            class="sidebar-backdrop"
            @click="mobileOpen = false"
        ></div>
        <main class="main-panel">
            <slot />
        </main>
    </div>
    <Transition name="float">
        <button
            v-if="showTop"
            class="scroll-top"
            type="button"
            aria-label="Scroll to top"
            @click="scrollTo({ top: 0, behavior: 'smooth' })"
        >
            <i class="bi bi-arrow-up"></i>
        </button>
    </Transition>
</template>
