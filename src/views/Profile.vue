<template>
  <div class="uk-section uk-section-default uk-section-small">
    <div class="uk-container">
      <div uk-grid class="uk-margin-medium-bottom">
        <div class="uk-width-1-3@s uk-margin-medium-right">
          <h2 class="uk-text-center">
            Modifier les informations de mon profil
          </h2>
          <button class="uk-button uk-button-text">
            <img
              :src="`https://picsum.photos/seed/${userName}/500/500`"
              :alt="name"
              uk-tooltip="title: Modifier ma photo de profil; pos: top'"
              class="rounded"
            />
          </button>
          <div>
            <span class="uk-margin-small-right" uk-icon="icon: user"></span>
            {{ userName }}
          </div>
          <!-- TODO : afficher une liste des amis quand on clique sur le bouton -->
          <div>
            <button
              class="uk-button uk-button-text buttonNormalText"
              v-on:click="modifyEmail = !modifyEmail"
              v-if="email"
              uk-tooltip="title: Modifier mon adresse email.; pos: top"
            >
              <span class="uk-margin-small-right" uk-icon="icon: mail"></span>
              Email actuel : {{ email }}
            </button>
            <div v-if="!email || modifyEmail">
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
                  id="cMail"
                />
                <!-- TODO : modifier l'email quand on clique sur le petit icon crayon -->
              </div>
            </div>
          </div>
          <hr class="uk-divider-icon" />
          <div>
            <button
              v-if="age > 0"
              class="uk-button uk-button-text buttonNormalText"
              v-on:click="modifyAge = !modifyAge"
              uk-tooltip="title: Modifier mon âge.; pos: top"
            >
              <span
                class="uk-margin-small-right"
                uk-icon="icon: calendar"
              ></span>
              {{ age }} ans
            </button>
            <div v-if="!age || modifyAge">
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
                <!-- TODO : modifier l'âge quand on clique sur le petit icon crayon -->
              </div>
            </div>
          </div>
          <button
            v-if="age > 0"
            class="uk-button uk-button-text buttonNormalText"
            v-on:click="modifyPassword = !modifyPassword"
          >
            Modifier mon mot de passe
          </button>
          <div v-if="modifyPassword" class="uk-margin-small-top">
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
          </div>
        </div>
        <div
          class="uk-width-2-3@s uk-container uk-margin-small-top uk-position-relative"
          uk-grid
        >
          <h2 class="uk-text-center">Mes amis</h2>
          <div
            class="uk-grid-column-small uk-grid-row-small uk-child-width-1-4@s uk-text-center uk-margin-medium-bottom"
            uk-grid
          >
            <FriendCard :userName="'July'" :followers="3615" />
            <FriendCard :userName="'foo'" :followers="2" />
            <FriendCard :userName="'bar'" :followers="1" />
            <FriendCard :userName="'Annie'" :followers="42" />
            <FriendCard :userName="'Elaim'" :followers="37" />
            <FriendCard :userName="'gperdu'" :followers="354" />
            <FriendCard :userName="'salut'" :followers="5" />
            <FriendCard :userName="'toi'" :followers="225" />
            <FriendCard :userName="'keur'" :followers="22326" />
          </div>
          <ul class="uk-pagination uk-position-bottom-center" uk-margin>
            <li class="uk-disabled">
              <a><span uk-pagination-previous></span></a>
            </li>
            <li class="uk-active"><span>1</span></li>
            <li><a href="#">2</a></li>
            <li><a href="#">3</a></li>
            <li><a href="#">4</a></li>
            <li><a href="#">5</a></li>
            <li class="uk-disabled"><span>...</span></li>
            <li><a href="#">8</a></li>
            <li><a href="#">9</a></li>
            <li><a href="#">10</a></li>
            <li class="uk-disabled"><span>...</span></li>
            <li><a href="#">20</a></li>
            <li>
              <a href="#"><span uk-pagination-next></span></a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, watch } from "vue";
import store from "@/app/store";
import router from "@/app/routes";
import FriendCard from "@/components/User/FriendCard.vue";

export default defineComponent({
  props: {
    userName: String,
    // email: String,
  },
  components: {
    FriendCard,
  },
  beforeRouteEnter(to, from, next) {
    if (store.state.loggedIn) next();
    else next({ name: "Login", query: { redirect: to.fullPath } });
  },
  setup() {
    const email = "test@example.com";
    const modifyEmail = ref(false);
    const modifyAge = ref(false);
    const modifyPassword = ref(false);
    const age = 20;

    watch(
      () => store.state.loggedIn,
      () => {
        router.replace({
          name: "Login",
          query: { redirect: router.currentRoute.value.fullPath },
        });
      }
    );
    
    return {
      email,
      modifyEmail,
      age,
      modifyAge,
      modifyPassword,
    };
  },
});
</script>

<style lang="scss" scoped>
img {
  &.rounded {
    object-fit: cover;
    border-radius: 50%;
    height: 200px;
    width: 200px;
  }
}
</style>
