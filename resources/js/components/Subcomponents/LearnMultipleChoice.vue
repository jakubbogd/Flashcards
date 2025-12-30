<template>
  <div class="learn-container">
    <div class="question">
      {{ currentCard?.question }}
    </div>

    <div class="options flex">
      <button
        v-for="(option, i) in currentCard.options"
        :key="option.text + '-' + i"
        :class="{
          'green-btn correct': option.text === currentCard.answer && answered,
          'red-btn wrong': option.text !== currentCard.answer && selectedOption && selectedOption.text === option.text && answered
        }"
        class="option-btn btn"
        @click="selectOption(option)"
        :disabled="answered"
      >
        {{ option.text }}
      </button>
    </div>

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

const emits = defineEmits(['update:current', 'markEasy', 'markHard','openEndModal'])

const answered = ref(false)
const selectedOption = ref(null)

const currentCard = computed(() => {
  return props.flashcards.length > props.current ? props.flashcards[props.current] : null
})

const selectOption = (option) => {
  if (!currentCard.value || answered.value) return
  selectedOption.value = option
  answered.value = true
  const cardId = currentCard.value.id
  if (option.text === currentCard.value.answer && answered.value) {
    emits('markEasy',cardId)
  } else if (option.text !== currentCard.value.answer && answered.value) {
    emits('markHard',cardId)
  }
}


const nextCard = () => {
  if (!props.flashcards.length) return
  setTimeout(() => {
    const newCurrent = (props.current + 1) % props.flashcards.length
    if (newCurrent === 0) {
      emits('openEndModal',Object.values(props.easyCounts).filter(v => v > 0).length,Object.keys(props.easyCounts).filter(key => props.easyCounts[key] > 0)) 
    } else {
      emits('update:current', newCurrent)
    }
    answered.value = false
  }, 300)
}
</script>
