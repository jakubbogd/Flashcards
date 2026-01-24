<template>
 <div>
  <div class="user-bar">
      <span>Witaj, {{ user.name }}!</span>
      <form method="POST" action="/logout">
        <input type="hidden" name="_token" :value="csrf">
        <button type="submit">Wyloguj</button>
      </form>
    </div>

  <nav class="home-grid grid">
    <HomeCard
      class="sets"
      href="/seesets"
      icon="📦"
      title="Zestawy fiszek"
      description="Twórz fiszki oraz ucz się pojedynczych zestawów"
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
      href="/seesmart-learn"
      icon="🧠"
      title="Ucz się!"
      description="Ucz się efektywnie"
    />

    <HomeCard
    class="settings"
      href="/seesettings"
      icon="🧠"
      title="Ustawienia!"
      description="Dostosuj aplikację pod siebie"
    />

  </nav>
 </div>
</template>

<script setup>
import HomeCard from './Subcomponents/HomeCard.vue'

const user = window.Laravel.user


const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')

</script>

<style scoped>
  .user-bar {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  margin-bottom: 24px;
  gap: 12px;
}

.user-bar span {
  font-weight: 500;
}

.user-bar button {
  padding: 8px 12px;
  background-color: #4f46e5;
  color: #fff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

.user-bar button:hover {
  background-color: #3730a3;
}

.home-grid {
  max-width: 1000px;
  margin: 64px auto;
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

body.dark .sets {
  background: linear-gradient(135deg, #6d28d9, #166534);
}

body.dark .exam {
  background: linear-gradient(135deg, #166534, #1e3a8a);
}

body.dark .slearn {
  background: linear-gradient(135deg, #1e3a8a, #15803d);
}

body.dark .settings {
  background: linear-gradient(135deg, #15803d, #4d7c0f);
}

</style>