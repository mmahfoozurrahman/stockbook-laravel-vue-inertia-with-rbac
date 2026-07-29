<script setup>
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { computed, ref, onMounted, onUnmounted } from 'vue';
import MemberLayout from '@/Layouts/MemberLayout.vue';

const props = defineProps({
    stats: Object,
    bookmarks: Array,
    recentPublished: Array,
});

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

const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Good morning';
    if (hour < 18) return 'Good afternoon';
    return 'Good evening';
});

const firstName = computed(() => {
    const name = usePage().props.auth?.user?.name ?? '';
    return name.split(' ')[0];
});
</script>

<template>
    <Head title="Your shelf · Folio" />
    <MemberLayout>
        <header class="member-head">
            <div>
                <span class="eyebrow">Your shelf</span>
                <h1>{{ greeting }}, {{ firstName }}.</h1>
                <p>Here's a quiet look at your reading life today.</p>
            </div>
            <div class="head-actions">
                <Link :href="route('public.books.index')" class="btn btn-primary">
                    <i class="bi bi-search"></i> Browse catalog
                </Link>
            </div>
        </header>

        <section v-if="loading" class="member-stats">
            <article v-for="n in 3" :key="n" class="stat-card skeleton-stat-card">
                <span class="stat-icon skeleton-icon"></span>
                <div>
                    <div class="skeleton-line short" style="width: 40%; margin-bottom: 6px;"></div>
                    <div class="skeleton-line short" style="width: 60%;"></div>
                </div>
            </article>
        </section>

        <section v-else class="member-stats">
            <article class="stat-card">
                <span class="stat-icon"><i class="bi bi-bookmark-heart"></i></span>
                <div>
                    <small>Total bookmarks</small>
                    <strong>{{ stats.bookmarks }}</strong>
                </div>
            </article>
            <article class="stat-card">
                <span class="stat-icon"><i class="bi bi-calendar-week"></i></span>
                <div>
                    <small>Added this month</small>
                    <strong>{{ stats.thisMonth }}</strong>
                </div>
            </article>
            <article class="stat-card">
                <span class="stat-icon"><i class="bi bi-stars"></i></span>
                <div>
                    <small>Featured in catalog</small>
                    <strong>{{ recentPublished.length }}</strong>
                </div>
            </article>
        </section>

        <section v-if="loading" class="panel">
            <div class="panel-head">
                <div>
                    <span class="eyebrow">Your shelf</span>
                    <h2>Recent bookmarks</h2>
                </div>
            </div>
            <ul class="bookmark-list">
                <li v-for="n in 3" :key="n" class="skeleton-bookmark-item">
                    <div class="bm-thumb skeleton-thumb"></div>
                    <div class="bm-info">
                        <div class="skeleton-line" style="margin-bottom: 6px;"></div>
                        <div class="skeleton-line short"></div>
                    </div>
                    <div class="skeleton-button"></div>
                </li>
            </ul>
        </section>

        <section v-else-if="bookmarks?.length" class="panel">
            <div class="panel-head">
                <div>
                    <span class="eyebrow">Your shelf</span>
                    <h2>Recent bookmarks</h2>
                </div>
                <Link :href="route('member.bookmarks.index')" class="text-link">
                    See all <i class="bi bi-arrow-up-right"></i>
                </Link>
            </div>
            <ul class="bookmark-list">
                <li v-for="book in bookmarks" :key="book.id">
                    <Link :href="route('public.books.show', book.id)" class="bm-thumb">
                        <img v-if="book.cover_image" :src="`/storage/${book.cover_image}`" :alt="book.title">
                        <span v-else class="book-fallback small">
                            <strong>{{ book.title.charAt(0) }}</strong>
                        </span>
                    </Link>
                    <div class="bm-info">
                        <strong>{{ book.title }}</strong>
                        <small v-if="book.author">{{ book.author.name }}</small>
                    </div>
                    <Link :href="route('public.books.show', book.id)" class="btn btn-soft btn-sm">
                        Open <i class="bi bi-arrow-right"></i>
                    </Link>
                </li>
            </ul>
        </section>

        <section v-else-if="!loading" class="panel empty-state">
            <i class="bi bi-bookmark-plus"></i>
            <h3>Your shelf is empty</h3>
            <p>Browse the catalog and tap the bookmark on any book to save it for later.</p>
            <Link :href="route('public.books.index')" class="btn btn-primary">
                Find your first bookmark
            </Link>
        </section>

        <section v-if="loading" class="panel">
            <div class="panel-head">
                <div>
                    <span class="eyebrow">Fresh from the catalog</span>
                    <h2>Recently published</h2>
                </div>
            </div>
            <div class="recent-row">
                <div v-for="n in 3" :key="n" class="recent-card skeleton-recent-card">
                    <div class="skeleton-cover" style="height: 200px;"></div>
                    <div>
                        <div class="skeleton-line" style="margin-bottom: 6px;"></div>
                        <div class="skeleton-line short"></div>
                    </div>
                </div>
            </div>
        </section>

        <section v-else-if="recentPublished?.length" class="panel">
            <div class="panel-head">
                <div>
                    <span class="eyebrow">Fresh from the catalog</span>
                    <h2>Recently published</h2>
                </div>
            </div>
            <div class="recent-row">
                <Link
                    v-for="book in recentPublished"
                    :key="book.id"
                    :href="route('public.books.show', book.id)"
                    class="recent-card"
                >
                    <img v-if="book.cover_image" :src="`/storage/${book.cover_image}`" :alt="book.title">
                    <span v-else class="book-fallback small">
                        <strong>{{ book.title.charAt(0) }}</strong>
                    </span>
                    <div>
                        <strong>{{ book.title }}</strong>
                        <small v-if="book.author">{{ book.author.name }}</small>
                    </div>
                </Link>
            </div>
        </section>
    </MemberLayout>
</template>
