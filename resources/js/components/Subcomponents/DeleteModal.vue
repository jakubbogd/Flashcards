<script setup>
import Modal from "./Modal.vue"

const emits = defineEmits(['confirm', 'close'])

const close = () => {
  emits('close')
}

const confirmDelete = () => {
  emits('confirm')
  close()
}
</script>

<template>
  <Modal>
    <p>Czy na pewno chcesz usunąć tę fiszkę?</p>
    <div class="buttons flex">
      <button @click="close" class="btn cancel">Anuluj</button>
      <button @click="confirmDelete" class="btn red-btn">Usuń</button>
    </div>
  </Modal>
</template>

<style scoped>

.cancel {
  background: #f3f4f6;
  color: #374151;
}

.cancel:hover {
  background: #e5e7eb;
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
}


</style>
