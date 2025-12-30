<template>
  <div class="container flex">
    <GoToMain />
    <h1>Wybierz zestawy do efektywnej nauki</h1>

    <section class="con-card">
      <h2>Zestawy</h2>
      <label v-for="set in sets" :key="set.id">
        <input type="checkbox" v-model="selected" :value="set.id" />
        <span>{{ set.name }}</span>
            <span class="count">
              ({{ set.flashcards_count }} fiszek)
            </span>
      </label>
      <div class="form-group">
        <label>Ile fiszek chcesz przerobić?</label>
        <input
          type="number"
          v-model.number="count"
          min="1"
          max="100"
          class="input-field"
        />
      </div>
      <button class="blue-btn btn" @click="start">Start</button>
    </section>

    
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import GoToMain from './Subcomponents/GoToMain.vue'
import { smartLearnService } from '@/api/smartLearnService'
import { hierarchyService } from '@/api/hierarchyService'
import { goTo } from '@/helpers/helpers'

const sets = ref([])
const selected = ref([])
const count = ref(10)

onMounted(async () => {
  sets.value = await hierarchyService.getSets()
})

const start = async () => {
  const uuid = await smartLearnService.startSession(selected.value, count.value)
  window.location.href = `/smart-learn/${uuid.uuid}`
}
</script>
