<template>
  <nav uk-sticky class="uk-navbar-container uk-margin" uk-navbar>
    <div class="uk-navbar-left search-overlay">
      <!-- Logo -->
      <div class="uk-navbar-item">
        <router-link to="/">
          <img src="@/assets/logo_simple.png" width="50" alt="" />
          <img src="@/assets/logo_text.png" width="100" alt="" />
        </router-link>
      </div>
    </div>
    <div class="uk-navbar-center search-overlay">
      <!-- Routes principales -->
      <ul class="uk-navbar-nav">
        <li :class="{ 'uk-active': $route.path === '/users' }">
          <router-link to="/users">utilisateurs</router-link>
        </li>
        <li :class="{ 'uk-active': $route.path === '/projects' }">
          <router-link to="/projects">projets</router-link>
        </li>
        <li :class="{ 'uk-active': $route.path === '/about' }">
          <router-link to="/about">à propos</router-link>
        </li>
      </ul>
    </div>
    <div class="uk-navbar-right search-overlay">
      <ul class="uk-navbar-nav">
        <li class="uk-active">
          <a
            class="uk-navbar-toggle"
            uk-search-icon
            uk-toggle="target: .search-overlay; animation: uk-animation-fade"
          ></a>
        </li>
      </ul>
      <!-- Actions rapide -->
      <div v-if="logged" class="uk-navbar-item">
        <ul class="uk-iconnav">
          <li>
            <a
              href="#"
              uk-icon="icon: plus"
              uk-tooltip="title: Nouveau projet; pos: bottom; delay: 200"
            ></a>
          </li>
          <li>
            <a uk-icon="icon: thumbnails"></a>
            <div uk-dropdown="pos: bottom-justify">
              <ul class="uk-nav uk-dropdown-nav">
                <li class="uk-nav-header">Projets récents</li>
                <li>
                  <a href="#">
                    <span
                      class="uk-margin-small-right"
                      uk-icon="icon: folder"
                    ></span>
                    Projet 3615
                  </a>
                </li>
                <li>
                  <a href="#">
                    <span
                      class="uk-margin-small-right"
                      uk-icon="icon: folder"
                    ></span>
                    Nier: Automata
                  </a>
                </li>
                <li>
                  <a href="#">
                    <span
                      class="uk-margin-small-right"
                      uk-icon="icon: folder"
                    ></span>
                    Daft Punk
                  </a>
                </li>
                <li class="uk-nav-divider"></li>
                <li>
                  <a href="#">
                    <span
                      class="uk-margin-small-right"
                      uk-icon="icon: lock"
                    ></span>
                    Foo
                  </a>
                </li>
                <li>
                  <a href="#">
                    <span
                      class="uk-margin-small-right"
                      uk-icon="icon: lock"
                    ></span>
                    Bar
                  </a>
                </li>
              </ul>
            </div>
          </li>
          <li>
            <a
              href="#"
              uk-icon="icon: bell"
              uk-tooltip="title: Notifications; pos: bottom; delay: 200"
              ><span class="uk-badge">2</span></a
            >
          </li>
        </ul>
      </div>
      <ul class="uk-navbar-nav">
        <!-- Profil utilisateur -->
        <li v-if="logged" class="uk-active">
          <a class="username">{{ username }} </a>
          <div class="uk-navbar-dropdown">
            <ul class="uk-nav uk-navbar-dropdown-nav">
              <li>
                <a href="#">
                  <span
                    class="uk-margin-small-right"
                    uk-icon="icon: user"
                  ></span>
                  Mon profil
                </a>
              </li>
              <li>
                <a href="#">
                  <span
                    class="uk-margin-small-right"
                    uk-icon="icon: album"
                  ></span>
                  Mes projets
                </a>
              </li>
              <li>
                <a href="#">
                  <span
                    class="uk-margin-small-right"
                    uk-icon="icon: users"
                  ></span>
                  Mes amis
                </a>
              </li>
              <li class="uk-nav-divider"></li>
              <li>
                <a href="#">
                  <span
                    class="uk-margin-small-right"
                    uk-icon="icon: settings"
                  ></span>
                  Paramètres
                </a>
              </li>
              <li>
                <a href="#">
                  <span
                    class="uk-margin-small-right"
                    uk-icon="icon: sign-out"
                  ></span>
                  Déconnexion
                </a>
              </li>
            </ul>
          </div>
        </li>
        <li v-else>
          <router-link to="/login">
            <span class="uk-margin-small-right" uk-icon="icon: sign-in"></span>
            Se connecter
          </router-link>
        </li>
      </ul>
    </div>
    <!-- Overlay de recherche -->
    <div class="uk-navbar-left uk-flex-1 search-overlay" hidden>
      <div class="uk-navbar-item uk-width-expand">
        <form class="uk-search uk-search-navbar uk-width-1-1">
          <input
            class="uk-search-input"
            type="search"
            placeholder="Rechercher..."
            autofocus
          />
        </form>
      </div>
      <a
        class="uk-navbar-toggle"
        uk-close
        uk-toggle="target: .search-overlay; animation: uk-animation-fade"
      ></a>
    </div>
  </nav>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  setup() {
    const logged = false;
    const username = "Steel";
    return {
      logged,
      username,
    };
  },
});
</script>

<style lang="scss" scoped>
.username {
  text-transform: none;
  font-weight: bold;
}
</style>