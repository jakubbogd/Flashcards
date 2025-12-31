<template>
  <div class="container flex">
    <GoToMain/>
    
    <h1>Zestawy fiszek</h1>
   <Form @add="addFolder">
      <input v-model="newFolderName" placeholder="Nazwa folderu" class="input-field" required />
      <button type="submit" class="blue-btn btn">Dodaj folder</button>
   </Form>
    <FolderSection
      v-for="folder in folders"
      :key="folder.id"
      :folder="folder"
      :sets="sets.filter(set => set.folder_id === folder.id)"
      @loadSets="loadSets()"
    />

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import FolderSection from './Subcomponents/FolderSection.vue'
import Form from './Subcomponents/Form.vue'
import GoToMain from './Subcomponents/GoToMain.vue'
import { hierarchyService } from '@/api/hierarchyService'

const sets = ref([])
const folders = ref([])
const newFolderName = ref('')

const loadFolders = async () => {
  folders.value = await hierarchyService.getFolders()
}

const loadSets = async () => {
  try {
    sets.value = await hierarchyService.getSets()
  } catch (error) {
    console.error(error)
  }
}


const addFolder = async () => {
  if (!newFolderName.value.trim()) return
  try {
    const res = await hierarchyService.addFolder(newFolderName.value)
    folders.value.unshift(res)
    newFolderName.value = ''
    loadFolders()
  } catch (error) {
    console.error(error)
  }
}

onMounted(()=>{
  loadSets()
  loadFolders()
})
</script>
