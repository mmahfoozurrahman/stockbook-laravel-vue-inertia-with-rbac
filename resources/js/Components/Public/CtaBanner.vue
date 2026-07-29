<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <section class="cta-banner">
        <div class="cta-inner">
            <div class="cta-copy">
                <span class="eyebrow light">Save your reading list</span>
                <h2>Keep a private shelf of the books you love.</h2>
                <p>
                    Sign in to bookmark titles, revisit them anytime, and let Folio remember
                    the stories that stayed with you. No noise, no algorithm — just your shelf.
                </p>
            </div>
            <div class="cta-actions">
                <Link :href="route('register')" class="btn btn-primary btn-lg">
                    <i class="bi bi-bookmark-plus"></i> Start your shelf
                </Link>
                <Link :href="route('login')" class="btn btn-ghost-light btn-lg">
                    I already have an account
                </Link>
            </div>
        </div>
    </section>
</template>
