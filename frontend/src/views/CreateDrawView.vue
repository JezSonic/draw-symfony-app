<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import {createDraw} from "@/api/draws.ts";

const router = useRouter()

const name = ref('')
const resultsCount = ref<number | null>(1)
type Opt = { content: string; author: string }
const options = ref<Opt[]>([{ content: '', author: '' }])
const error = ref<string | null>(null)
const submitting = ref(false)

function addOption() {
  options.value.push({ content: '', author: '' })
}

function removeOption(idx: number) {
  options.value.splice(idx, 1)
}

function canSubmit() {
  const clean = options.value.filter((o) => o.content.trim() && o.author.trim())
  const rc = Number(resultsCount.value)
  return name.value.trim() && rc > 0 && clean.length >= rc
}

async function submit() {
  error.value = null
  submitting.value = true
  try {
    const draw = await createDraw({
      name: name.value,
      resultsCount: Number(resultsCount.value ?? 0),
      options: options.value.map((o) => ({ content: o.content, author: o.author })),
    })
    await router.push({name: 'draw-detail', params: {id: draw.id}})
  } catch (e: any) {
    error.value = e?.message || 'Nie udało się utworzyć losowania'
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <main class="container">
    <h1>Utwórz losowanie</h1>

    <form class="form" @submit.prevent="submit" :aria-busy="submitting ? 'true' : 'false'">
      <div class="field">
        <label for="name">Nazwa</label>
        <input id="name" v-model="name" type="text" placeholder="Np. Losowanie nagród" required :disabled="submitting" />
      </div>

      <div class="field">
        <label for="rc">Liczba wyników do wylosowania</label>
        <input id="rc" v-model.number="resultsCount" type="number" min="1" required :disabled="submitting" />
      </div>

      <div class="options">
        <div class="options-head">
          <h2>Opcje</h2>
          <button class="btn" type="button" @click="addOption" :disabled="submitting">+ Dodaj opcję</button>
        </div>
        <div v-if="options.length === 0" class="hint">Dodaj co najmniej jedną opcję.</div>
        <ul class="option-list">
          <li v-for="(o, idx) in options" :key="idx" class="option-row">
            <input v-model="o.content" type="text" placeholder="Treść/opis" :disabled="submitting" />
            <input v-model="o.author" type="text" placeholder="Autor (osoba zgłaszająca)" :disabled="submitting" />
            <button class="icon" type="button" aria-label="Usuń" @click="removeOption(idx)" :disabled="submitting">✕</button>
          </li>
        </ul>
      </div>

      <p v-if="error" class="error">{{ error }}</p>

      <div class="actions">
        <button class="btn primary" type="submit" :disabled="submitting || !canSubmit()">{{ submitting ? 'Zapisywanie…' : 'Zapisz' }}</button>
      </div>
    </form>
  </main>
</template>

<style scoped>
.container { max-width: 900px; margin: 0 auto; padding: 1rem; }
.form { display: grid; gap: 1rem; }
.field { display: grid; gap: 0.25rem; }
label { font-weight: 600; }
input[type="text"], input[type="number"] { padding: 0.5rem; border: 1px solid var(--color-border); border-radius: 6px; width: 100%; }
.options { border-top: 1px solid var(--color-border); padding-top: 0.5rem; }
.options-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem; }
.option-list { list-style: none; padding: 0; display: grid; gap: 0.5rem; }
.option-row { display: grid; grid-template-columns: 1fr 1fr auto; gap: 0.5rem; align-items: center; }
.btn { padding: 0.5rem 0.75rem; border: 1px solid var(--color-border); border-radius: 6px; background: transparent; color: inherit; cursor: pointer; }
.btn.primary { background: #2b7cff; color: #fff; border-color: #2b7cff; }
.btn.primary:disabled { opacity: 0.5; cursor: not-allowed; }
.icon { border: 1px solid var(--color-border); background: transparent; border-radius: 6px; padding: 0.4rem 0.6rem; cursor: pointer; }
.hint { color: var(--vt-c-text-light-2); }
.error { color: #c62828; }
.actions { display: flex; justify-content: flex-end; }
@media (max-width: 640px) { .option-row { grid-template-columns: 1fr; } }
</style>
