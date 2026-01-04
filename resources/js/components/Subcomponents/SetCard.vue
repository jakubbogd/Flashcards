<template>
  <div class="con-card pointer">
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