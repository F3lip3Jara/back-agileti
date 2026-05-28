<script setup>
import { ref } from 'vue'

const sidebarNavItems = ref([
  {
    title: 'Profile',
    href: '/settings/profile',
  },
  {
    title: 'Password',
    href: '/settings/password',
  },
  {
    title: 'API Tokens',
    href: '/settings/api-tokens',
  }
])
</script>

<template>
  <div class="settings-layout">
    <!-- Componente de navegación sidebar usando sidebarNavItems -->
    <aside class="sidebar">
      <nav class="nav-flex">
        <a 
          v-for="item in sidebarNavItems" 
          :key="item.href" 
          :href="item.href"
          class="nav-link"
        >
          {{ item.title }}
        </a>
      </nav>
    </aside>
    
    <main class="content">
      <slot />
    </main>
  </div>
</template>

<style scoped>
/* Estilos premium de navegación de configuraciones */
.settings-layout {
  display: flex;
  gap: 2rem;
  padding: 2rem;
  font-family: 'Inter', sans-serif;
}
.sidebar {
  width: 250px;
}
.nav-flex {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
.nav-link {
  display: block;
  padding: 0.75rem 1rem;
  border-radius: 0.375rem;
  color: #4b5563;
  text-decoration: none;
  transition: all 0.2s ease;
}
.nav-link:hover {
  background-color: #f3f4f6;
  color: #111827;
}
.content {
  flex: 1;
}
</style>
