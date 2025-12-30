<template>
  <div class="learn-container">

    <section class="learn-section flex">
      <transition name="card-slide" mode="out-in">
        <div class="card-wrapper" :key="flashcards[props.current].id">
          <div ref="card"
            class="card pointer"
            :style="{ transform: `rotateY(${rotateY}deg) scale(${scale})`, '--rotate-y': rotateY + 'deg' }"
            @mousedown="pressCard"
            @mouseup="releaseCard"
            @mouseleave="releaseCard"
            @click="flipCard"
          >
            <div ref="front" class="site front flex" :style="{ fontSize: fontSize + 'rem', ...siteStyle }">{{ flashcards[props.current].question }}</div>
            <div ref="back" class="site back flex" :style="{ fontSize: fontSize + 'rem', ...siteStyle }">{{ flashcards[props.current].answer }}</div>
          </div>
        </div>
      </transition>

      <div class="buttons flex">
        <button @click="nextCard" class="btn green-btn">Następna karta</button>
        <button @click="flipCard" class="btn red-btn">Odwróć kartę</button>
      </div>

      <div v-if="showAnswer">
         <p v-if="flashcards[props.current].notes" class="con-card">
          {{ flashcards[props.current].notes }}
         </p>
         <img
                v-if="flashcards[props.current].image_path && flashcards[props.current].image_path !== '0'"
                :src="`/storage/${flashcards[props.current].image_path}`"
                alt="Obraz fiszki"
                class="card-image clickable"
                @click="openImage(flashcards[props.current].image_path)"
              />
        <div class="buttons flex">
          <button @click="markEasy" class="btn green-btn">Łatwe</button>
          <button @click="markHard" class="btn red-btn">Trudne</button>
        </div>
      </div>
      
     
    </section>
    <transition name="fade">
      <div v-if="previewImage" class="lightbox" @click="closeImage">
        <img
          :src="`/storage/${previewImage}`"
          class="lightbox-image"
          @click.stop
        />
        <span class="close-btn">✕</span>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
  flashcards: {
    type: Array,
    required: true
  },
  current: {
    type: Number,
    required: true
  },
  easyCounts: {
    type: Object,
    required: true
  }
})

const emits = defineEmits(['update:current', 'markEasy', 'markHard','openEndModal'])

const showAnswer = ref(false)
const rotateY = ref(0)
const scale = ref(1)

const transitionClass = ref('')
const previewImage = ref(null)

const openImage = (path) => {
  previewImage.value = path
  document.body.style.overflow = 'hidden'
}

const closeImage = () => {
  previewImage.value = null
  document.body.style.overflow = ''
}

const front = ref(null)
const back = ref(null)
const card=ref(null)
const fontSize = ref(1.6)
const siteStyle = ref({ padding: '20px' })

const adjustFontSize = async () => {
  const site = showAnswer.value ? back.value : front.value
  const cardEl = card.value
  if (!site || !cardEl) return

  let size = 1.6
  site.style.fontSize = size + 'rem'

  while (site.scrollHeight > cardEl.clientHeight && size > 0.8) {
    size -= 0.15
    site.style.fontSize = size + 'rem'
  }
  fontSize.value = size

}
onMounted(() =>{
  window.addEventListener('keydown', e => {
      if (e.key === 'Escape') closeImage()
    })
  adjustFontSize()
})


const pressCard = () => { scale.value = 1.05 }
const releaseCard = () => { scale.value = 1 }

const flipCard = () => {
  showAnswer.value = !showAnswer.value
  rotateY.value = showAnswer.value ? 180 : 0
  adjustFontSize()
}

const markEasy = () => {
  const cardId = props.flashcards[props.current].id
  emits('markEasy',cardId)
  nextCard()
}

const markHard = () => {
  const cardId = props.flashcards[props.current].id
  emits('markHard',cardId)
  nextCard()
}



const nextCard = () => {
  if (props.flashcards.length === 0) return
  transitionClass.value = 'slide-out'
  setTimeout(() => {
    const newCurrent = (props.current + 1) % props.flashcards.length
    if (newCurrent === 0) {
      emits('openEndModal',Object.values(props.easyCounts).filter(v => v > 0).length,Object.keys(props.easyCounts).filter(key => props.easyCounts[key] > 0)) 
    } else {
      emits('update:current', newCurrent)
    }
    showAnswer.value = false
    rotateY.value = 0
    transitionClass.value = 'slide-in'
    setTimeout(() => transitionClass.value = '', 300)
    adjustFontSize()
  }, 300)
   
}


</script>


<style scoped>
  .clickable {
  cursor: zoom-in;
}

/* tło */
.lightbox {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.75);
  backdrop-filter: blur(4px);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
}

/* powiększony obraz */
.lightbox-image {
  max-width: 90vw;
  max-height: 90vh;
  border-radius: 16px;
  box-shadow: 0 25px 60px rgba(0,0,0,0.4);
  animation: zoomIn 0.25s ease;
  cursor: zoom-out;
}

/* X */
.close-btn {
  position: absolute;
  top: 20px;
  right: 24px;
  font-size: 26px;
  color: white;
  cursor: pointer;
  opacity: 0.8;
}

.close-btn:hover {
  opacity: 1;
}

/* animacje */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

@keyframes zoomIn {
  from {
    transform: scale(0.85);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}

.image-section {
  display: flex;
  align-items: center;
  gap: 12px;
  width: 100%;
}

.card-image {
  width: 120px;
  height: 120px;
  object-fit: cover;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  box-shadow: 0 4px 10px rgba(0,0,0,0.08);
}


.learn-section {
  flex-direction: column;
  gap: 25px;
  align-items: center;
}

.card-wrapper {
  perspective: 1000px;
  width: 100%;
  max-width: 400px;
  margin: 0 auto;
}

.card {
  width: 100%;
  min-height: 220px;
  height: auto;
  border-radius: 20px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  transform-style: preserve-3d;
  transition: transform 0.8s ease, box-shadow 0.8s ease;
  position: relative;
}

.site{
  position: absolute;
  width: 100%;
  height: 100%;
  overflow-y: auto; /* przewijanie jeśli za dużo tekstu */
  border-radius: 20px;
  justify-content: center;
  align-items: center;
  backface-visibility: hidden;
  box-sizing: border-box;
  font-weight: 600;
  color: white;
}

.front {
  background: linear-gradient(135deg, #6366f1, #4f46e5);
}

.back {
  background: linear-gradient(135deg, #fbbf24, #f59e0b);
  transform: rotateY(180deg);
}

.card:hover {
  transform: scale(1.05) rotateY(var(--rotate-y, 0deg));
  box-shadow: 0 14px 35px rgba(0, 0, 0, 0.2);
}

.card-wrapper.slide-out {
  animation: slideOut 0.3s forwards;
}

.card-wrapper.slide-in {
  animation: slideIn 0.3s forwards;
}


.card-slide-enter-active, .card-slide-leave-active {
  transition: all 0.3s ease;
}

.card-slide-enter-from {
  transform: translateX(100%) scale(0.9);
  opacity: 0;
}

.card-slide-enter-to, .card-slide-leave-from  {
  transform: translateX(0) scale(1);
  opacity: 1;
}

.card-slide-leave-to {
  transform: translateX(-100%) scale(0.9);
  opacity: 0;
}


/* Responsywność */
@media (max-width: 768px) {

  .card {
    height: 180px;
  }

  .site {
    padding: 15px;
  }
}

@media (max-width: 480px) {
  .card {
    height: 150px;
  }

  .site{
    padding: 10px;
  }

}
</style>