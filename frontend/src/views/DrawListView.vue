<script setup lang="ts">
import {computed, onMounted, ref} from 'vue'
import { RouterLink } from 'vue-router'
import {listDraws} from "@/api/draws.ts";
import type {Draw} from "@/types/draw.ts";
const isLoading = ref<boolean>(false)
const data = ref<Draw[]>([])
const error = ref<string | null>(null)

const hasAny = computed(() => data.value.length > 0)

onMounted(() => {
  isLoading.value = true
  error.value = null
  listDraws()
    .then((response) => {
      data.value = response
    })
    .catch((e: any) => {
      error.value = e?.message || 'Nie udało się pobrać listy losowań'
    })
    .finally(() => {
      isLoading.value = false
    })
})
</script>

<template>
  <main class="container" :aria-busy="isLoading ? 'true' : 'false'">
    <header class="topbar">
      <h1>Losowania</h1>
      <RouterLink class="btn" to="/create">+ Utwórz</RouterLink>
    </header>

    <div v-if="isLoading" class="loading" aria-live="polite">Ładowanie…</div>

    <p v-else-if="error" class="error">{{ error }}</p>

    <div v-else-if="!hasAny" class="empty">
      Brak losowań. Utwórz pierwsze.
    </div>

    <ul class="list" v-else>
      <li v-for="d in data" :key="d.id" class="card">
        <div class="card-header">
          <h2 class="name">
            <RouterLink :to="{ name: 'draw-detail', params: { id: d.id } }">{{ d.name }}</RouterLink>
          </h2>
          <span class="status" :class="d.status === 'FINISHED' ? 'done' : 'in'">
            {{ d.status === 'FINISHED' ? 'Zakończone' : 'W trakcie' }}
          </span>
        </div>
        <div class="meta">
          Opcje: {{ d.options.length }} • Wyników do wylosowania: {{ d.resultsCount }}
        </div>
        <div v-if="d.status === 'FINISHED' && d.result" class="winners">
          <strong>Wylosowane:</strong>
          <ul class="winners-list">
            <li v-for="w in d.result.winners" :key="w.id">{{ w.content }} — <em>{{ w.author }}</em></li>
          </ul>
        </div>
      </li>
    </ul>
  </main>
  
</template>

<style scoped>
.container { max-width: 900px; margin: 0 auto; padding: 1rem; }
.topbar { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1rem; }
.btn { padding: 0.5rem 0.75rem; border: 1px solid var(--color-border); border-radius: 6px; text-decoration: none; }
.list { list-style: none; padding: 0; display: grid; grid-template-columns: 1fr; gap: 0.75rem; }
.card { border: 1px solid var(--color-border); border-radius: 8px; padding: 0.75rem; }
.card-header { display: flex; align-items: baseline; justify-content: space-between; gap: 1rem; }
.name { margin: 0; font-size: 1.1rem; }
.status { font-size: 0.85rem; padding: 0.25rem 0.5rem; border-radius: 999px; border: 1px solid var(--color-border); }
.status.done { background: #1e7e34; color: #fff; border-color: #1e7e34; }
.status.in { background: #f1f1f1; color: #333; }
.meta { color: var(--vt-c-text-light-2); font-size: 0.9rem; margin-top: 0.25rem; }
.winners { margin-top: 0.5rem; }
.winners-list { margin: 0.25rem 0 0; padding-left: 1rem; }
.empty { color: var(--vt-c-text-light-2); }
.loading { color: var(--vt-c-text-light-2); }
.error { color: #c62828; }
</style>
