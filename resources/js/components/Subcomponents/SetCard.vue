<template>
  <div class="set-card pointer">
    <input class="green-border-input input-field" v-model="set.name" @blur="emit('updateSet', set)" />
    <input class="green-border-input input-field" v-model="set.description"  @blur="emit('updateSet', set)" placeholder="Opis" />
    <p>{{ set.flashcards_count ?? 0 }} fiszek</p>
    <div class="buttons flex">
      <button class="btn green-btn" @click="goTo(`create?chosen=${set.id}`)">Twórz</button>
      <button class="btn red-btn" @click="emit('setRemoved', set.id)">Usuń</button>
    </div>
  </div>
  
</template>

<script setup>
import { goTo } from '@/helpers/helpers'

defineProps({ set: Object })
const emit=defineEmits(['setRemoved','updateSet'])

</script>

<style scoped>
.set-card {
  padding: 25px;
  border-radius: 16px;
  background: white;
  transition: 0.2s;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.set-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 30px rgba(0,0,0,0.12);
}
</style>