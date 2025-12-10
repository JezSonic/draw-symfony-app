<script setup lang="ts">
import {computed, onMounted, ref} from 'vue'
import {useRoute, useRouter} from 'vue-router'
import {getDraw, runDraw} from "@/api/draws.ts";
import type {Draw} from "@/types/draw.ts";

const route = useRoute()
const router = useRouter()
const isLoading = ref<boolean>(false)
const id = computed(() => String(route.params.id))
const draw = ref<Draw | null>(null)
const error = ref<string | null>(null)
const running = ref(false)

async function run() {
  if (!draw.value) return
  if (draw.value.status === 'FINISHED') return
  running.value = true
  error.value = null
  try {
    // simulate async feel; real backend would be await fetch
    await new Promise((r) => setTimeout(r, 150))
    runDraw(draw.value.id)
        .then(() => router.push('/'))
  } catch (e: any) {
    error.value = e?.message || 'Błąd losowania'
  } finally {
    running.value = false
  }
}

function back() {
  router.push({name: 'draws'})
}

onMounted(() => {
  isLoading.value = true
  error.value = null
  getDraw(id.value)
      .then((response) => {
        draw.value = {
          createdAt: response.createdAt,
          finishedAt: response.finishedAt,
          id: response.id,
          name: response.name,
          options: response.options,
          result: response.result,
          resultsCount: response.resultsCount,
          status: response.status
        }
      })
      .catch((e: any) => {
        error.value = e?.message || 'Nie udało się pobrać losowania'
      })
      .finally(() => {
        isLoading.value = false
      })
})
</script>

<template>
  <main class="container" :aria-busy="isLoading ? 'true' : 'false'">
    <button class="link" type="button" @click="back">← Powrót</button>

    <div v-if="isLoading" class="loading" aria-live="polite">
      Ładowanie…
    </div>

    <p v-else-if="error" class="error">{{ error }}</p>

    <div v-else-if="!draw" class="empty">
      Nie znaleziono losowania.
    </div>

    <div v-else class="card">
      <header class="head">
        <h1 class="title">{{ draw.name }}</h1>
        <span class="status" :class="draw.status === 'FINISHED' ? 'done' : 'in'">
          {{ draw.status === 'FINISHED' ? 'Zakończone' : 'W trakcie' }}
        </span>
      </header>

      <div class="meta">Opcje: {{ draw.options.length }} • Wyników do wylosowania: {{ draw.resultsCount }}</div>

      <section class="section">
        <h2>Opcje</h2>
        <ul class="options">
          <li v-for="o in draw.options" :key="o.id">{{ o.content }} — <em>{{ o.author }}</em></li>
        </ul>
      </section>

      <section class="section">
        <h2>Losowanie</h2>
        <div v-if="draw.status === 'IN_PROGRESS'">
          <button class="btn primary" :disabled="running" @click="run">{{
              running ? 'Losowanie…' : 'Uruchom losowanie'
            }}
          </button>
        </div>
        <div v-else>
          <p><strong>Wylosowane:</strong></p>
          <ul class="winners">
            <li v-for="w in draw?.result?.winners" :key="w.id">{{ w.content }} — <em>{{ w.author }}</em></li>
          </ul>
        </div>
        <p v-if="error" class="error">{{ error }}</p>
      </section>
    </div>
  </main>
</template>

<style scoped>
.container {
  max-width: 900px;
  margin: 0 auto;
  padding: 1rem;
}

.link {
  background: transparent;
  border: none;
  color: #2b7cff;
  cursor: pointer;
  margin-bottom: 0.5rem;
}

.card {
  border: 1px solid var(--color-border);
  border-radius: 8px;
  padding: 0.75rem;
}

.head {
  display: flex;
  align-items: baseline;
  justify-content: space-between;
  gap: 1rem;
}

.title {
  margin: 0;
}

.status {
  font-size: 0.85rem;
  padding: 0.25rem 0.5rem;
  border-radius: 999px;
  border: 1px solid var(--color-border);
}

.status.done {
  background: #1e7e34;
  color: #fff;
  border-color: #1e7e34;
}

.status.in {
  background: #f1f1f1;
  color: #333;
}

.meta {
  color: var(--vt-c-text-light-2);
  font-size: 0.9rem;
  margin-top: 0.25rem;
}

.section {
  margin-top: 1rem;
}

.options, .winners {
  margin: 0.25rem 0 0;
  padding-left: 1rem;
}

.btn {
  padding: 0.5rem 0.75rem;
  border: 1px solid var(--color-border);
  border-radius: 6px;
  background: transparent;
  color: inherit;
  cursor: pointer;
}

.btn.primary {
  background: #2b7cff;
  color: #fff;
  border-color: #2b7cff;
}

.error {
  color: #c62828;
}

.empty {
  color: var(--vt-c-text-light-2);
}

.loading {
  color: var(--vt-c-text-light-2);
}
</style>
