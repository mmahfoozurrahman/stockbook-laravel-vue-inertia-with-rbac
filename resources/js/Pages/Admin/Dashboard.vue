<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    metrics: Object,
    recentBooks: Array,
    topBooks: Array,
});

const greeting = new Date().getHours() < 12
    ? 'Good morning'
    : new Date().getHours() < 18
        ? 'Good afternoon'
        : 'Good evening';
</script>

<template>
    <Head title="Dashboard" />
    <AdminLayout>
        <header class="page-head dashboard-head">
            <div>
                <span class="eyebrow">Library overview</span>
                <h1>{{ greeting }}, {{ $page.props.auth.user.name.split(' ')[0] }}.</h1>
                <p>Here’s the quiet pulse of your collection today.</p>
            </div>
            <Link :href="route('admin.books.index')" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add a book
            </Link>
        </header>

        <section class="metric-grid">
            <article class="metric-card">
                <span class="metric-icon mint"><i class="bi bi-journals"></i></span>
                <div>
                    <small>Total collection</small>
                    <strong>{{ metrics.books }}</strong>
                    <p>Titles in your catalog</p>
                </div>
                <span class="metric-trend">Live</span>
            </article>
            <article class="metric-card">
                <span class="metric-icon sand"><i class="bi bi-bookmark-check"></i></span>
                <div>
                    <small>Published titles</small>
                    <strong>{{ metrics.published }}</strong>
                    <p>Ready for readers</p>
                </div>
                <span class="metric-trend">Curated</span>
            </article>
            <article class="metric-card">
                <span class="metric-icon blue"><i class="bi bi-people"></i></span>
                <div>
                    <small>Active members</small>
                    <strong>{{ metrics.users }}</strong>
                    <p>Workspace access</p>
                </div>
                <span class="metric-trend">Team</span>
            </article>
            <article class="metric-card warning">
                <span class="metric-icon coral"><i class="bi bi-exclamation-circle"></i></span>
                <div>
                    <small>Low-stock watch</small>
                    <strong>{{ metrics.lowStock }}</strong>
                    <p>Need attention</p>
                </div>
                <span class="metric-trend">Review</span>
            </article>
        </section>

        <section class="dashboard-grid">
            <article class="panel span-2">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Catalog activity</span>
                        <h2>Recent additions</h2>
                    </div>
                    <Link :href="route('admin.books.index')" class="text-link">
                        View catalog <i class="bi bi-arrow-up-right"></i>
                    </Link>
                </div>
                <div v-if="recentBooks.length" class="table-responsive">
                    <table class="table app-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th>Stock</th>
                                <th>Added</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="book in recentBooks" :key="book.id">
                                <td>
                                    <div class="book-cell">
                                        <span class="mini-cover">{{ book.title.charAt(0) }}</span>
                                        <strong>{{ book.title }}</strong>
                                    </div>
                                </td>
                                <td>{{ book.author?.name || 'Unassigned' }}</td>
                                <td>
                                    <span class="status-pill" :class="book.status">{{ book.status }}</span>
                                </td>
                                <td>{{ book.stock }} copies</td>
                                <td>
                                    {{ new Date(book.created_at).toLocaleDateString(undefined, { month: 'short', day: 'numeric' }) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="empty-state">
                    <i class="bi bi-journal-plus"></i>
                    <h3>Your first shelf is waiting</h3>
                    <p>Add a book to begin seeing catalog activity here.</p>
                    <Link :href="route('admin.books.index')" class="btn btn-primary">Add first book</Link>
                </div>
            </article>

            <article class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Shelf signal</span>
                        <h2>Well stocked</h2>
                    </div>
                    <span class="round-icon"><i class="bi bi-bar-chart"></i></span>
                </div>
                <div class="rank-list">
                    <div v-for="(book, index) in topBooks" :key="book.id" class="rank-row">
                        <span class="rank">{{ String(index + 1).padStart(2, '0') }}</span>
                        <div>
                            <strong>{{ book.title }}</strong>
                            <small>{{ book.author?.name || 'Unknown author' }}</small>
                        </div>
                        <span class="count">{{ book.stock }}</span>
                    </div>
                    <div v-if="!topBooks.length" class="empty-compact">Stock signals will appear here.</div>
                </div>
            </article>
        </section>
    </AdminLayout>
</template>
