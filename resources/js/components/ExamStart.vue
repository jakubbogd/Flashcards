<template>
  <div class="container flex">
    <GoToMain />

    <h1>📝 Egzamin</h1>

    <div class="con-card">
      <section class="exam-section">
        <h2>📁 Foldery</h2>

        <div
          v-for="folder in folders"
          :key="folder.id"
          class="folder-block"
        >
          <h3 class="folder-title">{{ folder.name }}</h3>

          <label
            v-for="set in folder.sets"
            :key="set.id"
            class="exam-option"
          >
            <input
              type="checkbox"
              v-model="form.set_ids"
              :value="set.id"
              :disabled="set.flashcards_count === 0"
            />
            <span>{{ set.name }}</span>
            <span class="count">
              ({{ set.flashcards_count }} fiszek)
            </span>
          </label>
        </div>
      </section>

      <section class="exam-section">
        <h2>🎯 Poziom trudności</h2>

        <div class="mode-select flex">
          <button
            v-for="level in difficulties"
            :key="level.value"
            type="button"
            class="mode-btn btn difficulty-option"
            :class="{ active: form.difficulty === level.value }"
            @click="form.difficulty = level.value" aria-pressed
          >
            <span class="icon">{{ level.icon }}</span>
            <span class="label">{{ level.label }}</span>
          </button>
        </div>
      </section>

      <button
        class="btn blue-btn"
        :disabled="loading || form.set_ids.length === 0"
        @click="startExam"
      >
        🚀 Rozpocznij egzamin
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import GoToMain from './Subcomponents/GoToMain.vue'
import { examService } from '@/api/examService'
import { hierarchyService } from '@/api/hierarchyService'
import { goTo } from '@/helpers/helpers'


const folders = ref([])
const loading = ref(false)

const difficulties = [
  { value: 'easy', label: 'Łatwy', icon: '😊' },
  { value: 'normal', label: 'Normalny', icon: '😐' },
  { value: 'hard', label: 'Trudny', icon: '🔥' }
]
const form = reactive({
  set_ids: [],
  difficulty: 'normal',
})

onMounted(async () => {
  folders.value = await hierarchyService.getFolders()
})

const startExam = async () => {
  if (form.set_ids.length === 0) {
    return
  }

  loading.value = true
  try {
    const uuid = await examService.startExam(form)
    goTo(`exam/${uuid.uuid}`)
  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }
}
</script>


<style scoped>
.difficulty-option {
  flex: 1;
  border-radius: 18px;
  transition: all 0.25s ease;
  display: flex;
  flex-direction: column;
  gap: 6px;
  font-weight: 600;
}

.difficulty-option .icon {
  font-size: 1.6rem;
}

.difficulty-option .label {
  font-size: 0.95rem;
}


</style>