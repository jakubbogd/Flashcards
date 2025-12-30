<template>
  <a :href="href" class="home-card flex">
    <div class="icon">{{ icon }}</div>

    <div>
      <h2>{{ title }}</h2>
      <p>{{ description }}</p>
    </div>
  </a>
</template>

<script setup>
defineProps({
  href: String,
  icon: String,
  title: String,
  description: String,
})
</script>

<style scoped>
.home-card {
  align-items: center;
  justify-content: center;
  border-radius: 24px;
  text-decoration: none;
  color: white;
  padding: 50px 30px;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.home-card:hover {
  transform: translateY(-8px) scale(1.02);
  box-shadow: 0 35px 60px rgba(0, 0, 0, 0.25);
}


.icon {
  font-size: 2.5rem;
  margin-right: 20px;
}

.home-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s;
}

.home-card:hover::before {
  left: 100%;
}

@media (max-width: 768px) {
  .home-card {
    padding: 40px 20px;
  }

}

@media (max-width: 480px) {
  .home-card {
    padding: 30px 15px;
  }
}
</style>
