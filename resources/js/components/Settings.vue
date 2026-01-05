<template>
    <div class="container flex">
      <GoToMain />
      <div class="con-card">
        <h2>⚙️ Ustawienia</h2>
        <div class="setting toggle">
          <label>🌙 Tryb ciemny</label>
          <input type="checkbox" v-model="dark" />
        </div>
        <button class="btn blue-btn" @click="saveDark">Zapisz</button>

        <hr class="divider" />

        <!-- Sekcja aktualizacji profilu -->
        <h3>👤 Twoje dane</h3>
        <div v-if="profileErrors.length" class="errors">
          <ul>
            <li v-for="(err, i) in profileErrors" :key="i">{{ err }}</li>
          </ul>
        </div>
        <form @submit.prevent="updateProfile">
          <div class="setting">
            <label>Imię</label>
            <input type="text" v-model="form.name" required />
          </div>
          <div class="setting">
            <label>Email</label>
            <input type="email" v-model="form.email" required />
          </div>
          <button class="btn green-btn" type="submit">Zapisz dane</button>
        </form>

        <hr class="divider" />

        <!-- Sekcja zmiany hasła -->
        <h3>🔑 Zmiana hasła</h3>
        <div v-if="passwordErrors.length" class="errors">
          <ul>
            <li v-for="(err, i) in passwordErrors" :key="i">{{ err }}</li>
          </ul>
        </div>
        <form @submit.prevent="updatePassword">
          <div class="setting">
            <label>Nowe hasło</label>
            <input type="password" v-model="passwordForm.password" required />
          </div>
          <div class="setting">
            <label>Potwierdź hasło</label>
            <input type="password" v-model="passwordForm.password_confirmation" required />
          </div>
          <button class="btn orange-btn" type="submit">Zmień hasło</button>
        </form>
      </div>
    </div>
</template>

<script setup>
import { ref, onMounted} from 'vue'
import GoToMain from './Subcomponents/GoToMain.vue'
import { settingsService } from '@/api/settingsService'
import { userService } from '@/api/userService'

const dark = ref(false)
const form = ref({
  name: '',
  email: ''
})
const profileErrors = ref([])
const passwordForm = ref({
  password: '',
  password_confirmation: ''
})
const passwordErrors = ref([])

onMounted(async () => {
  try {
    const user = await userService.getUser()
    form.value.name = user.name
    form.value.email = user.email
    
    const res = await settingsService.getSettings()
    dark.value = Boolean(res.dark_mode)
  } catch (error ){
    console.error(error)
  } finally {
    applyDark()
  }
})

function applyDark() {
  document.body.classList.toggle('dark', dark.value)
}

async function saveDark() {
  await settingsService.updateSettings(dark.value)
  localStorage.setItem('dark_mode', dark.value ? '1' : '0')
  applyDark()
}

// Aktualizacja profilu (imię, email)
async function updateProfile() {
  profileErrors.value = []
  try {
    await userService.updateProfile(form.value)
  } catch (err) {
    if (err.response?.status === 422) {
      profileErrors.value = Object.values(err.response.data.errors).flat()
    }
  }
}

// Aktualizacja hasła
async function updatePassword() {
  passwordErrors.value = []
  try {
    await userService.updatePassword(passwordForm.value)
    passwordForm.value.password = ''
    passwordForm.value.password_confirmation = ''
  } catch (err) {
    if (err.response?.status === 422) {
      passwordErrors.value = Object.values(err.response.data.errors).flat()
    }
  }
}
</script>


<style scoped>

.setting {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.toggle input {
  transform: scale(1.4);
}

.btn {
  padding: 0.5rem 1rem;
  border-radius: 6px;
  border: none;
  cursor: pointer;
  font-weight: 500;
  margin-top: 10px;
}

.blue-btn {
  background-color: #4f46e5;
  color: white;
}
.green-btn {
  background-color: #16a34a;
  color: white;
}
.orange-btn {
  background-color: #f97316;
  color: white;
}

.divider {
  margin: 20px 0;
  border: 0;
  height: 1px;
  background-color: #ddd;
}

.errors {
  background-color: #fdd;
  padding: 0.5rem 1rem;
  border-radius: 5px;
  color: #900;
  margin-bottom: 10px;
}
</style>