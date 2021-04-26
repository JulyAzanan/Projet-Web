<template>
  <div class="uk-section uk-section-default uk-section-small">
    <div class="uk-container">
      <div uk-grid class="uk-margin-medium-bottom">
        <div class="uk-width-1-3@s uk-margin-medium-right">
          <div v-if="ready">
            <h2 class="uk-text-center">
              Modifier les informations de mon profil
            </h2>
            <button class="uk-button uk-button-text">
              <UserPicture :user="user" :size="15" />
            </button>
            <div>
              <span class="uk-margin-small-right" uk-icon="icon: user"></span>
              {{ user.name }}
            </div>
            <!-- TODO : afficher une liste des amis quand on clique sur le bouton -->
            <div>
              <!-- v-on:click="modifyEmail = !modifyEmail" -->
              <button
                class="uk-button uk-button-text buttonNormalText"
                v-if="user.email"
                uk-tooltip="title: Modifier mon adresse email.; pos: top"
              >
                <span class="uk-margin-small-right" uk-icon="icon: mail"></span>
                Email actuel : {{ user.email }}
              </button>
              <!-- <div v-if="!user.email || modifyEmail">
                <div class="uk-inline">
                  <button
                    class="uk-form-icon"
                    uk-icon="icon: pencil"
                    v-on:click="changeMail()"
                  ></button>
                  <input
                    class="uk-input"
                    type="email"
                    placeholder="Entrer une adresse mail"
                  />
                </div>
              </div> -->
            </div>
            <hr class="uk-divider-icon" />
            <div>
              <!-- v-on:click="modifyAge = !modifyAge" -->
              <button
                v-if="user.age > 0"
                class="uk-button uk-button-text buttonNormalText"
                uk-tooltip="title: Modifier mon âge.; pos: top"
              >
                <span
                  class="uk-margin-small-right"
                  uk-icon="icon: calendar"
                ></span>
                {{ user.age }} ans
              </button>
              <!-- <div v-if="!user.age || modifyAge">
                <div class="uk-inline">
                  <button
                    class="uk-form-icon"
                    href="#"
                    uk-icon="icon: pencil"
                  ></button>
                  <input
                    class="uk-input"
                    type="text"
                    placeholder="Entrez votre âge."
                  />
                </div>
              </div> -->
            </div>
            <!-- v-on:click="modifyPassword = !modifyPassword" -->
            <button
              v-if="user.age > 0"
              class="uk-button uk-button-text buttonNormalText"
            >
              Modifier mon mot de passe
            </button>
            <!-- <div v-if="modifyPassword" class="uk-margin-small-top">
              <input
                class="uk-input"
                type="password"
                placeholder="Ancien mot de passe."
                required
              />
              <input
                class="uk-input"
                type="password"
                placeholder="Nouveau mot de passe."
                required
              />
              <button class="uk-button uk-button-default buttonNormalText">
                Confirmer
              </button>
            </div> -->
          </div>
          <div v-else uk-spinner></div>
        </div>
        <div
          class="uk-width-2-3@s uk-container uk-margin-small-top uk-position-relative"
          uk-grid
        >
          <h2 class="uk-text-center">Mes amis</h2>
          <div v-if="friendLoaded">
            <div
              class="uk-grid-column-small uk-grid-row-small uk-child-width-1-4@s uk-text-center uk-margin-medium-bottom"
              uk-grid
            >
              <UserCard
                v-for="friend in user.following"
                :key="friend.name"
                :user="friend"
              />
            </div>
            <Pagination :page="parseInt(page)" :pages="pages" />
          </div>
          <div v-else uk-spinner></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, watch } from "vue";
import * as User from "@/api/user";
import * as Friend from "@/api/friend";
import UserPicture from "@/components/User/UserPicture.vue";
import Pagination from "@/components/Pagination.vue";
import UserCard from "@/components/User/UserCard.vue";
import store from "@/app/store";
import router, { notFound } from "@/app/routes";

export default defineComponent({
  props: {
    userName: String,
    page: String,
  },
  components: {
    UserPicture,
    Pagination,
    UserCard,
  },
  beforeRouteEnter(to, _from, next) {
    if (store.state.loggedIn) next();
    else next({ name: "Login", query: { redirect: to.fullPath } });
  },
  setup(props) {
    const ready = ref(false);
    const friendLoaded = ref(false);
    const user = ref<User.ProfileResult>({
      name: "",
      following: [],
      followingCount: 0,
    });
    const pages = ref(0);

    watch(
      () => props.page,
      async () => {
        window.scrollTo(0, 0);
        friendLoaded.value = false;
        const page = parseInt(props.page!);
        const result = await Friend.all(page, store.state.user);
        if (result.length === 0 && page != 1) {
          router.replace({ query: { page: "1" } });
        } else {
          user.value.following = result;
        }
        friendLoaded.value = true;
      }
    );

    watch(
      () => store.state.loggedIn,
      () => router.replace({ name: "Login" })
    );

    async function init() {
      const page = parseInt(props.page!);
      const result = await User.profile();
      if (result === null) return notFound();
      user.value = result;
      if (result.following.length === 0 && page != 1) {
        router.replace({ query: { page: "1" } });
      }
      pages.value = Math.ceil(result.followingCount / Friend.friendPerPage);
      friendLoaded.value = true;
      ready.value = true;
    }

    init();

    return {
      ready,
      user,
      friendLoaded,
      pages,
    };
  },
});
</script>
