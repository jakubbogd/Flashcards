<template>

<form @submit.prevent="add" @keyup.enter="add" class="add-form flex">
   <slot />
</form>

</template> 

<script setup>
    const emits=defineEmits(['add']);

    const add = () => emits('add')
</script>