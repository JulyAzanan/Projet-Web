<template>
  <nav uk-sticky class="uk-navbar-container uk-margin" uk-navbar>
    <div class="uk-navbar-left search-overlay">
      <!-- Logo -->
      <div class="uk-navbar-item">
        <router-link :to="{ name: 'Home'}">
          <img src="@/assets/logo_simple.png" width="50" alt="" />
          <img src="@/assets/logo_text.png" width="100" alt="" />
        </router-link>
      </div>
    </div>
    <div class="uk-navbar-center search-overlay uk-visible@s">
      <!-- Routes principales -->
      <ul class="uk-navbar-nav">
        <Links />
      </ul>
    </div>
    <div class="uk-navbar-right search-overlay">
      <!-- Hamburger -->
      <a
        class="uk-navbar-toggle uk-hidden@s"
        uk-toggle="target: #sidenav"
        uk-navbar-toggle-icon
      ></a>
      <!-- Recherche -->
      <ul class="uk-navbar-nav uk-visible@s">
        <li class="uk-active">
          <a
            class="uk-navbar-toggle"
            uk-search-icon
            uk-toggle="target: .search-overlay; animation: uk-animation-fade"
          ></a>
        </li>
      </ul>
      <!-- Actions rapide -->
      <div v-if="logged" class="uk-navbar-item uk-visible@s">
        <ul class="uk-iconnav">
          <li>
            <a
              href="#"
              uk-tooltip="title: Nouveau projet; pos: bottom; delay: 200"
              ><span uk-icon="icon: plus"></span>
            </a>
          </li>

          <li>
            <a><span uk-icon="icon: thumbnails"></span></a>
            <div uk-dropdown="pos: bottom-justify">
              <ul class="uk-nav uk-dropdown-nav">
                <li class="uk-nav-header">Projets récents</li>
                <RecentProjects />
              </ul>
            </div>
          </li>
          <Notifications />
        </ul>
      </div>
      <ul class="uk-navbar-nav uk-visible@s">
        <!-- Profil utilisateur -->
        <li v-if="logged" class="uk-active">
          <a class="username">{{ userName }} </a>
          <div class="uk-navbar-dropdown">
            <ul class="uk-nav uk-navbar-dropdown-nav">
              <Usernav />
            </ul>
          </div>
        </li>
        <Login v-else />
      </ul>
    </div>
    <!-- Overlay de recherche -->
    <div class="uk-navbar-left uk-flex-1 search-overlay" hidden>
      <div class="uk-navbar-item uk-width-expand">
        <Search/>
      </div>
      <a
        class="uk-navbar-toggle"
        uk-close
        uk-toggle="target: .search-overlay; animation: uk-animation-fade"
      ></a>
    </div>
  </nav>
  <Sidenav />
</template>

<script lang="ts">
import { defineComponent, computed } from "vue";
import store from "@/app/store";
import Sidenav from "./Sidenav.vue";
import Links from "./Links.vue";
import Usernav from "./Usernav.vue";
import Login from "./Login.vue";
import RecentProjects from "./Recent-Projects.vue";
import Notifications from "./Notifications.vue";
import Search from "./Search.vue"

export default defineComponent({
  components: {
    Sidenav,
    Links,
    Usernav,
    Login,
    RecentProjects,
    Notifications,
    Search,
  },
  setup() {
    return {
      logged: computed(() => store.state.loggedIn),
      userName: computed(() => store.state.user),
    };
  },
});
</script>

<style lang="scss">
.username {
  text-transform: none !important;
  font-weight: bold;
}
</style>