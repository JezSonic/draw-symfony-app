<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router'
import {onMounted} from "vue";
import {ensureXsrfCookie} from "@/api/http"

onMounted(() => {
  ensureXsrfCookie()
})
</script>

<template>
  <div>
    <div class="nav">
      <RouterLink to="/" class="brand">Losowania</RouterLink>
      <RouterLink to="/">Lista</RouterLink>
      <RouterLink to="/create">Utwórz</RouterLink>
    </div>
    <RouterView />
  </div>
</template>

<style scoped>
.brand { font-weight: 700; text-decoration: none; color: inherit; }
.nav { display: flex; gap: 0.5rem; min-width: 470px;}
.nav a { text-decoration: none; padding: 0.25rem 0.5rem; border-radius: 6px; }
.nav a.router-link-active { background: var(--color-background-soft); }
</style>
