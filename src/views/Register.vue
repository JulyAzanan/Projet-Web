<template>
  <div class="background">
    <div class="uk-card uk-card-default uk-card-body uk-position-center">
      <h3 class="uk-card-title">S'enregistrer</h3>
      <form class="uk-form-stacked">
        <div class="uk-margin">
          <div class="uk-inline">
            <span class="uk-form-icon" uk-icon="icon: user"></span>
            <input
              v-model="userName"
              class="uk-input"
              :class="{ 'uk-form-danger': invalidUser }"
              type="text"
              placeholder="Nom d'utilisateur*"
              required
              autofocus
              @input="checkUser"
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
              placeholder="Mot de passe*"
              required
            />
          </div>
        </div>

        <div class="uk-margin">
          <div class="uk-inline">
            <span class="uk-form-icon" uk-icon="icon: mail"></span>
            <input
              v-model="email"
              class="uk-input"
              :class="{ 'uk-form-danger': invalidEmail }"
              @input="checkEmail"
              type="email"
              placeholder="Email"
            />
          </div>
        </div>

        <div class="uk-margin">
          <div class="uk-inline">
            <span class="uk-form-icon" uk-icon="icon: hashtag"></span>
            <input
              v-model="age"
              class="uk-input"
              type="number"
              placeholder="Age"
              min="0"
              max="100"
            />
          </div>
        </div>

        <div class="uk-width-auto">
          <button
            type="submit"
            @click="register"
            class="uk-button uk-button-primary uk-width-1-1"
            :disabled="invalidUser || invalidEmail"
          >
            Créer un compte
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
import debounce from "@/utils/debounce";

export default defineComponent({
  props: {
    redirect: String,
  },
  setup(props) {
    const userName = ref("");
    const password = ref("");
    const email = ref("");
    const age = ref("");
    const invalidUser = ref(false);
    const invalidEmail = ref(false);

    async function register(event: Event) {
      if (userName.value === "" || password.value === "") return;
      event.preventDefault();
      const success = await User.register(
        userName.value,
        password.value,
        email.value || null,
        parseInt(age.value) || null
      );
      if (success) {
        store.commit("login", [userName.value, password.value]);
        if (props.redirect !== undefined) {
          const route = router.resolve(props.redirect);
          if (route.matched.length > 0) {
            return router.replace(route);
          }
        }
        return router.replace({ name: "Home" });
      } else {
        userName.value = "";
        password.value = "";
        notifyWarning("Nom d'utilisateur indisponible.");
      }
    }

    async function userExists() {
      invalidUser.value = await User.find(userName.value);
    }

    async function emailExists() {
      invalidEmail.value = await User.findByEmail(email.value);
    }

    const checkUser = debounce(userExists, 500);
    const checkEmail = debounce(emailExists, 500);

    return {
      userName,
      password,
      email,
      age,
      register,
      invalidUser,
      invalidEmail,
      checkUser,
      checkEmail,
    };
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
