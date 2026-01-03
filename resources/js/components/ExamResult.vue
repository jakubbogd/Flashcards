<template>
  <div v-if="exam" class="container flex">
    <div class="con-card">
      <h1>📊 Egzamiz zakończony</h1>

      <p class="score">
    Wynik: <strong>{{ exam.score }} / {{ Math.min(exam.total,exam.questions.length) }}</strong>
  </p>

  <p class="percent">
    {{ exam.percent }}%
  </p>

  <Toast :showToast="showToast" :text="exam.message" />
    <p>🔥 Streak: {{ exam.streak }} dni</p>

  <button class="btn blue-btn" @click="goTo('exams')">
    📜 Historia egzaminów
    </button>
    <button class="blue-btn btn" @click="goTo()">🏠 Menu</button>


      <div
        v-for="q in exam.questions"
        :key="q.id"
        class="question-review"
        :class="{
          right: q.is_correct,
          wrongquestion: q.is_correct === false
        }"
      >
        <p>{{ q.flashcard.question }}</p>

        <ul>
          <li
            v-for="opt in q.flashcard.options"
            :key="opt.id"
            :class="{
              right: opt.is_correct,
              wronganswer: !opt.is_correct,
              selected: opt.id === q.selected_option_id
            }"
          >
            {{ opt.text }}
          </li>
        </ul>
      </div>

      
    </div>
  </div>
</template>


<script setup>
import { ref, onMounted } from 'vue'
import Toast from './Subcomponents/Toast.vue'
import { examService } from '@/api/examService'
import { goTo } from '@/helpers/helpers'


const exam = ref(null)
const uuid = window.location.pathname.split('/')[2]
const showToast = ref(false)

onMounted(async () => {
  exam.value = await examService.getExam(uuid)
  showToast.value = true
  setTimeout(() => showToast.value = false, 5000)
})

</script>

<style scoped>

.score {
  font-size: 1.2rem;
  margin-bottom: 28px;
  color: #374151;
}

.score strong {
  font-size: 2.4rem;
  color: #4f46e5;
}

@media (max-width: 480px) {

  .score strong {
    font-size: 2rem;
  }
}


.question-review.right {
  border-left: 6px solid #059669;
  background: #ecfdf5;
}

.question-review.wrongquestion {
  border-left: 6px solid #dc2626;
  background: #fef2f2;
}

li.right {
  color: #059669;
  font-weight: 600;
}

li.wronganswer {
  color: #dc2626;
}

</style>