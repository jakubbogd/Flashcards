<template>
  <div class="container flex">
    <GoToMain/>
    <h1>Tworzenie fiszek do zestawu {{ set.name }}</h1>
    
    <button class="btn blue-btn" @click="goToLearn">
      Przejdź do uczenia się fiszek
    </button>
 
    <CSVUpload :set="set"/>

    <Form @add="add">
      <textarea v-model="newQuestion" placeholder="Pytanie" class="textarea-field blue-border-input" ref="newQuestionEl" />
      <textarea v-model="newAnswer" placeholder="Odpowiedź" class="textarea-field blue-border-input" ref="newAnswerEl" />
      <textarea v-model="newNotes" placeholder="Dodatkowe notatki" class="textarea-field blue-border-input" ref="newNotesEl"/>
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

    <transition-group name="slide" tag="div">
      <div v-for="card in flashcards" :key="card.id">
        <div class="item">
        <textarea class="textarea-field green-border-input"
          v-model="card.question"
          @input="autoGrow($event)" @blur="update(card)"
          :data-id="'q-' + card.id"
        />
        <textarea class="textarea-field green-border-input"
                  v-model="card.answer"
                  @input="autoGrow($event)" @blur="update(card)"
                  :data-id="'a-' + card.id"
        />
        <textarea
                v-model="card.notes"
                @input="autoGrow($event)"
                @blur="update(card)"
                placeholder="Dodatkowe notatki"
                class="textarea-field green-border-input"
              />

              <div class="image-section">
              <!-- PODGLĄD OBRAZU -->
              <img
                v-if="card.image_path && card.image_path !== '0'"
                :src="`/storage/${card.image_path}`"
                alt="Obraz fiszki"
                class="card-image clickable"
                @click="openImage(card.image_path)"
              />
              <!-- CUSTOM UPLOAD -->
              <label class="image-upload">
                <input
                  type="file"
                  accept="image/*"
                  hidden
                  @change="onUpdateChange($event, card)"
                />
                <span>📷 Zmień obraz</span>
              </label>
              <button v-if="card.image_path && card.image_path !== '0'" @click="removeImage(card)" class="delete-image-btn">
              🗑️ Usuń obraz
            </button>
            </div>

          <input type="checkbox" v-model="card.learned" @change="updateLearned(card)" />
          Nauczone
        <button class="delete" @click="openModal(card.id)">✕</button>
        <span v-if="savingId.value === card.id" class="saving">zapisywanie…</span>
        </div>
        <div class="wrong-answers" v-if="!card.question.startsWith('Czy ')">
          <p class="wrong-answers-title">Błędne odpowiedzi</p>

          <div
            v-for="option in card.options.filter(o => !o.is_correct)"
            :key="option.id"
            class="wrong-answer-row"
            
          >
            <textarea
              v-model="option.text"
              class="wrong-answer-input green-border-input"
              placeholder="Błędna odpowiedź"
              @blur="updateWrongAnswer(option)"
              @input="autoGrow($event)"
            />
            <span
              v-if="savingWrongId === option.id"
              class="saving-inline"
            >
              zapisywanie…
            </span>
          </div>
          <div v-if="card.options.length < 4" class="add-wrong-answer">
            <textarea
              v-model="newWrongAnswers[card.id]"
              class="wrong-answer-input green-border-input"
              placeholder="Dodaj błędną odpowiedź (max 3)"
              @input="autoGrow($event)"
            />
            <button
              class="btn blue-btn small"
              @click="addWrongAnswer(card)"
              :disabled="!newWrongAnswers[card.id]?.trim()"
            >
              ➕ Dodaj błędną odpowiedź
            </button>
          </div>

        </div>

      </div>
    </transition-group>

    <DeleteModal v-if="show" @confirm="remove" @close="closeModal" />
    <transition name="fade">
      <div v-if="previewImage" class="lightbox" @click="closeImage">
        <img
          :src="`/storage/${previewImage}`"
          class="lightbox-image"
          @click.stop
        />
        <span class="close-btn">✕</span>
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { hierarchyService } from '@/api/hierarchyService'
import { flashcardService } from '@/api/flashcardService'
import DeleteModal from './Subcomponents/DeleteModal.vue'
import CSVUpload from './Subcomponents/CSVUpload.vue'
import Toast from './Subcomponents/Toast.vue'
import Form from './Subcomponents/Form.vue'
import Lack from './Subcomponents/Lack.vue'
import GoToMain from './Subcomponents/GoToMain.vue'
import { goTo } from '@/helpers/helpers'

const set = ref([])
const selectedSetId = ref(0)
const flashcards = ref([])
const newQuestion = ref('')
const newAnswer = ref('')
const savingWrongId = ref(null)
const newNotes = ref('')
const showToast = ref(false)
const savingId = ref(0)
const loading = ref(false)
const show = ref(false)
const toDelete = ref(0)
const newQuestionEl = ref(null)
const newAnswerEl = ref(null)
const newNotesEl= ref(null)
const newImage = ref(null)
const updateImage = ref(null)
const previewImage = ref(null)
const newWrongAnswers = ref({})

