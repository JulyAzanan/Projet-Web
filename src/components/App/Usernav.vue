<template>
  <li>
    <router-link to="/profile">
      <span class="uk-margin-small-right" uk-icon="icon: user"></span>
      Mon profil
    </router-link>
  </li>

  <li>
    <router-link :to="{ name: 'User', params: { user: username } }">
      <span class="uk-margin-small-right" uk-icon="icon: album"></span>
      Mes projets
    </router-link>
  </li>

  <li>
    <router-link to="/friends">
      <span class="uk-margin-small-right" uk-icon="icon: users"></span>
      Mes amis
    </router-link>
  </li>

  <li class="uk-nav-divider"></li>

  <li>
    <router-link to="/settings">
      <span class="uk-margin-small-right" uk-icon="icon: settings"></span>
      Paramètres
    </router-link>
  </li>

  <li>
    <a cli>
      <span class="uk-margin-small-right" uk-icon="icon: sign-out"></span>
      Déconnexion
    </a>
  </li>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  setup() {
    return {
      username: "Steel",
    };
  },
});
</script>
