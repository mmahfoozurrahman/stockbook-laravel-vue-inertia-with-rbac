<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppModal from '@/Components/AppModal.vue';
import RichEditor from '@/Components/RichEditor.vue';
import { usePermission } from '@/composables/usePermission';
import { useSweetAlert } from '@/composables/useSweetAlert';

const props = defineProps({
    books: Object,
    authors: Array,
    categories: Array,
    tags: Array,
    filters: Object,
});
const { can } = usePermission();
const { confirmDelete } = useSweetAlert();

const modal = ref(false);
const preview = ref(null);
const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const editing = ref(null);

const blank = () => ({
    title: '',
    author_id: '',
    isbn: '',
    description: '',
    published_at: '',
    status: 'draft',
    stock: 0,
    cover: null,
    categories: [],
    tags: [],
});
const form = useForm(blank());

let timer;
watch([search, status], () => {
    clearTimeout(timer);
    timer = setTimeout(
        () => router.get(
            route('admin.books.index'),
            { search: search.value, status: status.value },
            { preserveState: true, replace: true },
        ),
        350,
    );
});

const openCreate = () => {
    editing.value = null;
    form.defaults(blank());
    form.reset();
    form.clearErrors();
    modal.value = true;
};

const openEdit = (book) => {
    editing.value = book;
    form.defaults({
        title: book.title,
        author_id: book.author_id || '',
        isbn: book.isbn || '',
        description: book.description || '',
        published_at: book.published_at?.slice(0, 10) || '',
        status: book.status,
        stock: book.stock,
        cover: null,
        categories: book.categories.map((i) => i.id),
        tags: book.tags.map((i) => i.id),
    });
    form.reset();
    form.clearErrors();
    modal.value = true;
};

const submit = () => {
    if (editing.value) {
        form.transform((data) => ({ ...data, _method: 'put' }))
            .post(route('admin.books.update', editing.value.id), {
                forceFormData: true,
                preserveScroll: true,
                onSuccess: () => { modal.value = false; },
            });
    } else {
        form.post(route('admin.books.store'), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => { modal.value = false; },
        });
    }
};

