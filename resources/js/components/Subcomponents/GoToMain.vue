<template>
    <button class="home-btn" @click="goTo()">
        <svg width="24" height="24" viewBox="0 0 24 24">
            <path fill="currentColor"
            d="M12 3l9 8h-3v9h-5v-6h-2v6H6v-9H3z" />
        </svg>
    </button>
</template>

<script setup>
import { goTo } from '@/helpers/helpers'

</script>

<style scoped>
    .home-btn {
  position: absolute;
  top: 16px;
  left: 16px;

  background: transparent;
  border: none;
  font-size: 26px;
  cursor: pointer;

  opacity: 0.7;
  transition: transform 0.2s ease, opacity 0.2s ease;
}

.home-btn:hover {
  opacity: 1;
  transform: scale(1.1);
}

</style>