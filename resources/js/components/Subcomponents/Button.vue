<template>
    <button :class="type+'-btn'" class="btn">{{ text }}</button>
</template>
<script setup>
defineProps({
  type: { type: String, required: true },
  text: { type: String, required: true }
})
</script>
<style scoped>


</style>