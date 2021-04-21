<template>
  <div class="uk-section uk-section-default uk-section-small">
    <div class="uk-container uk-margin-medium-bottom">
      <h2>Liste des utilisateurs</h2>
      <div uk-grid class="uk-display-block uk-inline">
        <div>
          <div class="uk-position-top-center">
            <div class="uk-inline">
              <button
                class="uk-form-icon"
                uk-icon="icon: search"
                v-on:click="changeMail()"
              ></button>
              <input
                class="uk-input uk-form-width-large"
                type="text"
                placeholder="Rechercher un utilisateur..."
                id="cMail"
              />
            </div>
          </div>
          <div class="uk-container uk-margin-large-top uk-inline" uk-grid>
            <div
              class="uk-grid-column-small uk-child-width-1-6@s uk-text-center uk-margin-medium-bottom"
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
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import FriendCard from "@/components/User/FriendCard.vue";

export default defineComponent({
  props: {
    page: String,
  },
  components: {
    FriendCard,
  },
});
</script>

<style lang="scss" scoped>
</style>