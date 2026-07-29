<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    book: { type: Object, required: true },
    bookmarked: { type: Boolean, default: false },
    showBookmark: { type: Boolean, default: true },
});

const page = usePage();
const isAuth = computed(() => !!page.props.auth?.user);
const busy = ref(false);
const local = ref(props.bookmarked);

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
        local.value = !local.value;
    } finally {
        busy.value = false;
    }
};
</script>

<template>
    <article class="book-card-public" :class="{ featured: book.is_featured }">
        <Link :href="route('public.books.show', book.id)" class="book-cover-link">
            <img v-if="book.cover_image" :src="`/storage/${book.cover_image}`" :alt="book.title">
            <span v-else class="book-fallback">
                <small>Folio edition</small>
                <strong>{{ book.title }}</strong>
                <i class="bi bi-book"></i>
            </span>
            <span v-if="book.is_featured" class="featured-pill">
                <i class="bi bi-stars"></i> Featured
            </span>
        </Link>
        <div class="book-card-body">
            <div class="book-card-meta">
                <span v-if="book.author" class="meta-author">{{ book.author.name }}</span>
                <span v-if="book.published_at" class="meta-date">
                    {{ new Date(book.published_at).getFullYear() }}
                </span>
            </div>
            <h3>
                <Link :href="route('public.books.show', book.id)">{{ book.title }}</Link>
            </h3>
            <div v-if="book.categories?.length" class="book-chips">
                <span v-for="cat in book.categories.slice(0, 2)" :key="cat.id" class="chip">
                    {{ cat.name }}
                </span>
            </div>
            <div class="book-card-actions">
                <Link :href="route('public.books.show', book.id)" class="btn btn-soft">
                    <i class="bi bi-eye"></i> Preview
                </Link>
                <button
                    v-if="showBookmark"
                    class="icon-btn"
                    :class="{ active: local }"
                    :disabled="busy"
                    :title="local ? 'Remove bookmark' : 'Bookmark this book'"
                    @click="toggleBookmark"
                >
                    <i class="bi" :class="local ? 'bi-bookmark-fill' : 'bi-bookmark'"></i>
                </button>
            </div>
        </div>
    </article>
</template>
