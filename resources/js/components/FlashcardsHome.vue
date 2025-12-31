<template>
 
<nav class="home-grid grid">
  <HomeCard
    class="sets"
    href="/sets"
    icon="📦"
    title="Zestawy fiszek"
    description="Twórz i porządkuj fiszki w zestawach"
  />
  <HomeCard
  class="exam"
    href="/exam"
    icon="✏️"
    title="Egzamin z fiszek"
    description="Sprawdź, czy potrafisz"
  />

  <HomeCard
  class="slearn"
    href="/smart-learn"
    icon="🧠"
    title="Ucz się!"
    description="Ucz się efektywnie"
  />

  <HomeCard
  class="settings"
    href="/settings"
    icon="🧠"
    title="Ustawienia!"
    description="Dostosuje aplikację pod siebie"
  />

</nav>

</template>

<script setup>
import HomeCard from './Subcomponents/HomeCard.vue'
</script>

<style scoped>
.home-grid {
  max-width: 1000px;
  margin: 60px auto;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 24px;
}

.sets {
  background: linear-gradient(135deg, #a146dd, #609c07);
}

.exam {
  background: linear-gradient(135deg, #609c07, #4f46e5);
}

.slearn {
  background: linear-gradient(135deg, #4f46e5, #37a418);
}

.settings {
  background: linear-gradient(135deg, #37a418, #ade571);
}
</style>