<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import BookCard from '@/Components/Public/BookCard.vue';

const props = defineProps({
    book: Object,
    related: Array,
    isBookmarked: Boolean,
});

const page = usePage();
const isAuth = computed(() => !!page.props.auth?.user);

const bookmarked = ref(props.isBookmarked);
const busy = ref(false);

const toggleBookmark = async () => {
    if (!isAuth.value) {
        router.visit(route('login'));
        return;
    }
    busy.value = true;
    try {
        await router.post(route('member.bookmarks.toggle', props.book.id), {}, {
            preserveScroll: true,
        });
        bookmarked.value = !bookmarked.value;
    } finally {
        busy.value = false;
    }
};
</script>

<template>
    <Head :title="`${book.title} · Folio`" />
    <PublicLayout>
        <article class="book-detail">
            <nav class="crumbs">
                <Link :href="route('home')">Home</Link>
                <i class="bi bi-chevron-right"></i>
                <Link :href="route('public.books.index')">Catalog</Link>
                <i class="bi bi-chevron-right"></i>
                <span>{{ book.title }}</span>
            </nav>

            <div class="book-detail-grid">
                <div class="book-detail-cover">
                    <img v-if="book.cover_image" :src="`/storage/${book.cover_image}`" :alt="book.title">
                    <span v-else class="book-fallback large">
                        <small>Folio edition</small>
                        <strong>{{ book.title }}</strong>
                        <i class="bi bi-book"></i>
                    </span>
                </div>
                <div class="book-detail-body">
                    <span class="eyebrow">
                        {{ book.author?.name ?? 'Unattributed' }}
                    </span>
                    <h1>{{ book.title }}</h1>
                    <div class="book-detail-meta">
                        <span v-if="book.published_at">
                            <i class="bi bi-calendar3"></i>
                            Published {{ new Date(book.published_at).toLocaleDateString() }}
                        </span>
                        <span v-if="book.isbn">
                            <i class="bi bi-upc-scan"></i>
                            ISBN · {{ book.isbn }}
                        </span>
                        <span v-if="book.categories?.length" class="book-chips">
                            <span v-for="c in book.categories" :key="c.id" class="chip">{{ c.name }}</span>
                        </span>
                    </div>

                    <div class="book-detail-actions">
                        <button
                            class="btn"
                            :class="bookmarked ? 'btn-light' : 'btn-primary'"
                            :disabled="busy"
                            @click="toggleBookmark"
                        >
                            <i class="bi" :class="bookmarked ? 'bi-bookmark-fill' : 'bi-bookmark-plus'"></i>
                            {{ bookmarked ? 'Bookmarked' : 'Bookmark this book' }}
                        </button>
                        <Link :href="route('public.books.index')" class="btn btn-ghost">
                            <i class="bi bi-arrow-left"></i> Back to catalog
                        </Link>
                    </div>
                </div>
            </div>

            <section v-if="book.description" class="book-description panel">
                <h3>About this book</h3>
                <div class="prose" v-html="book.description"></div>
            </section>

            <section v-if="related?.length" class="related">
                <div class="section-head">
                    <div>
                        <span class="eyebrow">More to explore</span>
                        <h2>You might also like</h2>
                    </div>
                </div>
                <div class="related-grid">
                    <BookCard
                        v-for="r in related"
                        :key="r.id"
                        :book="r"
                    />
                </div>
            </section>
        </article>
    </PublicLayout>
</template>
