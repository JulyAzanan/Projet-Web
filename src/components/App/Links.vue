<template>
  <!-- Routes principales -->
  <li :class="{ 'uk-active': $route.path === '/users' }">
    <router-link to="/users">Utilisateurs</router-link>
  </li>

  <li :class="{ 'uk-active': $route.path === '/projects' }">
    <router-link to="/projects">Projets</router-link>
  </li>

  <li :class="{ 'uk-active': $route.path === '/about' }">
    <router-link to="/about">À propos</router-link>
  </li>
</template>
