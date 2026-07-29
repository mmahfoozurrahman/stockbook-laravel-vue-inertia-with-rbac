<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    stats: { type: Object, default: () => ({}) },
});
</script>

<template>
    <section class="hero">
        <div class="hero-bg">
            <div class="hero-glow glow-1"></div>
            <div class="hero-glow glow-2"></div>
        </div>
        <div class="hero-inner">
            <div class="hero-copy">
                <span class="eyebrow">A library for curious readers</span>
                <h1>
                    Quietly catalogued.<br>
                    <em>Beautifully read.</em>
                </h1>
                <p class="hero-lede">
                    Folio is a calm, considered workspace for organizing remarkable books.
                    Discover published titles, save the ones you love, and return whenever you're ready to read.
                </p>
                <div class="hero-actions">
                    <Link :href="route('public.books.index')" class="btn btn-primary btn-lg">
                        <i class="bi bi-search"></i> Browse the catalog
                    </Link>
                    <Link :href="route('register')" class="btn btn-ghost btn-lg">
                        Create a free account <i class="bi bi-arrow-right"></i>
                    </Link>
                </div>
                <div class="hero-stats" v-if="stats">
                    <div>
                        <strong>{{ stats.books ?? 0 }}</strong>
                        <small>Published titles</small>
                    </div>
                    <div>
                        <strong>{{ stats.authors ?? 0 }}</strong>
                        <small>Featured authors</small>
                    </div>
                    <div>
                        <strong>{{ stats.categories ?? 0 }}</strong>
                        <small>Curated categories</small>
                    </div>
                </div>
            </div>
            <div class="hero-art">
                <div class="art-stack">
                    <div class="art-book art-book-1">
                        <div class="art-spine"></div>
                        <div class="art-cover">
                            <span class="art-kicker">Volume I</span>
                            <strong>The Reading Year</strong>
                            <em>An anthology</em>
                        </div>
                    </div>
                    <div class="art-book art-book-2">
                        <div class="art-cover art-cover-mint">
                            <span class="art-kicker">Folio</span>
                            <strong>Library Notes</strong>
                            <em>On craft & curiosity</em>
                        </div>
                    </div>
                    <div class="art-book art-book-3">
                        <div class="art-cover art-cover-sand">
                            <span class="art-kicker">Edition</span>
                            <strong>Shelf Talk</strong>
                            <em>Reading rooms</em>
                        </div>
                    </div>
                </div>
                <div class="art-pulse"></div>
            </div>
        </div>
    </section>
</template>
