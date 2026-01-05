<script setup>
import { reactive, ref } from 'vue'
import axios from 'axios'
import Form from './Subcomponents/Form.vue'
import { goTo } from '@/helpers/helpers'

const form = reactive({
  email: '',
  password: '',
  remember: false,
})

const errors = ref({})
const loading = ref(false)

const submit = async () => {
  loading.value = true
  errors.value = {}
 

  try {
  await axios.post('/login', form)
   window.location.href = '/'
  } catch (e) {
      errors.value = e.response.data.errors
    
  } finally {
    loading.value = false
  }
}
</script>

<template>
   <div class="container flex">
    <div class="con-card">
      <h1>Logowanie</h1>

      <div v-if="Object.keys(errors).length" class="errors">
        <ul>
          <li v-for="(messages, field) in errors" :key="field">
            {{ messages[0] }}
          </li>
        </ul>
      </div>

      <Form :flex="false">
        <input class="input-field" type="text" v-model="form.email" required placeholder="Email" />
        <input class="input-field" type="password" v-model="form.password" required placeholder="Hasło" />
        <label>
            <input class="input-field" type="checkbox" v-model="form.remember" /> Zapamiętaj mnie
        </label>
        <button type="submit" :disabled="loading" class="blue-btn btn" @click="submit" >
          {{ loading ? 'Logowanie...' : 'Zaloguj się' }}
        </button>
        </Form>

         
        <div>
          Nie masz konta? 
          <button class="blue-btn btn" @click="goTo('register')">Zarejestruj się</button>
        </div>
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
