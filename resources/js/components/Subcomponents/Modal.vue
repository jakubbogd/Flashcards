
<template>
  <div class="overlay flex">
    <div class="modal">
      <slot />
    </div>
  </div>
</template>

<style>
.modal {
  background: white;
  padding: 24px;
  border-radius: 12px;
  text-align: center;
  max-width: min(90vw, 320px);
  width: 100%;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
  animation: fadeIn 0.3s ease-out;
}

.overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.3);
  justify-content: center;
  align-items: center;
  z-index: 1000;
  backdrop-filter: blur(2px);
}


@media (max-width: 480px) {
  .modal {
    padding: 20px;
  }
}
</style>