const destroy = async (book) => {
    if ((await confirmDelete(`Remove “${book.title}”?`)).isConfirmed) {
        router.delete(route('admin.books.destroy', book.id), { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="Books" />
    <AdminLayout>
        <header class="page-head">
            <div>
                <span class="eyebrow">Catalog</span>
                <h1>Books</h1>
                <p>Keep every edition, shelf signal, and story beautifully organized.</p>
            </div>
            <button
                v-if="can('books.create')"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg"></i> Add book
            </button>
        </header>

        <section class="panel filters-panel">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input
                    v-model="search"
                    placeholder="Search title or ISBN…"
                    aria-label="Search books"
                >
                <kbd>⌘ K</kbd>
            </div>
            <select v-model="status" class="form-select compact-select">
                <option value="">All statuses</option>
                <option value="published">Published</option>
                <option value="draft">Draft</option>
                <option value="archived">Archived</option>
            </select>
            <span class="result-count">
                {{ books.total }} {{ books.total === 1 ? 'title' : 'titles' }}
            </span>
        </section>

        <section v-if="books.data.length" class="book-grid">
            <article v-for="book in books.data" :key="book.id" class="book-card">
                <button class="cover-wrap" type="button" @click="preview = book">
                    <img
                        v-if="book.cover_image"
                        :src="`/storage/${book.cover_image}`"
                        :alt="book.title"
                    >
                    <span v-else class="cover-fallback">
                        <small>Folio edition</small>
                        <strong>{{ book.title }}</strong>
                        <i class="bi bi-book"></i>
                    </span>
                    <span class="status-pill cover-status" :class="book.status">
                        {{ book.status }}
                    </span>
                </button>
                <div class="book-card-body">
                    <div>
                        <span class="book-author">{{ book.author?.name || 'Unassigned author' }}</span>
                        <h2>{{ book.title }}</h2>
                    </div>
                    <div class="meta-line">
                        <span><i class="bi bi-upc-scan"></i> {{ book.isbn || 'No ISBN' }}</span>
                        <span :class="{ danger: book.stock <= 3 }">
                            <i class="bi bi-box-seam"></i> {{ book.stock }} in stock
                        </span>
                    </div>
                    <div class="chip-row">
                        <span
                            v-for="category in book.categories.slice(0, 2)"
                            :key="category.id"
                            class="soft-chip"
                        >
                            {{ category.name }}
                        </span>
                        <span v-if="!book.categories.length" class="soft-chip muted-chip">
                            Uncategorized
                        </span>
                    </div>
                    <div class="card-actions">
                        <button class="btn btn-soft" @click="preview = book">
                            <i class="bi bi-eye"></i> Preview
                        </button>
                        <button
                            v-if="can('books.update')"
                            class="icon-btn"
                            title="Edit"
                            @click="openEdit(book)"
                        >
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button
                            v-if="can('books.delete')"
                            class="icon-btn danger-btn"
                            title="Delete"
                            @click="destroy(book)"
                        >
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>
            </article>
        </section>

        <div v-else class="panel empty-state">
            <i class="bi bi-journal-plus"></i>
            <h3>No books found</h3>
            <p>
                {{ search || status
                    ? 'Try easing your search or filter.'
                    : 'Build your collection one remarkable title at a time.' }}
            </p>
            <button v-if="can('books.create')" class="btn btn-primary" @click="openCreate">
                Add a book
            </button>
        </div>

        <nav v-if="books.links?.length > 3" class="pagination-row">
            <button
                v-for="link in books.links"
                :key="link.label"
                :disabled="!link.url"
                :class="{ active: link.active }"
                @click="link.url && router.visit(link.url)"
                v-html="link.label"
            ></button>
        </nav>

        <AppModal
            :open="modal"
            :title="editing ? 'Edit book' : 'Add a new book'"
            kicker="Catalog editor"
            size="wide"
            @close="modal = false"
        >
            <form @submit.prevent="submit">
                <div class="form-grid">
                    <label>
                        Book title
                        <input v-model="form.title" :class="{ invalid: form.errors.title }">
                        <small v-if="form.errors.title" class="error">{{ form.errors.title }}</small>
                    </label>
                    <label>
                        Author
                        <select v-model="form.author_id">
                            <option value="">Unassigned</option>
                            <option v-for="author in authors" :key="author.id" :value="author.id">
                                {{ author.name }}
                            </option>
                        </select>
                    </label>
                    <label>
                        ISBN
                        <input v-model="form.isbn" placeholder="978-…">
                        <small v-if="form.errors.isbn" class="error">{{ form.errors.isbn }}</small>
                    </label>
                    <label>
                        Published date
                        <input v-model="form.published_at" type="date">
                    </label>
                    <label>
                        Status
                        <select v-model="form.status">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                        </select>
                    </label>
                    <label>
                        Stock
                        <input v-model.number="form.stock" type="number" min="0">
                    </label>
                </div>
                <label>
                    Description
                    <RichEditor
                        v-model="form.description"
                        placeholder="Add a polished synopsis, editorial note, or catalog description…"
                    />
                </label>
                <div class="form-grid">
                    <label>
                        Categories
                        <select v-model="form.categories" multiple>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                        <small class="field-hint">Hold Ctrl / Cmd to select several.</small>
                    </label>
                    <label>
                        Tags
                        <select v-model="form.tags" multiple>
                            <option v-for="tag in tags" :key="tag.id" :value="tag.id">
                                {{ tag.name }}
                            </option>
                        </select>
                        <small class="field-hint">Use tags for discovery and curation.</small>
                    </label>
                </div>
                <label class="upload-box">
                    <input
                        type="file"
                        accept="image/*"
                        @change="form.cover = $event.target.files[0]"
                    >
                    <i class="bi bi-cloud-arrow-up"></i>
                    <span>
                        <strong>Choose a cover image</strong>
                        <small>JPG or PNG, up to 3 MB</small>
                    </span>
                </label>
                <div class="modal-actions">
                    <button type="button" class="btn btn-light" @click="modal = false">Cancel</button>
                    <button class="btn btn-primary" :disabled="form.processing">
                        <span v-if="form.processing" class="spinner-border spinner-border-sm"></span>
                        {{ editing ? 'Save changes' : 'Add to catalog' }}
                    </button>
                </div>
            </form>
        </AppModal>

        <AppModal
            :open="!!preview"
            :title="preview?.title || ''"
            kicker="Book preview"
            @close="preview = null"
        >
            <div v-if="preview" class="book-preview">
                <div class="preview-cover">
                    <img v-if="preview.cover_image" :src="`/storage/${preview.cover_image}`">
                    <span v-else class="cover-fallback">
                        <small>Folio edition</small>
                        <strong>{{ preview.title }}</strong>
                        <i class="bi bi-book"></i>
                    </span>
                </div>
                <div>
                    <span class="book-author">{{ preview.author?.name || 'Unknown author' }}</span>
                    <div class="chip-row">
                        <span class="status-pill" :class="preview.status">{{ preview.status }}</span>
                        <span class="soft-chip">{{ preview.stock }} copies</span>
                    </div>
                    <div class="prose" v-html="preview.description || '<p>No description has been added yet.</p>'"></div>
                    <small class="muted">ISBN · {{ preview.isbn || 'Not recorded' }}</small>
                </div>
            </div>
        </AppModal>
    </AdminLayout>
</template>
