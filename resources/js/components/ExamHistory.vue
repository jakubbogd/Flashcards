<template>
  <div class="container flex">
    <GoToMain />
    <h1>📜 Historia egzaminów</h1>

    <p v-if="loading">⏳ Ładowanie...</p>
    <p v-else-if="!exams.length">Brak ukończonych egzaminów</p>

    <div v-else class="exam-list grid">
      <article
        v-for="exam in exams"
        :key="exam.uuid"
        class="con-card"
      >
        <div class="row">
          <span>📝 Data</span>
          <strong>{{ formatDate(exam.finished_at) }}</strong>
        </div>

        <div class="row">
          <span>📚 Zestawy</span>
          <strong>{{ exam.sets }}</strong>
        </div>

        <div class="row">
          <span>📊 Wynik</span>
          <strong>
            {{ exam.score }}/{{ exam.total }} ({{ exam.percent }}%)
          </strong>
        </div>

        <div class="row">
          <span>🎯 Poziom</span>
          <strong>{{ MapLvl(exam.difficulty) }}</strong>
        </div>
      </article>
    </div>
  </div>
</template>


<script setup>
import { ref, onMounted } from 'vue'
import GoToMain from './Subcomponents/GoToMain.vue'
import { examService } from '@/api/examService'

const exams = ref([])
const loading = ref(true)

const MapLvl = lvl => {
  if (lvl === 'easy') return 'łatwy'
  if (lvl === 'normal') return 'normalny'
  if (lvl === 'hard') return 'trudny'
}

const formatDate = (date) =>
  new Intl.DateTimeFormat('pl-PL', {
    dateStyle: 'medium',
    timeStyle: 'short'
  }).format(new Date(date))

onMounted(async () => {
    exams.value= await examService.getHistory()
    loading.value = false
  }
)
</script>

<style scoped>
.exam-list {
  gap: 1rem;
}

.row {
  display: flex;
  justify-content: space-between;
  margin-bottom: .5rem;
}

.row strong {
  margin-left: 35px;
}

</style>