const addWrongAnswer = async (card) => {
  const text = newWrongAnswers.value[card.id]?.trim()
  if (!text) return

  try {
    const res = await flashcardService.addWrongAnswer(card.id, text)
    card.options.push(res)
    newWrongAnswers.value[card.id] = ''
    showToast.value = true
    setTimeout(() => (showToast.value = false), 1200)
  } catch (error) {
    console.error('Błąd podczas dodawania błędnej odpowiedzi:', error)
  }
}


const updateWrongAnswer = async (option) => {
  if (!option.text.trim()) return

  savingWrongId.value = option.id
  try {
    await flashcardService.updateWrongAnswer(
      option.id,
      option.text.trim()
    )
    showToast.value = true
    setTimeout(() => (showToast.value = false), 1200)
  } catch (error) {
    console.error('Błąd podczas aktualizacji błędnej odpowiedzi:', error)
  } finally {
    savingWrongId.value = null
  }
}


const openImage = (path) => {
  previewImage.value = path
  document.body.style.overflow = 'hidden'
}

const closeImage = () => {
  previewImage.value = null
  document.body.style.overflow = ''
}


onMounted(() =>{
  const params = new URLSearchParams(window.location.search)
  selectedSetId.value = params.get('chosen')
  load()
  window.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeImage()
  })
})

const onFileChange = (e) => {
  newImage.value = e.target.files[0]
}

const onUpdateChange = async (e,card) => {
  try {
    updateImage.value = e.target.files[0]
    const res = await flashcardService.updateImage(card.id,updateImage.value)
    card.image_path = res.image_path
    showToast.value = true
    setTimeout(() => showToast.value = false, 1500)
    updateImage.value = null
  } catch (error) {
    console.error('Błąd podczas aktualizacji obrazka:', error)
  } 
}

const removeImage = async (card) => {
  if (!card.image_path) return

  try {
    await flashcardService.removeImage(card.id)
    card.image_path = null
  } catch (error) {
    console.error('Błąd podczas usuwania obrazu:', error)
  }
}


const openModal = (id) => {
  show.value = true
  toDelete.value = id
}

const closeModal = () => {
  show.value = false
  toDelete.value = 0
}

const goToLearn = () => {
  window.location.href = `/learn?chosen=${set.value.id}`
}

const load = async () => {
  try {
    set.value = await hierarchyService.getSets()
    set.value = set.value.filter(set => set.id == selectedSetId.value)[0]
    flashcards.value = await flashcardService.getFlashcards(selectedSetId.value)
    await nextTick()
    flashcards.value.forEach(card => {
      autoGrowById(card.id)
    })
    autoGrowTextarea(newQuestionEl.value)
    autoGrowTextarea(newAnswerEl.value)
    autoGrowTextarea(newNotesEl.value)
  } catch (error) {
    console.error('Błąd podczas ładowania fiszek:', error)
  }
}

const add = async () => {
  if (!newQuestion.value.trim() || !newAnswer.value.trim()) return
  loading.value = true
  try {
    const res = await flashcardService.addFlashcards(selectedSetId.value, newQuestion.value.trim(), newAnswer.value.trim(), newNotes.value.trim(), selectedSetId.value, newImage.value)
    flashcards.value.unshift(res)
    newQuestion.value = ''
    newAnswer.value = ''
    newNotes.value = ''
    newImage.value =null
    await nextTick()
    autoGrowTextarea(newQuestionEl.value)
    autoGrowTextarea(newAnswerEl.value)
    autoGrowTextarea(newNotesEl.value)
    autoGrowById(res.id)
  } catch (error) {
    console.error('Błąd podczas dodawania fiszki:', error)
  } finally {
    loading.value = false
  }
}

const update = async (card) => {
  if (!card.question.trim() || !card.answer.trim()) return
  savingId.value = card.id
  try {
    await flashcardService.updateFlashcard(selectedSetId.value, card.id, card.question.trim(), card.answer.trim(), card.notes? card.notes.trim():'')
    showToast.value = true
    setTimeout(() => showToast.value = false, 1500)
  } catch (error) {
    console.error('Błąd podczas aktualizacji fiszki:', error)
  } finally {
    savingId.value = 0
  }
}

const updateLearned = async (card) => {
  try {
    await flashcardService.markLearn(card.id, card.learned)
  } catch (error) {
    console.error('Błąd podczas aktualizacji statusu nauczenia:', error)
  }
}

const remove = async () => {
  try {
    await deleteFlashcard.deleteFlashcard(selectedSetId.value, toDelete.value)
    await load()
  } catch (error) {
    console.error('Błąd podczas usuwania fiszki:', error)
  }

  closeModal()
}

