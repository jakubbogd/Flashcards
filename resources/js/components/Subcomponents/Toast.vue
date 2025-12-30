<template>
    <transition name="slide">
      <div v-if="showToast" class="toast" :class="{'green-btn': props.theme, 'red-btn':!props.theme}">{{ props.text }}</div>
    </transition>
</template>

<script setup>


const props = defineProps({
  showToast: Boolean,
  text: {
    type: String,
    default: "✔ Zapisano"
  },
  theme: {
    type: Boolean,
    default: true
  }
})
</script>

<style scoped>
.toast {
  position: fixed;
  bottom: 30px;
  right: 30px;
  color: white;
  padding: 14px 20px;
  border-radius: 14px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  font-weight: 500;
  margin-top: 30px;
  z-index: 1000;
}


</style>