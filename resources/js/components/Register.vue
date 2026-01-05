<script setup>
import { reactive, ref } from 'vue'
import axios from 'axios'
import Form from './Subcomponents/Form.vue'

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const errors = ref({})
const loading = ref(false)

const submit = async () => {
  loading.value = true
  errors.value = {}

  try {
    await axios.post('/register', form)
    window.location.href = '/'
  } catch (e) {
    if (e.response?.status === 422) {
      errors.value = e.response.data.errors
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="container flex">
    <div class="con-card">
      <h1>Rejestracja</h1>

      <div v-if="Object.keys(errors).length" class="errors">
        <ul>
          <li v-for="(messages, field) in errors" :key="field">
            {{ messages[0] }}
          </li>
        </ul>
      </div>

    <Form @add="submit" :flex="false">
        <input class="input-field" type="text" v-model="form.name" required placeholder="Imię" />
        <input class="input-field" type="text" v-model="form.email" required placeholder="Email" />
        <input class="input-field" type="password" v-model="form.password" required placeholder="Hasło" />
        <input class="input-field" type="password" v-model="form.password_confirmation" required placeholder="Potwierdź hasło" />
        
        <button type="submit" :disabled="loading" class="blue-btn btn">
          {{ loading ? 'Rejestracja...' : 'Zarejestruj się' }}
        </button>
    </Form>
    </div>
  </div>
</template>

<style scoped>

.errors {
  background-color: #fdd;
  color: #900;
  padding: 0.75rem 1rem;
  margin-bottom: 1rem;
  border-radius: 5px;
  font-size: 0.9rem;
}



</style>
