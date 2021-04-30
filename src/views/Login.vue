<template>
  <div class="background">
    <div class="uk-card uk-card-default uk-card-body uk-position-center">
      <h3 class="uk-card-title">Connexion</h3>
      <form class="uk-form-stacked" onsubmit="return false;">
        <span class="uk-form-label">
          Pas encore de compte ?
          <router-link
            :to="{
              name: 'Register',
              query: { redirect: from.fullPath },
            }"
            replace
            >S'enregistrer</router-link
          >
        </span>

        <div class="uk-margin">
          <div class="uk-inline">
            <span class="uk-form-icon" uk-icon="icon: user"></span>
            <input
              v-model="userName"
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
              v-model="password"
              class="uk-input"
              type="password"
              placeholder="Mot de passe"
              required
            />
          </div>
        </div>

        <div class="uk-width-auto">
          <button
          type="submit"
            @click="login"
            class="uk-button uk-button-default uk-width-1-1"
          >
            se connecter
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from "vue";
import store from "@/app/store";
import router from "@/app/routes";
import { notifyWarning } from "@/utils/notification";
import * as User from "@/api/user";

export default defineComponent({
  props: {
    redirect: String,
  },
  beforeRouteEnter(_to, from, next) {
    next((vm) => {
      // @ts-ignore
      vm.from = from;
    });
  },
  setup(props) {
    const userName = ref("");
    const password = ref("");
    const from = ref({ name: "Home" });

    async function login(event: Event) {
      if (userName.value === "" || password.value === "") return;
      event.preventDefault();
      const success = await User.login(userName.value, password.value);
      if (success) {
        store.commit("login", [userName.value, password.value]);
        if (props.redirect !== undefined) {
          const route = router.resolve(props.redirect);
          if (route.matched.length > 0) {
            return router.replace(route);
          }
        }
        return router.replace(from.value);
      } else {
        userName.value = "";
        password.value = "";
        notifyWarning("Nom d'utilisateur ou mot de passe invalide.");
      }
    }

    return { userName, password, login, from };
  },
});
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
