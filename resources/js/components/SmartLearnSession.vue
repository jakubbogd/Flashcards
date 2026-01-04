<template>
  <div class="container flex">
    <GoToMain />
    <div class="con-card">
    
    <div  v-if="current">
    <h2>{{ current.flashcard.question }}</h2>

    <div v-if="mode==='multiple_choice'">
      <div class="options flex">
        <button
        v-for="opt in current.flashcard.options"
        :key="opt.id"
        class="option-btn btn option"
       

        :class="{
          'green-btn correct': opt.text === current.flashcard.answer  && selectedOptionId,
          'red-btn wrong': opt.text !== current.flashcard.answer && selectedOptionId && selectedOptionId === opt.id
        }"


        @click="answer(opt.text, opt.id)"
      >
        {{ opt.text }}
      </button>
      </div>
    </div>
    <div v-else>
      <input class="input-field blue-border-input" v-model="text" placeholder="Twoja odpowiedź..." @keyup.enter="answer(text, 0)"/>
      <button class="blue-btn btn" @click="answer(text, 0)">Odpowiedz</button>
      <div v-if="feedbackClass==='wrong'">
        Poprawna odpowiedź to: {{ current.flashcard.answer }}
      </div>
    </div>
    

    <p>{{ index }} / {{ total }}</p>
     </div>

  <div v-else-if="session" class="session-end">
    <h2>🎉 Sesja zakończona!</h2>
    <p class="summary">
      Poprawne odpowiedzi: {{ correctCount }} / {{ total }} ({{ correctPercent }}%)
    </p>

    <div class="results">
      <div
        v-for="(q,i) in session.questions"
        :key="q.id"
        class="result-item"
      >
  
        <h3>{{ q.flashcard.question }}</h3>
        <div :class="{
              'correct-answer': q.is_correct,
              'wrong-answer': !q.is_correct
            }">
              <span v-if="q.is_correct" class="correct-notification">🎉 Twoja odpowiedź była poprawna!</span>
              <span v-else class="wrong-notification">❌ Niestety, twoja odpowiedź nie była poprawna</span>
            </div>
        <ul v-if="odps[i][1] === 'multiple_choice'">
          <li
            v-for="opt in q.flashcard.options"
            :key="opt.id"
            :class="{
              correct: opt.is_correct,
             
            }"
          >
            {{ opt.text }}
          </li>
        </ul>
        <div v-else class="correct test">
          {{ q.flashcard.options.filter(el => el.is_correct)[0].text }}
        </div>
      </div>
    </div>
  </div>
  </div>
  <Toast :showToast="showToast" :text="toastText" :theme="toastTheme"/>

  </div>

</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import Toast from './Subcomponents/Toast.vue'
import GoToMain from './Subcomponents/GoToMain.vue'
import { smartLearnService } from '@/api/smartLearnService'
import { rand } from '@/helpers/helpers'

const uuid = window.location.pathname.split('/').pop()

const session = ref(null)
const current = ref(null)
const index = ref(1)
const text = ref('')
const total = ref(0)
const correctCount=ref(0)
const selectedOptionId = ref(null)
const feedbackClass = ref('')
const odps=ref([])
const toastText = ref('')
const toastTheme = ref(null)
const showToast = ref(false)
let mode = 'multiple_choice'

const load = async () => {
  session.value = await smartLearnService.getSession(uuid)
  total.value = session.value.total
  console.log(session.value.questions)
  current.value = session.value.questions.find(q => !q.answered_at) || null
  mode = rand()
  selectedOptionId.value = null
  feedbackClass.value = ''
  text.value=''
}

const answer = async (optionText, optionId) => {
  selectedOptionId.value = optionId

  const answer = await smartLearnService.postAnswer(uuid,current.value.order,optionText,optionId,mode)

  feedbackClass.value = answer.is_correct ? 'correct' : 'wrong'
  if (answer.is_correct) correctCount.value++
  odps.value.push([answer.is_correct,mode])

  toastText.value = answer.message
  toastTheme.value = answer.is_correct
  showToast.value = true
  
  setTimeout(() => showToast.value = false, 1000)

  if (answer.is_correct) {
    setTimeout(async () => {
      index.value++
      await load()
    }, 1000)
  } else {
    setTimeout(async () => {
      index.value++
      await load()
    }, 5000)
  }
  
}


const correctPercent = computed(() => {
  if (!session.value) return 0
  return Math.round((correctCount.value / total.value) * 100)
})

onMounted(load)
</script>

<style scoped>
.test {
  border-radius: 12px;
  padding: 16px 20px;
}

.option {
  padding: 16px 20px;
  font-size: 1.1rem;
  justify-content: center;
}

.option.correct:hover{
  background: #10b981;
}

.option.wrong:hover{
  background: #ef4444;
}

.option.correct {
  background: #10b981;
  color: white;
  border-color: #059669;
  animation: correctPulse 0.4s ease;
}

.option.wrong {
  background: #ef4444;
  color: white;
  border-color: #b91c1c;
  animation: wrongShake 0.4s ease;
}

.correct-answer {
  color: #10b981;
  font-weight: 600;
  font-size: 1rem;
}

.wrong-answer {
  color: #ef4444;
  font-weight: 600;
  font-size: 1rem;
}

.correct-notification {
  color: #10b981;
  font-weight: 600;
}

.wrong-notification {
  color: #ef4444;
  font-weight: 600;
}

.session-end {
  text-align: center;
  padding: 20px;
}

.summary {
  font-size: 1.2rem;
  margin-bottom: 30px;
  font-weight: 600;
}

.results {
  display: flex;
  flex-direction: column;
  gap: 20px;
  text-align: left;
}

.result-item h3 {
  font-weight: 500;
  margin-bottom: 8px;
}

.result-item ul {
  list-style: none;
  padding-left: 0;
}

.result-item li {
  padding: 8px 12px;
  border-radius: 10px;
  margin-bottom: 4px;
  border: 1px solid #e5e7eb;
}

.result-item li.correct {
  background-color: #10b981;
  color: white;
  border-color: #059669;
}

.result-item li.wrong {
  background-color: #ef4444;
  color: white;
  border-color: #dc2626;
}
</style>
