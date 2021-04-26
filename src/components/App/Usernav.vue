<template>
  <li>
    <router-link :to="{ name: 'Profile', params: { userName } }">
      <span class="uk-margin-small-right" uk-icon="icon: user"></span>
      Mon profil
    </router-link>
  </li>

  <li>
    <router-link :to="{ name: 'User', params: { userName } }">
      <span class="uk-margin-small-right" uk-icon="icon: album"></span>
      Mes projets
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
    <a @click="logout">
      <span class="uk-margin-small-right" uk-icon="icon: sign-out"></span>
      Déconnexion
    </a>
  </li>
</template>

<script lang="ts">
import { defineComponent, computed } from "vue";
import store from "@/app/store";

export default defineComponent({
  setup() {
    return {
      userName: computed(() => store.state.user),
      logout: () => {
        store.commit("logout");
      }
    };
  },
});
</script>
