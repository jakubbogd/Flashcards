<template>
  <div class="container flex">
    <GoToMain/>
    <h1 class="title">📚 Nauka fiszek</h1>
    <button class="btn blue-btn" @click="goToCreate">
      Przejdź do tworzenia fiszek
    </button>
    <div class="mode-select flex">
      <button
        v-for="mode in modes"
        :key="mode.key"
        :class="{ active: currentMode === mode.key, 'light-grey':  currentMode !== mode.key }"
        class="btn mode-btn"
        @click="currentMode = mode.key; current=0"
      >
        {{ mode.label }}
      </button>
    </div>

    <button
      class="btn"
      :class='{ "blue-btn": showAll, "light-grey": !showAll }'
      @click="showAll = !showAll"
    >
      {{ showAll ? 'Pokaż tylko nienauczone' : 'Pokaż wszystkie fiszki' }}
    </button>


    <FlashcardsProgress v-if="filteredFlashcards.length" :current="current + 1" :total="filteredFlashcards.length" />

    <component
      v-if="filteredFlashcards.length"
      :is="currentComponent"
      :flashcards="filteredFlashcards"
      :current="current"
      :easyCounts="easyCounts"
      @update:current="current = $event"
      @markEasy="handleMarkEasy"
      @markHard="handleMarkHard"
      @openEndModal="handleEndofCycle"
      :key="`${currentMode}-${current}`"
    />
    <Lack v-else />
    
    <EndCycleModal v-if="showEndModal" 
      :easyCount="easyCount" 
      :learnedCards="learnedCards" 
      @restartCycle="restartCycle" 
      @endCycle="endCycle"/>


  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import LearnFlip from './Subcomponents/LearnFlip.vue'
import LearnMultipleChoice from './Subcomponents/LearnMultipleChoice.vue'
import LearnWrite from './Subcomponents/LearnWrite.vue'
import FlashcardsProgress from './Subcomponents/FlashcardsProgress.vue'
import EndCycleModal from './Subcomponents/EndCycleModal.vue'
import Lack from './Subcomponents/Lack.vue'
import GoToMain from './Subcomponents/GoToMain.vue'
import { flashcardService } from '@/api/flashcardService'
import { goTo } from '@/helpers/helpers'


const flashcards = ref([])
const currentMode = ref('flip')
const current = ref(0)
const showEndModal = ref(false)
const showAll = ref(false)
const easyCounts = ref({})
const selectedSetId = ref('')
const easyCount = ref(0)
const learnedCards = ref([])

onMounted(() =>{
  const params = new URLSearchParams(window.location.search)
  selectedSetId.value = params.get('chosen')
  loadFlashcards()
})


const goToCreate = () => {
  window.location.href = `/create?chosen=${selectedSetId.value}`
}


const modes = [
  { key: 'flip', label: 'Flip' },
  { key: 'multiple', label: 'Test wyboru' },
  { key: 'write', label: 'Wpisz odpowiedź' }
]

const currentComponent = computed(() => {
  switch (currentMode.value) {
    case 'flip': return LearnFlip
    case 'multiple': return LearnMultipleChoice
    case 'write': return LearnWrite
    default: return LearnFlip
  }
})


const handleMarkEasy = (cardId) => {
  easyCounts.value[cardId] = 1 
}
const handleMarkHard = (cardId) => {
  easyCounts.value[cardId] = 0 
}

const handleEndofCycle = (count,keys) => {
  showEndModal.value = true
  learnedCards.value = flashcards.value.filter(card => keys.map(Number).includes(card.id))
  easyCount.value = count
}

const filteredFlashcards = computed(() => {
  return showAll.value ? flashcards.value : flashcards.value.filter(card => !card.learned)
})

const restartCycle = () => {
  for (const cardId in easyCounts.value) {
    if (easyCounts.value[cardId] > 0) {
      handleMarkLearned(cardId)
    }
  }
  endCycle()
}

const endCycle = () => {
  showEndModal.value = false
  current.value = 0
  easyCounts.value = {}
}

const loadFlashcards = async () => {
  try {
    flashcards.value = await flashcardService.getFlashcards(selectedSetId.value)
  } catch (e) {
    console.error('Błąd podczas ładowania fiszek:', e)
  }
}

const handleMarkLearned = async (cardId) => {
  try {
    await flashcardService.markLearn(cardId)
    const card = flashcards.value.find(c => c.id === cardId)
    if (card) card.learned = true
    loadFlashcards()
  } catch (error) {
    console.error('Błąd podczas oznaczania jako nauczone:', error)
  }
}
</script>

<style scoped>
.title {
  text-align: center;
  margin-bottom: 20px;
  color: #1f2937;
}



/* Responsywność */
@media (max-width: 768px) {

  .mode-select {
    gap: 10px;
  }

  .title {
    font-size: 1.5rem;
  }
}

@media (max-width: 480px) {
  .mode-select {
    flex-direction: column;
    align-items: center;
  }

}

</style>
