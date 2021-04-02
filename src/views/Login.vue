<template>
      <div class="background">
        <div class="uk-card uk-card-default uk-card-body uk-position-center">
          <h3 class="uk-card-title">Connexion</h3>
          <form class="uk-form-stacked">
            <span class="uk-form-label">
              Pas encore de compte ?
              <router-link to="register">S'enregistrer</router-link>
            </span>

            <div class="uk-margin">
              <div class="uk-inline">
                <span class="uk-form-icon" uk-icon="icon: user"></span>
                <input
                  class="uk-input"
                  type="text"
                  placeholder="Nom d'utilisateur"
                  required
                  autofocus
                />
              </div>
            </div>

            <div class="uk-margin">
              <div class="uk-inline">
                <span class="uk-form-icon" uk-icon="icon: lock"></span>
                <input
                  class="uk-input"
                  type="password"
                  placeholder="Mot de passe"
                  required
                />
              </div>
            </div>

            <div class="uk-width-auto">
              <button class="uk-button uk-button-default uk-width-1-1">
                se connecter
              </button>
            </div>
          </form>
        </div>
      </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({});
</script>

<style lang="scss" scoped>
.background {
  background-image: url("../assets/login.jpg");
  background-color: #685d5a;
  height: 80vh;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
}
// Photo by <a href="https://unsplash.com/@john_matychuk?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">John Matychuk</a> on <a href="https://unsplash.com/s/photos/music?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Unsplash</a>
</style>
