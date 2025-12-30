<template>
    <div class="container flex">
      <GoToMain />
  <div class="con-card">
    <h2>⚙️ Ustawienia</h2>


    <div class="setting toggle">
      <label>🌙 Tryb ciemny</label>
      <input type="checkbox" v-model="dark" />
    </div>
    <button class="btn blue-btn" @click="save">Zapisz</button>
  </div>
  </div>
</template>

<script setup>
import { ref, onMounted} from 'vue'
import GoToMain from './Subcomponents/GoToMain.vue'
import { settingsService } from '@/api/settingsService'


const dark = ref(false)

onMounted(async () => {
  try {
    const res = await settingsService.getSettings()

    // 🔹 upewniamy się, że jest boolean
    dark.value = Boolean(res.dark_mode)

    applyDark()
    ready.value = true
  } catch {
    // fallback – tylko UX
    dark.value = localStorage.getItem('dark_mode') === '1'
    applyDark()
  }
})

function applyDark() {
  document.body.classList.toggle('dark', dark.value)
}

async function save() {
  await settingsService.updateSettings(dark.value)
  localStorage.setItem('dark_mode', dark.value ? '1' : '0')
  applyDark()
}

</script>


<style scoped>

.setting {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.setting input[type="number"] {
  width: 80px;
  padding: 8px;
  border-radius: 10px;
  border: 1px solid #ddd;
}

.toggle input {
  transform: scale(1.4);
}

</style>