<template>
    <transition-group name="slide" tag="div">
      <div v-for="card in flashcards" :key="card.id">
        <div class="item">
          <textarea class="blue-border-input" v-model="card.question" @input="autoGrow($event)" @blur="update(card)"/>
          <textarea class="blue-border-input" v-model="card.answer" @input="autoGrow($event)" @blur="update(card)"/>
          <textarea class="blue-border-input" v-model="card.notes" @input="autoGrow($event)" @blur="update(card)" placeholder="Dodatkowe notatki"/>
          <div class="image-section">
            
            <img v-if="card.image_path && card.image_path !== '0'" :src="`/storage/${card.image_path}`" alt="Obraz fiszki" class="card-image clickable" @click="openImage(card.image_path)"/>
            <label>
              <input type="file" accept="image/*" hidden @change="onUpdateChange($event, card)"/>
              <span v-if="card.image_path && card.image_path !== '0'">📷 Zmień obrazek</span>
              <span v-else>📷 Dodaj obrazek</span>
            </label>
            <button v-if="card.image_path && card.image_path !== '0'" @click="removeImage(card)" class="delete-image-btn">🗑️ Usuń obraz</button>
          </div>

          <input type="checkbox" v-model="card.learned" @change="updateLearned(card)" />
          Nauczone
          <button class="delete" @click="openModal(card.id)">✕</button>
          <span v-if="savingId.value === card.id" class="saving">zapisywanie…</span>
        </div>

        <div class="wrong-answers" v-if="!card.question.startsWith('Czy ')">
          <div class="wrong-answers-header" @click="toggleWrongAnswers(card.id)">
            <p class="wrong-answers-title">Błędne odpowiedzi</p>
            <span class="arrow" :class="{ open: wrongAnswersOpen[card.id] }">
              ▶
            </span>
          </div>
          <transition name="fade-slide">
            <div v-show="wrongAnswersOpen[card.id]">
              <div v-for="option in card.options.filter(o => !o.is_correct)" :key="option.id" class="wrong-answer-row">
                <textarea v-model="option.text" class="wrong-answer-input blue-border-input" placeholder="Błędna odpowiedź" @blur="updateWrongAnswer(option)" @input="autoGrow($event)"/>
                <span v-if="savingWrongId === option.id" class="saving-inline">
                  zapisywanie…
                </span>
              </div>
              <div v-if="card.options.length < 4" class="add-wrong-answer">
                <textarea v-model="newWrongAnswers[card.id]" class="wrong-answer-input blue-border-input" placeholder="Dodaj błędną odpowiedź (max 3)" @input="autoGrow($event)" />
                <button class="btn blue-btn small" @click="addWrongAnswer(card)" :disabled="!newWrongAnswers[card.id]?.trim()">
                  ➕ Dodaj błędną odpowiedź
                </button>
              </div>
            </div>
          </transition>
        </div>
      </div>
    </transition-group>
     <DeleteModal v-if="show" @confirm="remove" @close="closeModal" />
</template>

<script setup>
import DeleteModal from './DeleteModal.vue'
import { flashcardService } from '@/api/flashcardService'
import { ref,reactive } from 'vue'

defineProps({
  flashcards: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['reload'])

const wrongAnswersOpen = ref({})
const updateImage = ref(null)
const savingId = ref(0)
const newWrongAnswers = ref({})
const savingWrongId = ref(null)
const show = ref(false)
const toDelete = ref(0)

const toggleWrongAnswers = (cardId) => {
  wrongAnswersOpen.value[cardId] = !wrongAnswersOpen.value[cardId]
}


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
    console.error(error)
  }
}

const remove = async () => {
  try {
    await flashcardService.deleteFlashcard(toDelete.value)
    emit('reload')
  } catch (error) {
    console.error(error)
  }

  closeModal()
}

const closeModal = () => {
  show.value = false
  toDelete.value = 0
}

const updateWrongAnswer = async (option) => {
  if (!option.text.trim()) return

  savingWrongId.value = option.id
  try {
    await flashcardService.updateWrongAnswer(
      option.id,
      option.text
    )
    showToast.value = true
    setTimeout(() => (showToast.value = false), 1200)
  } catch (error) {
    console.error(error)
  } finally {
    savingWrongId.value = null
  }
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
    console.error(error)
  } 
}

const removeImage = async (card) => {
  if (!card.image_path) return

  try {
    await flashcardService.removeImage(card.id)
    card.image_path = null
  } catch (error) {
    console.error(error)
  }
}


const openModal = (id) => {
  show.value = true
  toDelete.value = id
}

const openImage = (path) => {
  previewImage.value = path
  document.body.style.overflow = 'hidden'
}

const update = async (card) => {
  if (!card.question.trim() || !card.answer.trim()) return
  savingId.value = card.id
  try {
    await flashcardService.updateFlashcard(selectedSetId.value, card.id, card.question, card.answer, card.notes? card.notes:'')
    showToast.value = true
    setTimeout(() => showToast.value = false, 1500)
  } catch (error) {
    console.error(error)
  } finally {
    savingId.value = 0
  }
}

const updateLearned = async (card) => {
  try {
    await flashcardService.markLearn(card.id, card.learned)
  } catch (error) {
    console.error(error)
  }
}

const autoGrow = (event) => {
  const el = event.target
  el.style.height = 'auto'
  el.style.height = el.scrollHeight + 'px'
}

</script>

<style scoped>
  @media (max-width: 768px) {
  .item {
    grid-template-columns: 1fr;
  }

  .delete {
    align-self: flex-start;
    margin-top: 10px;
  }
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



/* Transition-group */
.slide-enter-active, .slide-leave-active {
  transition: all 0.3s ease;
}
.slide-enter-from, .slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
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

.wrong-answers-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  cursor: pointer;
  user-select: none;
}

.arrow {
  font-size: 0.9rem;
  transition: transform 0.25s ease;
}

.arrow.open {
  transform: rotate(90deg);
}

/* animacja rozwijania */
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.25s ease;
}

.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}

body.dark .item {
  background: #16213a;
}

body.dark .wrong-answers {
  background: #16213a;
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


</style>