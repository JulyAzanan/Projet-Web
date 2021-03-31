<template>
  <nav uk-sticky class="uk-navbar-container uk-margin" uk-navbar>
    <div class="uk-navbar-left test-overlay">
      <div class="uk-navbar-item">
        <router-link to="/">
          <img src="@/assets/logo_simple.png" width="50" alt="" />
          <img src="@/assets/logo_text.png" width="100" alt="" />
        </router-link>
      </div>
    </div>
    <div class="uk-navbar-center test-overlay">
      <ul class="uk-navbar-nav">
        <li :class="{'uk-active': $route.path === '/users'}"><router-link to="/users">utilisateurs</router-link></li>
        <li :class="{'uk-active': $route.path === '/projects'}"><router-link to="/users">projets</router-link></li>
        <li :class="{'uk-active': $route.path === '/about'}"><router-link to="/about">à propos</router-link></li>
        <li>
          <a href="#">Parent</a>
          <div class="uk-navbar-dropdown">
            <ul class="uk-nav uk-navbar-dropdown-nav">
              <li class="uk-active"><a href="#">Active</a></li>
              <li class="uk-parent">
                <a href="#">Parent</a>
                <ul class="uk-nav-sub">
                  <li><a href="#">Sub item</a></li>
                  <li><a href="#">Sub item</a></li>
                </ul>
              </li>
              <li class="uk-nav-header">Header</li>
              <li>
                <a href="#"
                  ><span
                    class="uk-margin-small-right"
                    uk-icon="icon: table"
                  ></span>
                  Item</a
                >
              </li>
              <li>
                <a href="#"
                  ><span
                    class="uk-margin-small-right"
                    uk-icon="icon: thumbnails"
                  ></span>
                  Item</a
                >
              </li>
              <li class="uk-nav-divider"></li>
              <li>
                <a href="#"
                  ><span
                    class="uk-margin-small-right"
                    uk-icon="icon: trash"
                  ></span>
                  Item</a
                >
              </li>
            </ul>
          </div>
        </li>
        <li>
          <a href="#">Content</a>
          <div class="uk-navbar-dropdown">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
            eiusmod tempor incididunt ut labore et dolore magna aliqua.
          </div>
        </li>
        <li><a href="#">Item</a></li>
        <li><a href="#">Item</a></li>
        <li><a href="#">Item</a></li>
      </ul>
      <div class="uk-navbar-item">
        <div>Some <a href="#">Link</a></div>
      </div>
    </div>
    <div class="uk-navbar-right test-overlay">
      <ul class="uk-navbar-nav">
        <li>
          <a
            class="uk-navbar-toggle"
            uk-search-icon
            uk-toggle="target: .test-overlay; animation: uk-animation-fade"
            href="#"
          ></a>
        </li>
        <li v-if="logged">
          <a href="#">Steel</a>
          <div class="uk-navbar-dropdown">
            <ul class="uk-nav uk-navbar-dropdown-nav">
              <li class="uk-nav-header">Active</li>
              <li class="uk-parent">
                <a href="#">Paramètres</a>
                <a href="#">Mon profil</a>
                <ul class="uk-nav-sub">
                  <li><a href="#">Mes projets</a></li>
                  <li><a href="#">Mes amis</a></li>
                </ul>
              </li>
              <li class="uk-nav-header">Header</li>
              <li>
                <a href="#"
                  ><span
                    class="uk-margin-small-right"
                    uk-icon="icon: table"
                  ></span>
                  Item</a
                >
              </li>
              <li>
                <a href="#"
                  ><span
                    class="uk-margin-small-right"
                    uk-icon="icon: thumbnails"
                  ></span>
                  Item</a
                >
              </li>
              <li class="uk-nav-divider"></li>
              <li>
                <a href="#"
                  ><span
                    class="uk-margin-small-right"
                    uk-icon="icon: trash"
                  ></span>
                  Déconnexion</a
                >
              </li>
            </ul>
          </div>
        </li>
        <li v-else>
          <a href="#">Se Connecter</a>
        </li>
      </ul>
    </div>
    <div class="uk-navbar-left uk-flex-1 test-overlay" hidden>
      <div class="uk-navbar-item uk-width-expand">
        <form class="uk-search uk-search-navbar uk-width-1-1">
          <input
            class="uk-search-input"
            type="search"
            placeholder="Search"
            autofocus
          />
        </form>
      </div>

      <a
        class="uk-navbar-toggle"
        uk-close
        uk-toggle="target: .test-overlay; animation: uk-animation-fade"
        href="#"
      ></a>
    </div>
  </nav>
  <div class="uk-container">
    <router-view />
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  setup(props) {
    const logged = true;
    return {
      logged,
    };
  },
});
</script>


<style lang="scss">
@import "@/styles/variables.scss";
</style>

