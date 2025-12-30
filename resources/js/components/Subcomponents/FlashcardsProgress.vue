<template>
  <div class="flashcards-progress flex">
    <div class="progress-bar">
      <div class="progress" :style="{ width: progress + '%', background: progressColor }"></div>
    </div>
    <div class="counter">
      {{ current }} / {{ total }}
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  current: { type: Number, required: true, validator: (value) => value >= 0 },
  total: { type: Number, required: true, validator: (value) => value > 0 }
})

const progress = computed(() => {
  if (!props.total) return 0
  return Math.min((props.current / props.total) * 100, 100)
})

const progressColor = computed(() => {
  const p = progress.value / 100 // 0 → 1
  const hue = 0 + (120 * p) // 0 (czerwony) → 120 (zielony)
  const saturation = 70 // Stała nasycenie
  const lightness = 50 // Stała jasność
  return `hsl(${hue}, ${saturation}%, ${lightness}%)`
})
</script>

<style scoped>
.flashcards-progress {
  margin-bottom: 20px;
  margin-top: 20px;
  width: 100%;
  flex-direction: column;
  align-items: center;
}

.progress-bar {
  width: min(100%, 400px);
  height: 25px;
  background: #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  margin: 0 auto 12px;
  box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
}

.progress {
  height: 100%;
  width: 0;
  background: linear-gradient(90deg, #6366f1, #4f46e5);
  border-radius: 10px;
  transition: width 0.3s ease;
}

.counter {
  font-size: 1rem;
  text-align: center;
  color: #374151;
  font-weight: 600;
}
</style>