// Automatyczne dopasowanie wysokości textarea
const autoGrow = (event) => {
  const el = event.target
  autoGrowTextarea(el)
}

const autoGrowTextarea = (el) => {
  if (!el) return
  el.style.height = 'auto'
  el.style.height = el.scrollHeight + 'px'
}

const autoGrowById = (id) => {
  const q = document.querySelector(`textarea[data-id='q-${id}']`)
  const a = document.querySelector(`textarea[data-id='a-${id}']`)
  autoGrowTextarea(q)
  autoGrowTextarea(a)
}
</script>

<style scoped>
  .clickable {
  cursor: zoom-in;
}

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

/* X */
.close-btn {
  position: absolute;
  top: 20px;
  right: 24px;
  font-size: 26px;
  color: white;
  cursor: pointer;
  opacity: 0.8;
}

.close-btn:hover {
  opacity: 1;
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

.wrong-answers {
  
  width: auto;
  margin-top: 10px;
  margin-bottom: 20px;
  padding: 14px;
  background: #f8fafc;
  border-radius: 14px;
  border: 1px solid #e5e7eb;
}

.wrong-answers-title {
  font-size: 0.85rem;
  font-weight: 600;
  color: #64748b;
  margin-bottom: 10px;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.wrong-answer-row {
  display: flex;
  flex-direction: column;
  gap: 4px;
  margin-bottom: 10px;
}

.wrong-answer-input {
  width: auto;
  min-height: 60px;
  padding: 10px 12px;
  resize: none;
  border-radius: 10px;
 
  font-family: 'Poppins', sans-serif;
  font-size: 0.95rem;
  line-height: 1.4;
  transition: all 0.2s ease;
  overflow: hidden;
}

.wrong-answer-input:focus {
  outline: none;
  background: white;
}

.saving-inline {
  font-size: 0.7rem;
  color: #9ca3af;
  align-self: flex-end;
}


.image-section {
  display: flex;
  align-items: center;
  gap: 12px;
  width: 100%;
}

.delete-image-btn {
  background: none;
  border: none;
  font-size: 18px;
  color: #ef4444;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-left: 10px;
}

.delete-image-btn:hover {
  color: #dc2626;
  transform: scale(1.1);
}

.add-wrong-answer {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-top: 10px;
}
.btn.small {
  font-size: 0.8rem;
  padding: 6px 10px;
  align-self: flex-end;
}


.card-image {
  width: 120px;
  height: 120px;
  object-fit: cover;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  box-shadow: 0 4px 10px rgba(0,0,0,0.08);
}

.image-upload {
  cursor: pointer;
  padding: 10px 14px;
  border-radius: 10px;
  background: #eff6ff;
  color: #2563eb;
  font-size: 0.9rem;
  transition: all 0.2s ease;
  border: 1px dashed #93c5fd;
}

.image-upload:hover {
  background: #dbeafe;
  transform: translateY(-1px);
}


.item {
  display: flex;
  flex-wrap: wrap; /* pozwala na łamanie w razie braku miejsca */
  gap: 10px;
  margin-bottom: 10px;
  padding: 15px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  align-items: flex-start; /* wyrównanie na górze */
}

.textarea-field {
  flex: 1 1 45%; /* zajmuje około połowy szerokości wiersza, może się kurczyć */
  min-width: 150px; /* minimalna szerokość przy małych ekranach */
  width: 100%;
  min-height: 100px;
  padding: 12px 14px;
  height: auto; /* wysokość automatyczna */
  box-sizing: border-box;
  resize: none;
  white-space: pre-wrap;
  word-break: break-word;
  font-family: 'Poppins', sans-serif;
  font-size: 1rem;
  line-height: 1.5;
  border: 1px solid #cbd5e1; /* delikatny szary */
  border-radius: 12px; /* zaokrąglone rogi */
  background-color: #f9fafb; /* delikatne tło */
  transition: all 0.2s ease;
  overflow: hidden; /* ukrywa scroll gdy auto-grow działa */
}



.delete {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
  color: #ef4444;
  transition: all 0.2s ease;
  flex: 0 0 auto; /* nie rośnie */
  align-self: center; /* wycentrowanie przy liniach o różnych wysokościach */
}

.delete:hover {
  color: #dc2626;
  transform: scale(1.2);
}

.learned-label {
  align-items: center;
  font-size: 0.9rem;
  display: flex;
  flex: 0 0 auto; /* nie rośnie */
  margin-right: 10px;
}

.saving {
  font-size: 0.75rem;
  color: #9ca3af;
  align-self: center;
}

.loading-text {
  font-style: italic;
  margin-top: 10px;
}

/* Transition-group */
.slide-enter-active, .slide-leave-active {
  transition: all 0.3s ease;
}
.slide-enter-from, .slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Responsywność */
@media (max-width: 768px) {
  .item {
    grid-template-columns: 1fr;
  }

  .delete {
    align-self: flex-start;
    margin-top: 10px;
  }
}
</style>
