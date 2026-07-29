<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import MemberLayout from '@/Layouts/MemberLayout.vue';

const props = defineProps({
    bookmarks: Object,
});

const removing = ref(null);

const remove = async (book) => {
    const bookmarkId = book.pivot.id;

    if (removing.value === bookmarkId) return;
    removing.value = bookmarkId;
    try {
        await router.delete(route('member.bookmarks.destroy', bookmarkId), {
            preserveScroll: true,
        });
    } finally {
        removing.value = null;
    }
};
</script>

<template>
    <Head title="My bookmarks · Folio" />
    <MemberLayout>
        <header class="member-head">
            <div>
                <span class="eyebrow">Your shelf</span>
                <h1>My bookmarks</h1>
                <p>The titles you've saved to read, revisit, or simply hold onto.</p>
            </div>
            <div class="head-actions">
                <Link :href="route('public.books.index')" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Add more
                </Link>
            </div>
        </header>

        <section v-if="bookmarks.data.length" class="bookmark-grid">
            <article v-for="book in bookmarks.data" :key="book.id" class="bookmark-card">
                <Link :href="route('public.books.show', book.id)" class="bm-cover">
                    <img v-if="book.cover_image" :src="`/storage/${book.cover_image}`" :alt="book.title">
                    <span v-else class="book-fallback">
                        <small>Folio edition</small>
                        <strong>{{ book.title }}</strong>
                        <i class="bi bi-book"></i>
                    </span>
                </Link>
                <div class="bm-body">
                    <span v-if="book.author" class="meta-author">{{ book.author.name }}</span>
                    <h3>
                        <Link :href="route('public.books.show', book.id)">{{ book.title }}</Link>
                    </h3>
                    <div class="bm-actions">
                        <Link :href="route('public.books.show', book.id)" class="btn btn-soft btn-sm">
                            <i class="bi bi-eye"></i> Open
                        </Link>
                        <button
                            class="icon-btn danger-btn"
                            :disabled="removing === book.pivot.id"
                            title="Remove bookmark"
                            @click="remove(book)"
                        >
                            <i class="bi" :class="removing === book.pivot.id ? 'bi-hourglass-split' : 'bi-trash3'"></i>
                        </button>
                    </div>
                </div>
            </article>
        </section>

        <div v-else class="panel empty-state">
            <i class="bi bi-bookmark"></i>
            <h3>No bookmarks yet</h3>
            <p>Start exploring the catalog and tap the bookmark icon on any book you love.</p>
            <Link :href="route('public.books.index')" class="btn btn-primary">
                Browse the catalog
            </Link>
        </div>

        <nav v-if="bookmarks.links?.length > 3" class="pagination-row">
            <Link
                v-for="link in bookmarks.links"
                :key="link.label"
                :href="link.url ?? '#'"
                :class="{ active: link.active, disabled: !link.url }"
                v-html="link.label"
            />
        </nav>
    </MemberLayout>
</template>
