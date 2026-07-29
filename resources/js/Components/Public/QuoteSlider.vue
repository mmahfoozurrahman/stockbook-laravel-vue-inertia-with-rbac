<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    quotes: { type: Array, default: () => [] },
});

const current = ref(0);
let timer;

const goTo = (i) => { current.value = (i + props.quotes.length) % props.quotes.length; };
const next = () => goTo(current.value + 1);
const prev = () => goTo(current.value - 1);

const start = () => {
    if (props.quotes.length < 2) return;
    stop();
    timer = setInterval(next, 6000);
};
const stop = () => { if (timer) clearInterval(timer); };

onMounted(start);
onUnmounted(stop);
</script>

<template>
    <section class="quote-slider" v-if="quotes.length">
        <div class="quote-track" @mouseenter="stop" @mouseleave="start">
            <Transition name="quote-fade" mode="out-in">
                <article class="quote-card" :key="current">
                    <i class="bi bi-quote quote-mark"></i>
                    <blockquote>{{ quotes[current].body }}</blockquote>
                    <footer>
                        <strong v-if="quotes[current].attribution">{{ quotes[current].attribution }}</strong>
                        <small v-if="quotes[current].source">— {{ quotes[current].source }}</small>
                    </footer>
                </article>
            </Transition>
            <div class="quote-controls" v-if="quotes.length > 1">
                <button class="quote-arrow" type="button" @click="prev" aria-label="Previous quote">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <div class="quote-dots">
                    <button
                        v-for="(q, i) in quotes"
                        :key="q.id ?? i"
                        type="button"
                        :class="{ active: i === current }"
                        :aria-label="`Go to quote ${i + 1}`"
                        @click="goTo(i)"
                    ></button>
                </div>
                <button class="quote-arrow" type="button" @click="next" aria-label="Next quote">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
    </section>
</template>
