<script setup>
defineProps({
    open: Boolean,
    title: String,
    kicker: { type: String, default: 'Catalog workspace' },
    size: { type: String, default: '' },
});
defineEmits(['close']);
</script>

<template>
    <Teleport to="body">
        <Transition name="modal">
            <div
                v-if="open"
                class="modal-shell"
                @mousedown.self="$emit('close')"
            >
                <section
                    class="modal-card"
                    :class="size"
                    role="dialog"
                    aria-modal="true"
                    :aria-label="title"
                >
                    <header class="modal-head">
                        <div>
                            <span class="eyebrow">{{ kicker }}</span>
                            <h2>{{ title }}</h2>
                        </div>
                        <button
                            class="icon-btn"
                            type="button"
                            aria-label="Close"
                            @click="$emit('close')"
                        >
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </header>
                    <div class="modal-rule"></div>
                    <div class="modal-body">
                        <slot />
                    </div>
                </section>
            </div>
        </Transition>
    </Teleport>
</template>
