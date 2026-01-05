<template>
    <header @click="toggle" class="flex pointer">
      <h2>
        📁 {{ props.folder.name }}
        <span class="light-grey">({{ props.folder.sets_count }})</span>
      </h2>
      <span>{{ open ? '▲' : '▼' }}</span>
    </header>

    <Form v-if="open" @add="addSet">
      <input v-model="form.name" placeholder="Nazwa zestawu" class="input-field" required />
      <input v-model="form.description" placeholder="Opis (opcjonalnie)" class="input-field" />
      <button type="submit" class="btn blue-btn" @click="addSet">Dodaj zestaw</button>
    </Form>

    <div v-if="open" class="sets flex">
      <SetCard
        v-for="set in props.sets"
        :key="set.id"
        :set="set"
        @setRemoved="removeSet"
        @updateSet="updateSet"
      />
    </div>

  <Toast :showToast="showToast" />
</template>

<script setup>
import { onMounted, ref,reactive } from 'vue'
import Toast from './Toast.vue'
import SetCard from './SetCard.vue'
import Form from './/Form.vue'
import { hierarchyService } from '@/api/hierarchyService'

const form = reactive({
  name:'',
  description: '',
  folder_id: props.folder.id
})

const resetForm =() => {
  form.name=''
  form.description=''
  form.folder_id=props.folder.id
}

const emit = defineEmits(['loadSets'])
const props = defineProps({
  folder: Object,
  sets: Array,
})
const showToast = ref(false)
const savingId = ref(null)

const open = ref(true)
const toggle = () => (open.value = !open.value)
const addSet = async () => {
  if (!form.name.trim()) return
  try {
    await hierarchyService.addSet(form)
    resetForm()
    emit('loadSets')
  } catch (error) {
    console.error(error)
  }
}
onMounted(() => {
  emit('loadSets')
})

const removeSet = async (id) => {
  try {
    await hierarchyService.deleteSet(id)
    emit('loadSets')
  } catch (error) {
    console.error(error)
  }
}


const updateSet = async (set) => {
  if (!set.name.trim()) return
  savingId.value = set.id
  try {
    await hierarchyService.updateSet(set.id, set.name,set.description)
    showToast.value = true
    setTimeout(() => showToast.value = false, 1500)
  } catch (error) {
    console.error(error)
  } finally {
   savingId.value = null
  }
}
</script>


<style scoped>

.sets {
  margin-top: 15px;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 16px;
}

</style>