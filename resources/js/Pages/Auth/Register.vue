<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
const form = useForm({ name: '', email: '', password: '', password_confirmation: '' });
const submit = () => form.post(route('register'));
</script>

<template>
  <Head title="Create account" />
  <main class="auth-shell">
    <section class="auth-story">
      <div class="auth-brand"><span><i class="bi bi-book-half"></i></span> Folio</div>
      <div class="auth-copy"><span class="eyebrow light">Begin your collection</span><h1>Make room for<br><em>remarkable stories.</em></h1><p>Join a considered workspace that keeps every author, edition, and shelf beautifully organized.</p></div>
      <div class="auth-quote"><i class="bi bi-stars"></i><p>Compact tools. Calm decisions. A catalog your whole team can trust.</p></div>
    </section>
    <section class="auth-form-panel">
      <form class="auth-form" @submit.prevent="submit">
        <div><span class="eyebrow">Create account</span><h2>Start your Folio</h2><p>Your personal library workspace is moments away.</p></div>
        <label>Full name<input v-model="form.name" autocomplete="name" :class="{ invalid: form.errors.name }"><small v-if="form.errors.name" class="error">{{ form.errors.name }}</small></label>
        <label>Email address<input v-model="form.email" type="email" autocomplete="email" :class="{ invalid: form.errors.email }"><small v-if="form.errors.email" class="error">{{ form.errors.email }}</small></label>
        <div class="form-grid">
          <label>Password<input v-model="form.password" type="password" autocomplete="new-password"></label>
          <label>Confirm password<input v-model="form.password_confirmation" type="password" autocomplete="new-password"></label>
        </div>
        <small v-if="form.errors.password" class="error">{{ form.errors.password }}</small>
        <button class="btn btn-primary btn-lg w-100" :disabled="form.processing">Create workspace <i class="bi bi-arrow-right"></i></button>
        <p class="auth-switch">Already a member? <Link :href="route('login')">Sign in</Link></p>
      </form>
    </section>
  </main>
</template>
