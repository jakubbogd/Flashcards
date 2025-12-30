<template>
  <Modal>
    <h3>Koniec cyklu nauki!</h3>
    <p>Wygląda na to, że umiesz {{ easyCount }} fiszki:</p>
    <p v-for ="el in learnedCards">
      - {{ el.question }}
    </p>
    <p>Czy chcesz je oznaczyć jako nauczone?</p>
    <div class="buttons flex">
      <button @click="restartCycle" class="btn green-btn">Tak, oznacz jako nauczone</button>
      <button @click="endCycle" class="btn red-btn">Nie, wróć do menu</button>
    </div>
  </Modal>
</template>

<script setup>
import Modal from "./Modal.vue"

const emits = defineEmits(['restartCycle', 'endCycle'])
const props = defineProps({
  easyCount: {
    type: Number,
    required: true
  },
  learnedCards: {
    type: Array,
    required: true
  }
})

const restartCycle = () => {
  emits('restartCycle')
}
const endCycle = () => {
  emits('endCycle')
}
</script>