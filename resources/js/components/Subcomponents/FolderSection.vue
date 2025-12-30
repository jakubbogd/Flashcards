<template>
    <header @click="toggle" class="flex pointer">
      <h2>
        📁 {{ props.folder.name }}
        <span class="light-grey">({{ props.folder.sets_count }})</span>
      </h2>
      <span>{{ open ? '▲' : '▼' }}</span>
    </header>

    <Form v-if="open" @add="addSet">
      <input v-model="newSetName" placeholder="Nazwa zestawu" class="input-field" required />
      <input v-model="newSetDescription" placeholder="Opis (opcjonalnie)" class="input-field" />
      <button type="submit" class="btn blue-btn">Dodaj zestaw</button>
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
import { onMounted, ref } from 'vue'
import Toast from './Toast.vue'
import SetCard from './SetCard.vue'
import Form from './/Form.vue'
import { hierarchyService } from '@/api/hierarchyService'


const emits = defineEmits(['loadSets'])
const props = defineProps({
  folder: Object,
  sets: Array,
})
const showToast = ref(false)
const savingId = ref(null)
const newSetName = ref('')
const newSetDescription = ref('')
const open = ref(true)
const toggle = () => (open.value = !open.value)
const addSet = async () => {
  if (!newSetName.value.trim()) return
  try {
    await hierarchyService.addSet(newSetName.value.trim(),newSetDescription.value.trim(),props.folder.id)
    newSetName.value = ''
    newSetDescription.value = ''
    emits('loadSets', props.folder.id)
  } catch (e) {
    console.error('Błąd przy tworzeniu zestawu:', e)
  }
}
onMounted(() => {
  emits('loadSets', props.folder.id)
})

const removeSet = async (id) => {
  try {
    await hierarchyService.deleteSet(id)
    emits('loadSets', props.folder.id)
  } catch (e) {
    console.error('Błąd przy usuwaniu zestawu:', e)
  }
}


const updateSet = async (set) => {
  if (!set.name.trim()) return
  savingId.value = set.id
  try {
    await hierarchyService.updateSet(set.id, set.name.trim(),set.description.trim())
    showToast.value = true
    setTimeout(() => showToast.value = false, 1500)
  } catch (error) {
    console.error('Błąd podczas aktualizacji zestawu:', error)
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