<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';

defineProps({
    flushFooter: {
        type: Boolean,
        default: false,
    },
});

const page = usePage();
const mobileOpen = ref(false);
const scrolled = ref(false);

const user = computed(() => page.props.auth?.user);
const isAuth = computed(() => !!user.value);

const updateScroll = () => {
    scrolled.value = scrollY > 24;
};

onMounted(() => {
    addEventListener('scroll', updateScroll, { passive: true });
    updateScroll();
});
onUnmounted(() => removeEventListener('scroll', updateScroll));

const closeMobile = () => { mobileOpen.value = false; };
</script>

<template>
    <div class="public-shell">
        <header class="public-nav" :class="{ scrolled }">
            <div class="public-nav-inner">
                <Link :href="route('home')" class="brand">
                    <span class="brand-mark"><i class="bi bi-book-half"></i></span>
                    <strong>Folio</strong>
                </Link>
                <nav class="public-links">
                    <Link :href="route('home')" class="nav-link">Home</Link>
                    <Link :href="route('public.books.index')" class="nav-link">Books</Link>
                </nav>
                <div class="public-cta">
                    <template v-if="isAuth">
                        <Link :href="route('member.dashboard')" class="btn btn-ghost">
                            <i class="bi bi-grid-1x2"></i> Dashboard
                        </Link>
                        <Link :href="route('logout')" method="post" as="button" class="btn btn-light">
                            Sign out
                        </Link>
                    </template>
                    <template v-else>
                        <Link :href="route('login')" class="btn btn-ghost">Sign in</Link>
                        <Link :href="route('register')" class="btn btn-primary">
                            Get started <i class="bi bi-arrow-right"></i>
                        </Link>
                    </template>
                </div>
                <button class="mobile-toggle" type="button" @click="mobileOpen = true">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </header>

        <div class="page-loader">
            <span></span>
            <strong>Turning the page</strong>
        </div>

        <main class="public-main" :class="{ 'public-main--flush': flushFooter }">
            <slot />
        </main>

        <footer class="public-footer">
            <div class="footer-inner">
                <div>
                    <span class="brand">
                        <span class="brand-mark"><i class="bi bi-book-half"></i></span>
                        <strong>Folio</strong>
                    </span>
                    <p class="muted">A quiet, considered workspace for cataloging remarkable books.</p>
                </div>
                <div>
                    <h5>Explore</h5>
                    <ul>
                        <li><Link :href="route('public.books.index')">All books</Link></li>
                        <li><Link :href="route('home')">Home</Link></li>
                    </ul>
                </div>
                <div>
                    <h5>Account</h5>
                    <ul>
                        <li v-if="!isAuth"><Link :href="route('login')">Sign in</Link></li>
                        <li v-if="!isAuth"><Link :href="route('register')">Create account</Link></li>
                        <li v-if="isAuth"><Link :href="route('member.dashboard')">Dashboard</Link></li>
                        <li v-if="isAuth"><Link :href="route('member.bookmarks.index')">My bookmarks</Link></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <small>© {{ new Date().getFullYear() }} Folio · A library for curious readers.</small>
            </div>
        </footer>

        <div v-if="mobileOpen" class="mobile-backdrop" @click="closeMobile"></div>
        <aside v-if="mobileOpen" class="mobile-drawer">
            <button class="mobile-close" @click="closeMobile"><i class="bi bi-x-lg"></i></button>
            <Link :href="route('home')" class="mobile-link" @click="closeMobile">Home</Link>
            <Link :href="route('public.books.index')" class="mobile-link" @click="closeMobile">Books</Link>
            <hr>
            <template v-if="isAuth">
                <Link :href="route('member.dashboard')" class="mobile-link" @click="closeMobile">Dashboard</Link>
                <Link :href="route('member.bookmarks.index')" class="mobile-link" @click="closeMobile">My bookmarks</Link>
                <Link :href="route('logout')" method="post" as="button" class="btn btn-light w-100" @click="closeMobile">Sign out</Link>
            </template>
            <template v-else>
                <Link :href="route('login')" class="btn btn-light w-100" @click="closeMobile">Sign in</Link>
                <Link :href="route('register')" class="btn btn-primary w-100 mt-2" @click="closeMobile">Get started</Link>
            </template>
        </aside>
    </div>
</template>
