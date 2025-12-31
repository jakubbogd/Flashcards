<template>

<form @submit.prevent="add" @keyup.enter="emit('add')" class="add-form flex">
   <slot />
</form>

</template> 

<script setup>
    const emit=defineEmits(['add']);
</script>