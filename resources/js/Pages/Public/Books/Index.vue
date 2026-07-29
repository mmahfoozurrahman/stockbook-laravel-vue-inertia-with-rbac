<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, onMounted, onUnmounted } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import BookCard from '@/Components/Public/BookCard.vue';

const props = defineProps({
    books: Object,
    categories: Array,
    authors: Array,
    filters: Object,
    bookmarkedIds: Array,
});

const search = ref(props.filters?.search ?? '');
const category = ref(props.filters?.category ?? '');
const author = ref(props.filters?.author ?? '');

const bookmarkedSet = ref(new Set(props.bookmarkedIds ?? []));
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

let timer;
watch([search, category, author], () => {
    clearTimeout(timer);
    timer = setTimeout(() => {
        router.get(
            route('public.books.index'),
            {
                search: search.value || undefined,
                category: category.value || undefined,
                author: author.value || undefined,
            },
            { preserveState: true, replace: true },
        );
    }, 350);
});

const reset = () => {
    search.value = '';
    category.value = '';
    author.value = '';
};
</script>

<template>
    <Head title="Browse the catalog · Folio" />
    <PublicLayout>
        <header class="catalog-head">
            <span class="eyebrow">The catalog</span>
            <h1>Browse published books</h1>
            <p>Search by title, ISBN, or author name. Filter by category or pick a writer you love.</p>
        </header>

        <section class="catalog-filters panel">
            <div class="search-wrap">
                <i class="bi bi-search"></i>
                <input
                    v-model="search"
                    type="search"
                    placeholder="Search title, ISBN, or author…"
                    aria-label="Search books"
                >
            </div>
            <select v-model="category" class="form-select compact-select" aria-label="Filter by category">
                <option value="">All categories</option>
                <option v-for="c in categories" :key="c.id" :value="c.slug">{{ c.name }}</option>
            </select>
            <select v-model="author" class="form-select compact-select" aria-label="Filter by author">
                <option value="">All authors</option>
                <option v-for="a in authors" :key="a.id" :value="a.id">{{ a.name }}</option>
            </select>
            <button
                v-if="search || category || author"
                type="button"
                class="btn btn-light"
                @click="reset"
            >
                <i class="bi bi-x-lg"></i> Clear
            </button>
        </section>

        <section v-if="loading" class="catalog-grid skeleton-grid">
            <article v-for="n in 6" :key="n" class="skeleton-card">
                <div class="skeleton-cover"></div>
                <div class="skeleton-card-body">
                    <div class="skeleton-line short"></div>
                    <div class="skeleton-line"></div>
                    <div class="skeleton-meta">
                        <div class="skeleton-pill"></div>
                        <div class="skeleton-pill short"></div>
                    </div>
                    <div class="skeleton-actions"></div>
                </div>
            </article>
        </section>

        <section v-else-if="books.data.length" class="catalog-grid">
            <BookCard
                v-for="book in books.data"
                :key="book.id"
                :book="book"
                :bookmarked="bookmarkedSet.has(book.id)"
            />
        </section>

        <div v-else class="empty-state panel">
            <i class="bi bi-search"></i>
            <h3>No books matched your search</h3>
            <p>Try a different keyword, clear your filters, or browse all published titles.</p>
            <button class="btn btn-primary" @click="reset">Reset filters</button>
        </div>

        <nav v-if="books.links?.length > 3" class="pagination-row">
            <Link
                v-for="link in books.links"
                :key="link.label"
                :href="link.url ?? '#'"
                :class="{ active: link.active, disabled: !link.url }"
                v-html="link.label"
            />
        </nav>
    </PublicLayout>
</template>
