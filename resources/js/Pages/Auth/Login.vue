<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
const form = useForm({ email: 'admin@booklist.test', password: 'password', remember: true });
const submit = () => form.post(route('login'));
</script>

<template>
  <Head title="Sign in" />
  <main class="auth-shell">
    <section class="auth-story">
      <div class="auth-brand"><span><i class="bi bi-book-half"></i></span> Folio</div>
      <div class="auth-copy">
        <span class="eyebrow light">Curated knowledge, beautifully managed</span>
        <h1>Your library,<br><em>in perfect order.</em></h1>
        <p>A quiet, focused workspace for cataloging remarkable books and helping readers find their next one.</p>
      </div>
      <div class="auth-quote"><i class="bi bi-quote"></i><p>“A library is not a luxury but one of the necessities of life.”</p><small>Henry Ward Beecher</small></div>
    </section>
    <section class="auth-form-panel">
      <form class="auth-form" @submit.prevent="submit">
        <div><span class="eyebrow">Welcome back</span><h2>Sign in to Folio</h2><p>Continue where your collection left off.</p></div>
        <label>Email address<input v-model="form.email" type="email" autocomplete="email" autofocus :class="{ invalid: form.errors.email }"><small v-if="form.errors.email" class="error">{{ form.errors.email }}</small></label>
        <label>Password<input v-model="form.password" type="password" autocomplete="current-password" :class="{ invalid: form.errors.password }"><small v-if="form.errors.password" class="error">{{ form.errors.password }}</small></label>
        <div class="form-inline"><label class="check"><input v-model="form.remember" type="checkbox"> Keep me signed in</label><span class="muted">Secure access</span></div>
        <button class="btn btn-primary btn-lg w-100" :disabled="form.processing"><span v-if="form.processing" class="spinner-border spinner-border-sm"></span><span v-else>Enter workspace</span><i class="bi bi-arrow-right"></i></button>
        <p class="auth-switch">New to Folio? <Link :href="route('register')">Create an account</Link></p>
        <div class="demo-note"><i class="bi bi-stars"></i><span><strong>Demo workspace</strong>admin@booklist.test · password</span></div>
      </form>
    </section>
  </main>
</template>
