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

  <li @click="switchDarkMode">
    <!-- <router-link to="/settings"> -->

    <a>
      <span class="uk-margin-small-right" uk-icon="icon: settings"></span>
      Dark Mode:
      <span v-if="enabled">On</span>
      <span v-else>Off</span>
    </a>

    <!-- </router-link> -->
  </li>

  <li>
    <a @click="logout">
      <span class="uk-margin-small-right" uk-icon="icon: sign-out"></span>
      Déconnexion
    </a>
  </li>
</template>

<script lang="ts">
import { defineComponent, computed, ref } from "vue";
import {
  enable as enableDarkMode,
  disable as disableDarkMode,
} from "darkreader";
import store from "@/app/store";

export default defineComponent({
  setup() {
    let enabled = ref(false);
    function switchDarkMode() {
      if (enabled.value) {
        disableDarkMode();
      } else {
        enableDarkMode({
          brightness: 100,
          contrast: 90,
          sepia: 10,
        });
      }
      enabled.value = !enabled.value;
    }
    return {
      userName: computed(() => store.state.user),
      logout: () => {
        store.commit("logout");
      },
      switchDarkMode,
      enabled,
    };
  },
});
</script>
