<template>
  <div class="container flex">
    <GoToMain/>
    <h1>Tworzenie fiszek do zestawu {{ set.name }}</h1>
    
    <button class="btn blue-btn" @click="goTo(`learn?chosen=${set.value.id}`)">
      Przejdź do nauki
    </button>
 
    <CSVUpload :set="set"/>

    <Form @add="add">
      <textarea v-model="newQuestion" placeholder="Pytanie" class="blue-border-input" ref="newQuestionEl" />
      <textarea v-model="newAnswer" placeholder="Odpowiedź" class="blue-border-input" ref="newAnswerEl" />
      <textarea v-model="newNotes" placeholder="Dodatkowe notatki" class="blue-border-input" ref="newNotesEl"/>
      <input type="file" @change="onFileChange" accept="image/*" />
      <button type="submit" class="btn blue-btn" :disabled="loading">
        <span>➕</span> Dodaj
      </button>
    </Form>

    <p v-if="loading" class="loading-text light-grey">
      ⏳ Proszę czekać, AI generuje odpowiedzi...
    </p>

    <Lack v-if="flashcards.length === 0" />

    <Toast :showToast="showToast" />
    <FlashcardsList :flashcards="flashcards"/>
   
    <transition name="fade">
      <div v-if="previewImage" class="lightbox" @click="closeImage">
        <img
          :src="`/storage/${previewImage}`"
          class="lightbox-image"
          @click.stop
        />
        <CloseX/>
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { hierarchyService } from '@/api/hierarchyService'
import { flashcardService } from '@/api/flashcardService'
import CSVUpload from './Subcomponents/CSVUpload.vue'
import Toast from './Subcomponents/Toast.vue'
import Form from './Subcomponents/Form.vue'
import Lack from './Subcomponents/Lack.vue'
import CloseX from './Subcomponents/CloseX.vue'
import GoToMain from './Subcomponents/GoToMain.vue'
import FlashcardsList from './Subcomponents/FlashcardsList.vue'
import { goTo } from '@/helpers/helpers'

const set = ref([])
const selectedSetId = ref(0)
const flashcards = ref([])
const newQuestion = ref('')
const newAnswer = ref('')
const newNotes = ref('')
const showToast = ref(false)
const loading = ref(false)
const newQuestionEl = ref(null)
const newAnswerEl = ref(null)
const newNotesEl= ref(null)
const newImage = ref(null)
const previewImage = ref(null)

const closeImage = () => {
  previewImage.value = null
  document.body.style.overflow = ''
}


onMounted(async () =>{
  const params = new URLSearchParams(window.location.search)
  selectedSetId.value = params.get('chosen')
  document.querySelectorAll('textarea').forEach(textarea => autoGrowTextarea(textarea));
  autoGrowTextarea(newQuestionEl.value)
  autoGrowTextarea(newAnswerEl.value)
  autoGrowTextarea(newNotesEl.value)
  window.addEventListener('keydown', e => {if (e.key === 'Escape') closeImage()})
  try {
    set.value = await hierarchyService.getSets()
    set.value = set.value.filter(set => set.id == selectedSetId.value)[0]
    flashcards.value = await flashcardService.getFlashcards(selectedSetId.value)    
  } catch (error) {
    console.error(error)
  }
})

const onFileChange = (e) => {
  newImage.value = e.target.files[0]
}

const add = async () => {
  if (!newQuestion.value.trim() || !newAnswer.value.trim()) return
  loading.value = true
  try {
    const res = await flashcardService.addFlashcards(selectedSetId.value, newQuestion.value, newAnswer.value, newNotes.value, selectedSetId.value, newImage.value)
    flashcards.value.unshift(res)
  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
    newQuestion.value = ''
    newAnswer.value = ''
    newNotes.value = ''
    newImage.value =null
    autoGrowTextarea(newQuestionEl.value)
    autoGrowTextarea(newAnswerEl.value)
    autoGrowTextarea(newNotesEl.value)
  }
}


const autoGrowTextarea = (el) => {
  if (!el) return
  el.style.height = 'auto'
  el.style.height = el.scrollHeight + 'px'
}


</script>

<style scoped>
/* tło */
.lightbox {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.75);
  backdrop-filter: blur(4px);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
}

/* powiększony obraz */
.lightbox-image {
  max-width: 90vw;
  max-height: 90vh;
  border-radius: 16px;
  box-shadow: 0 25px 60px rgba(0,0,0,0.4);
  animation: zoomIn 0.25s ease;
  cursor: zoom-out;
}


/* animacje */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

@keyframes zoomIn {
  from {
    transform: scale(0.85);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}

.loading-text {
  font-style: italic;
  margin-top: 10px;
}


</style>
