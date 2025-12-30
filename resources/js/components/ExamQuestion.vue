<template>
  <div class="con-card">
    <h2>{{ question.flashcard.question }}</h2>

    <div v-if="mode==='multiple_choice'" class="options grid">
      <button
        v-for="opt in question.flashcard.options"
        :key="opt.id"
        class="blue-btn btn"
        @click="answer(opt.is_correct)"
        
      >
        {{ opt.text }}
      </button>
    </div>

    <div v-else class="input-container">
      <input 
        v-model.trim="userInput"
        class="input-field blue-border-input" 
        placeholder="Twoja odpowiedź..." 
        @keyup.enter="handleTextSubmit"
        ref="answerInput"
      />
      <button class="blue-btn btn" :disabled="!userInput" @click="handleTextSubmit">
        Odpowiedz
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
  question: { type: Object, required: true },
  mode: { type: String, default: 'multiple_choice' }
})

const emit = defineEmits(['answered'])

const userInput = ref('')
const answerInput = ref(null)

const answer = (isCorrect) => {
  emit('answered', isCorrect)
}

onMounted(() => {
  if (answerInput.value) answerInput.value.focus()
})

const handleTextSubmit = () => {
  const correct =
    userInput.value.trim().toLowerCase() ===
    props.question.flashcard.answer.toLowerCase()

  emit('answered', correct)
  userInput.value=''
}
</script>