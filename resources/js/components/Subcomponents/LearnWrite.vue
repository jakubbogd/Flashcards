<template>
  <div class="learn-container grid">
    <div class="question">{{ currentCard.question }}</div>
  
    <input
      v-model="userAnswer"
      placeholder="Wpisz odpowiedź"
      class="input-field blue-border-input"
      @keyup.enter="checkAnswer"
      :disabled="answered"
    />
    <button
      class="btn blue-btn"
      @click="checkAnswer"
      :disabled="!userAnswer.trim() || answered"
    >
      Sprawdź
    </button>
    <transition name="slide">
      <div v-if="feedback" :class="{ correct: isCorrect, wrong: !isCorrect }" class="feedback">
        {{ feedback }}
      </div>
    </transition>
    <button v-if="answered" @click="nextCard" class="btn green-btn">Następna karta</button>
    
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  flashcards: {
    type: Array,
    required: true,
    validator: (value) => Array.isArray(value)
  },
  current: {
    type: Number,
    required: true
  },
  easyCounts: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['update:current', 'markEasy', 'markHard','openEndModal'])

const userAnswer = ref('')
const feedback = ref('')
const answered = ref(false)
const isCorrect = ref(false)

const currentCard = computed(() => {
  return props.flashcards.length > props.current ? props.flashcards[props.current] : null
})

const checkAnswer = () => {
  if (!userAnswer.value.trim() || answered.value || !currentCard.value) return
  answered.value = true
  isCorrect.value = userAnswer.value.trim().toLowerCase() === currentCard.value.answer.trim().toLowerCase()
  feedback.value = isCorrect.value ? '✅ Poprawnie!' : `❌ Błąd, prawidłowa odpowiedź: ${currentCard.value.answer}`
  if (isCorrect.value) {
    emit('markEasy',currentCard.value.id)
  } else {
    emit('markHard',currentCard.value.id)
  }
}

const nextCard = () => {
  if (!props.flashcards.length) return
  userAnswer.value = ''
  feedback.value = ''
  answered.value = false
  isCorrect.value = false

  setTimeout(() => {
    const newCurrent = (props.current + 1) % props.flashcards.length
    if (newCurrent === 0) {
      emit('openEndModal',Object.values(props.easyCounts).filter(v => v > 0).length,Object.keys(props.easyCounts).filter(key => props.easyCounts[key] > 0)) 
    } else {
      emit('update:current', newCurrent)
    }
    answered.value = false
  }, 300)
}
</script>

<style scoped>
.feedback {
  font-size: 1.1rem;
  font-weight: 600;
  text-align: center;
  padding: 12px 16px;
  border-radius: 12px;
  animation: feedbackFade 0.5s ease;
}

.correct {
  color: #059669;
  background: rgba(16, 185, 129, 0.1);
  border: 1px solid #10b981;
}

.wrong {
  color: #e11d48;
  background: rgba(244, 63, 94, 0.1);
  border: 1px solid #f43f5e;
}

@media (max-width: 480px) {


  .feedback {
    font-size: 1rem;
    padding: 10px 12px;
  }
}
</style>
