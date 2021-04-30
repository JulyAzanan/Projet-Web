<template>
  <div id="sidenav" uk-offcanvas="flip: true; overlay: true">
    <div class="uk-offcanvas-bar">
      <button class="uk-offcanvas-close" type="button" uk-close></button>
      <ul class="uk-nav-default uk-nav-parent-icon" uk-nav>
        <Search/>
        <li class="uk-nav-header">Naviguation</li>
        <Links />
        <li v-if="logged" class="uk-nav-header">Actions rapides</li>
        <li v-if="logged">
          <a href="#">
            <span class="uk-margin-small-right" uk-icon="icon: plus"></span>
            Nouveau projet
          </a>
        </li>
        <li class="uk-parent" v-if="logged">
          <a>
            <span
              class="uk-margin-small-right"
              uk-icon="icon: thumbnails"
            ></span>
            Projets récents
          </a>
          <ul class="uk-nav-sub">
            <RecentProjects />
          </ul>
        </li>
        <li v-if="logged" class="uk-nav-header username">{{ userName }}</li>
        <Usernav v-if="logged" />
        <Login v-else />
      </ul>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, computed } from "vue";
import store from "@/app/store"
import Links from "./Links.vue";
import Usernav from "./Usernav.vue";
import Login from "./Login.vue";
import RecentProjects from "./Recent-Projects.vue";
import Search from "./Search.vue"

export default defineComponent({
  components: {
    Links,
    Usernav,
    Login,
    RecentProjects,
    Search,
  },
  setup() {
    return {
      userName: computed(() => store.state.user),
      logged: computed(() => store.state.loggedIn),
    };
  },
});
</script>
