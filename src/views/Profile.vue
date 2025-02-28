<template>
  <div class="uk-section uk-section-default uk-section-small">
    <div class="uk-container">
      <div uk-grid class="uk-margin-medium-bottom">
        <div class="uk-width-1-3@s uk-margin-medium-right">
          <div v-if="ready">
            <div class="uk-text-center">
              <div
                class="uk-inline-clip uk-transition-toggle uk-light"
                tabindex="0"
                uk-form-custom
              >
                <input
                  @change="changePicture"
                  type="file"
                  accept="image/x-png,image/gif,image/jpeg"
                />
                <UserPicture :user="user" :size="15" />
                <div class="uk-position-center">
                  <span
                    class="uk-transition-fade"
                    uk-icon="icon: upload; ratio: 5"
                  ></span>
                </div>
              </div>
            </div>
            <h2 class="uk-text-center">
              {{ user.name }}
            </h2>
            <hr class="uk-divider-icon" />
            <div>
              <form class="uk-form-horizontal">
                <div class="uk-margin">
                  <label class="uk-form-label" for="form-h-text">Email</label>
                  <div class="uk-form-controls">
                    <input
                      v-model="user.email"
                      @input="checkEmail"
                      :class="{ 'uk-form-danger': invalidEmail }"
                      class="uk-input uk-form-width-large"
                      type="email"
                      autofocus
                      placeholder="email@example.com"
                    />
                  </div>
                </div>

                <div class="uk-margin">
                  <label class="uk-form-label" for="form-h-text">Age</label>
                  <div class="uk-form-controls">
                    <input
                      v-model="user.age"
                      class="uk-input uk-form-width-large"
                      type="number"
                      placeholder="42"
                    />
                  </div>
                </div>

                <div class="uk-margin">
                  <label class="uk-form-label" for="form-h-textarea">Bio</label>
                  <div class="uk-form-controls">
                    <textarea
                      v-model="user.bio"
                      class="uk-textarea uk-form-width-large"
                      rows="5"
                      placeholder="Quelques mots..."
                    ></textarea>
                  </div>
                </div>

                <div class="uk-margin">
                  <label class="uk-form-label" for="form-h-text"
                    >Mot de passe</label
                  >
                  <div class="uk-form-controls">
                    <input
                      v-model="newPassword"
                      class="uk-input uk-form-width-large"
                      type="password"
                      placeholder="Nouveau mot de passe"
                    />
                  </div>
                </div>

                <div class="uk-margin">
                  <div class="uk-form-controls">
                    <button
                      type="button"
                      @click="updateAccount"
                      class="uk-button uk-button-primary uk-width-1-1"
                      :disabled="invalidEmail"
                    >
                      Mettre à jour
                    </button>
                  </div>
                </div>

                <div class="uk-margin">
                  <div class="uk-form-controls">
                    <a
                      class="uk-button uk-button-danger uk-width-1-1"
                      href="#delete-account"
                      uk-toggle
                      >Supprimer le compte</a
                    >
                  </div>
                </div>
              </form>
            </div>
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
                class="uk-width-auto"
              />
            </div>
            <Pagination :page="parseInt(page)" :pages="pages" />
          </div>
          <div v-else uk-spinner></div>
        </div>
      </div>
    </div>
    <div id="delete-account" uk-modal>
      <div class="uk-modal-dialog uk-modal-body">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <h2 class="uk-modal-title">Attention</h2>
        <p>
          Vous êtes sur le point de supprimer votre compte. Cette action est
          irréversible, êtes vous sûr de vouloir poursuivre ?
        </p>
        <p class="uk-text-right">
          <button
            class="uk-button uk-button-default uk-modal-close uk-margin-small-right"
            type="button"
          >
            Annuler
          </button>
          <button
            class="uk-button uk-button-danger uk-modal-close"
            type="button"
            @click="deleteAccount"
          >
            Supprimer le compte
          </button>
        </p>
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
import debounce from "@/utils/debounce";
import { notifySuccess, notifyWarning } from "@/utils/notification";

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
    const newPassword = ref<string | null>("");
    const invalidEmail = ref(false);

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
      (loggedIn) => loggedIn || router.replace({ name: "Login" })
    );

    async function init() {
      const page = parseInt(props.page!);
      const result = await User.profile(page);
      if (result === null) return notFound();
      user.value = result;
      if (result.following.length === 0 && page != 1) {
        router.replace({ query: { page: "1" } });
      }
      pages.value = Math.ceil(result.followingCount / Friend.friendPerPage);
      friendLoaded.value = true;
      ready.value = true;
    }

    function changePicture(event: InputEvent) {
      const element = event.currentTarget as HTMLInputElement;
      const fileList = element.files;
      if (fileList?.length === 0) return;
      const file = fileList?.item(0) as File;
      const reader = new FileReader();
      reader.readAsDataURL(file);
      reader.onloadend = async () => {
        const content = reader.result as string;
        await User.edit(store.state.user, {
          picture: content,
        });
        user.value.picture = content;
        notifySuccess("Image de profil mise à jour");
      };
    }

    async function updateAccount() {
      const success = User.edit(store.state.user, {
        email: user.value.email,
        age: user.value.age,
        bio: user.value.bio,
        password: newPassword.value === "" ? null : newPassword.value,
      });
      if (newPassword.value) localStorage.setItem("password", newPassword.value);
      if (success) notifySuccess("Profil mis à jour");
      else notifyWarning("Erreur lors de la mise à jour du profil");
    }

    async function deleteAccount() {
      await User.remove(store.state.user);
      store.commit("logout");
    }

    async function emailExists() {
      if (user.value.email) {
        invalidEmail.value = await User.findByEmail(user.value.email);
      } else {
        invalidEmail.value = false;
      }
    }

    const checkEmail = debounce(emailExists, 500);

    init();

    return {
      ready,
      user,
      friendLoaded,
      pages,
      changePicture,
      newPassword,
      updateAccount,
      deleteAccount,
      checkEmail,
      invalidEmail,
    };
  },
});
</script>

<style lang="scss" scoped>
.uk-form-label {
  width: auto;
}

.uk-form-controls {
  margin-left: 8em;
}

.edit-marker {
  right: 5%;
  bottom: 0%;
}
</style>
