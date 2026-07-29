<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Hero from '@/Components/Public/Hero.vue';
import BookCard from '@/Components/Public/BookCard.vue';
import QuoteSlider from '@/Components/Public/QuoteSlider.vue';
import CtaBanner from '@/Components/Public/CtaBanner.vue';

const props = defineProps({
    featuredBooks: Array,
    categories: Array,
    recentBooks: Array,
    quotes: Array,
});

const stats = computed(() => ({
    books: props.featuredBooks?.length ?? 0,
    authors: new Set((props.featuredBooks ?? []).map((b) => b.author?.id).filter(Boolean)).size,
    categories: props.categories?.length ?? 0,
}));
</script>

<template>
    <Head title="Folio · A library for curious readers" />
    <PublicLayout flush-footer>
        <Hero :stats="stats" />

        <section v-if="featuredBooks?.length" class="section section-featured">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Handpicked</span>
                    <h2>Featured this season</h2>
                    <p>Curated titles we're reading, recommending, and returning to.</p>
                </div>
                <Link :href="route('public.books.index')" class="text-link">
                    See all <i class="bi bi-arrow-up-right"></i>
                </Link>
            </div>
            <div class="featured-grid">
                <BookCard
                    v-for="book in featuredBooks"
                    :key="book.id"
                    :book="book"
                />
            </div>
        </section>

        <QuoteSlider :quotes="quotes" />

        <section v-if="categories?.length" class="section section-categories">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Browse by</span>
                    <h2>Categories</h2>
                </div>
            </div>
            <div class="category-row">
                <Link
                    v-for="cat in categories"
                    :key="cat.id"
                    :href="route('public.books.index', { category: cat.slug })"
                    class="category-pill"
                >
                    <i class="bi bi-collection"></i>
                    {{ cat.name }}
                </Link>
            </div>
        </section>

        <section v-if="recentBooks?.length" class="section section-recent">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Latest from the catalog</span>
                    <h2>Freshly published</h2>
                </div>
                <Link :href="route('public.books.index')" class="text-link">
                    Browse the catalog <i class="bi bi-arrow-up-right"></i>
                </Link>
            </div>
            <div class="recent-grid">
                <BookCard
                    v-for="book in recentBooks"
                    :key="book.id"
                    :book="book"
                />
            </div>
        </section>

        <CtaBanner />
    </PublicLayout>
</template>
