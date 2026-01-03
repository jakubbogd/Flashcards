<template>
  <div v-if="exam" class="container flex">
    <GoToMain />
    <div class="exam-header">
      <h2>Pytanie {{ index + 1 }} / {{ Math.min(exam.total,exam.questions.length) }}</h2>
    </div>
    <div class="exam-timer" :class="timerClass">
      <span class="timer-icon">⏱️</span>
      <span class="timer-time">
        {{ Math.floor(timeLeft / 60) }}:{{ (timeLeft % 60).toString().padStart(2, '0') }}
      </span>
    </div>


    <ExamQuestion
      :question="exam.questions[index]" :mode="mode"
      @answered="submitAnswer"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import ExamQuestion from './ExamQuestion.vue'
import GoToMain from './Subcomponents/GoToMain.vue'
import { examService } from '@/api/examService'
import { goTo,rand } from '@/helpers/helpers'

const exam = ref(null)
const index = ref(0)
const mode=ref(null)
const timeLeft = ref(0)
let timer = null

const uuid = window.location.pathname.split('/').pop()

onMounted(async () => {
  exam.value = await examService.getExam(uuid)
  if (exam.value.difficulty === 'hard') mode.value='write'
  else if (exam.value.difficulty === 'normal') mode.value = rand()
  else mode.value = 'multiple_choice'
  if (!exam.value.finished_at) {
      timeLeft.value = exam.value.time_limit
      timer = setInterval(() => {
        timeLeft.value--
        if (timeLeft.value <= 0) {
          clearInterval(timer)
          goTo(`exam/${uuid}/result`)
        }
      }, 1000)
  }

})

const submitAnswer = async (isCorrect) => {
  const order = exam.value.questions[index.value].order

  await examService.submitAnswer(uuid,order,isCorrect)

  if (index.value + 1 < exam.value.questions.length) {
    index.value++
  } else {
    goTo(`exam/${uuid}/result`)
  }
  
  if (exam.value.difficulty === 'normal') {
    mode.value = rand()
  }
}

const timerClass = computed(() => {
  if (timeLeft.value <= 10) return 'danger'
  if (timeLeft.value <= 30) return 'warning'
  return 'normal'
})



</script>

<style scoped>
  .exam-header {
  text-align: center;
  margin-bottom: 24px;
  margin-top: 20px;
}

.exam-timer {
  position: fixed;
  top: 20px;
  right: 20px;

  display: flex;
  align-items: center;
  gap: 10px;

  padding: 12px 20px;
  border-radius: 16px;

  font-size: 1.2rem;
  font-weight: 700;
  letter-spacing: 0.05em;

  background: #f9fafb;
  color: #111827;

  box-shadow:
    0 10px 25px rgba(0, 0, 0, 0.1);

  transition: all 0.3s ease;
  z-index: 100;
}

.timer-icon {
  font-size: 1.4rem;
}

.exam-timer.normal {
  background: #eef2ff;
  color: #4338ca;
}

.exam-timer.warning {
  background: #fef3c7;
  color: #b45309;
  animation: pulse 1.2s infinite;
}

.exam-timer.danger {
  background: #fee2e2;
  color: #b91c1c;
  animation: pulse-strong 0.8s infinite;
}

/* subtelne pulsowanie */
@keyframes pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.03); }
  100% { transform: scale(1); }
}

@keyframes pulse-strong {
  0% { transform: scale(1); }
  50% { transform: scale(1.07); }
  100% { transform: scale(1); }
}

/* mobile */
@media (max-width: 768px) {
  .exam-timer {
    top: auto;
    bottom: 20px;
    right: 50%;
    transform: translateX(50%);
  }
}

</style>