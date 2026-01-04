<template>
     <div class="con-card"  :class="{ active: isDragging }"  @dragover.prevent="isDragging = true"  @dragleave.prevent="isDragging = false"  @drop.prevent="onDrop">
      <input type="file" ref="fileInput" accept=".csv" class="hidden" @change="onFile"/>
      <div class="pointer" @click="triggerFile">
        <div class="icon">📄</div>

        <p v-if="!file" class="light-grey">
          Przeciągnij plik CSV tutaj<br />
          <span>lub kliknij, aby wybrać</span>
        </p>

        <p v-else class="file light-grey">
          {{ file.name }}
        </p>
      </div>

      <button class="btn blue-btn" :disabled="!file || loading" @click.stop="uploadCSV">
        <span v-if="!loading">⬆ Importuj</span>
        <span v-else>⏳ Importuję…</span>
      </button>

      <div v-if="loading" class="progress">
        <div class="bar" :style="{ width: progress + '%' }"></div>
      </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { flashcardService } from '@/api/flashcardService'


const props = defineProps({
  set: {
    type: Object,
    required: true
  }
})

const file = ref(null)
const fileInput = ref(null)
const isDragging = ref(false)
const loading = ref(false)
const progress = ref(0)

const triggerFile = () => fileInput.value.click()

const onFile = (e) => {
  file.value = e.target.files[0]
}

const onDrop = (e) => {
  isDragging.value = false
  file.value = e.dataTransfer.files[0]
}

const uploadCSV = async () => {
  if (!file.value) return

  loading.value = true
  progress.value = 0
  const timer = setInterval(() => {
    if (progress.value < 90) progress.value += 10
  }, 300)

  await flashcardService.import(props.set.id, file.value)

  clearInterval(timer)
  progress.value = 100

  setTimeout(() => {
    loading.value = false
    file.value = null
    progress.value = 0
  }, 600)
}

</script>

<style scoped>
.hidden {
  display: none;
}


.csv-dropzone.active {
  box-shadow: 0 0 0 3px rgba(99,102,241,0.3);
  transform: scale(1.02);
}

.icon {
  font-size: 3rem;
  margin-bottom: 10px;
}

.file {
  font-weight: 600;
  color: #111827;
}

.progress {
  margin-top: 15px;
  height: 8px;
  background: #e5e7eb;
  border-radius: 999px;
  overflow: hidden;
}

.bar {
  height: 100%;
  background: linear-gradient(90deg, #10b981, #059669);
  transition: width 0.3s;
}

</style>