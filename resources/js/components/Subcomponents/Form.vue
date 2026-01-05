<template>

<form @keyup.enter="emit('add')" class="add-form" :class="{ 'flex': flex }">
   <slot />
</form>

</template> 

<script setup>
defineProps({
  flex: {
    type: Boolean,
    default: true
  }
})

const emit=defineEmits(['add']);
</script>