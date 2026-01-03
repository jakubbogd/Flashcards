<template>
  <Modal>
    <h3>Koniec cyklu nauki!</h3>
    <p>Wygląda na to, że umiesz {{ easyCount }} fiszki:</p>
    <p v-for ="el in learnedCards">
      - {{ el.question }}
    </p>
    <p>Czy chcesz je oznaczyć jako nauczone?</p>
    <div class="buttons flex">
      <button @click="emit('restartCycle')" class="btn green-btn">Tak, oznacz jako nauczone</button>
      <button @click="emit('endCycle')" class="btn red-btn">Nie, wróć do menu</button>
    </div>
  </Modal>
</template>

<script setup>
import Modal from "./Modal.vue"

const emit = defineEmits(['restartCycle', 'endCycle'])
defineProps({
  easyCount: {
    type: Number,
    required: true
  },
  learnedCards: {
    type: Array,
    required: true
  }
})
</script>