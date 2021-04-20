<template>
  <div class="uk-section uk-section-default uk-section-small">
    <div class="uk-container">
      <div uk-grid class="uk-margin-medium-bottom">
        <div class="uk-width-1-3@s uk-margin-large-top" uk-first-column>
          <h2>Profil de {{ userName }}</h2>
          <img
            :src="`https://picsum.photos/seed/${userName}/500/500`"
            :alt="userName"
            class="rounded"
          />
          <div>
            <button class="uk-button uk-button-text buttonNormalText">
              <span class="uk-margin-small-right" uk-icon="icon: star"></span>
              {{ friendAmount }} abonnés
            </button>
          </div>
          <!-- TODO : afficher une liste des amis quand on clique sur le bouton -->
          <div v-if="email">
            <span class="uk-margin-small-right" uk-icon="icon: mail"> </span>
            {{ email }}
          </div>
          <hr class="uk-divider-icon uk-margin-large-right" />
          <div v-if="age">
            <span class="uk-margin-small-right" uk-icon="icon: calendar"></span>
            {{ age }} ans
          </div>
        </div>
        <div
          class="uk-width-2-3@s uk-container uk-margin-small-top uk-position-relative"
          uk-grid
        >
          <h2>Projets récents</h2>
          <div
            class="uk-grid-column-small uk-grid-row-small uk-child-width-1-3@s uk-text-center uk-margin-medium-bottom"
            uk-grid
          >
            <ProjectCard
              :priv="true"
              :projectName="'Nier: Automata'"
              :userName="userName"
              :author="userName"
              :updatedAt="'04/04/2021'"
              :branch="'main'"
            />
            <ProjectCard
              :priv="true"
              :projectName="'Daft Punk'"
              :userName="userName"
              :author="userName"
              :updatedAt="'04/04/2021'"
              :branch="'master'"
            />
            <ProjectCard
              :priv="false"
              :projectName="'Tu'"
              :userName="userName"
              :author="userName"
              :updatedAt="'04/04/2021'"
              :branch="'branch1'"
            />
            <ProjectCard
              :priv="false"
              :projectName="'Tournes'"
              :userName="userName"
              :author="userName"
              :updatedAt="'04/04/2021'"
              :branch="'main'"
            />
            <ProjectCard
              :priv="false"
              :projectName="'Joyeuses'"
              :userName="userName"
              :author="userName"
              :updatedAt="'04/04/2021'"
              :branch="'ahahah'"
            />
            <ProjectCard
              :priv="false"
              :projectName="'Pâques'"
              :userName="userName"
              :author="userName"
              :updatedAt="'02/04/2021'"
              :branch="'Liora'"
            />
            <ProjectCard
              :priv="false"
              :projectName="'foo'"
              :userName="userName"
              :author="userName"
              :updatedAt="'05/04/2021'"
              :branch="'vitalité'"
            />
            <ProjectCard
              :priv="false"
              :projectName="'bar'"
              :userName="userName"
              :author="userName"
              :updatedAt="'01/04/2021'"
              :branch="'main'"
            />
            <ProjectCard
              :priv="false"
              :projectName="'nya'"
              :userName="userName"
              :author="userName"
              :updatedAt="'02/04/2021'"
              :branch="'main'"
            />
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
          <!-- <h2>Activités de mes amis</h2>
        <div
          class="uk-grid-column-small uk-grid-row-small uk-child-width-1-3@s uk-text-center"
          uk-grid
        >
          Générer quelques cartes de manière random, selon les projets récents des amis. Adapter si pas d'amis
          <ProjectCard />
          <ProjectCard />
          <ProjectCard />
          <ProjectCard />
          <ProjectCard />
          <ProjectCard />
          <ProjectCard />
          <ProjectCard />
          <ProjectCard />
        </div>
        <p></p> -->
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from "vue";
import ProjectCard from "@/components/User/ProjectCard.vue";

export default defineComponent({
  props: {
    userName: String,
    page: String,
    // friendAmount: Number,
    // age: Number,
  },
  components: {
    ProjectCard,
  },
  setup() {
    const friendAmount = 3615;
    const email = "maël@tutournes.troll";
    const age = 69;
    const modifyAge = ref(false);
    const modifyPassword = ref(false);

    function changeMail() {
      /* let mail = document.getElementById("cMail");
      this.email = mail; */
    }

    return {
      friendAmount,
      email,
      age,
      changeMail,
      modifyAge,
      modifyPassword,
    };
  },
});
</script>

<style lang="scss" scoped>
.buttonNormalText {
  text-transform: none;
}

.cardHolder {
  display: flex;
}

.userInfo {
  margin-top: 10em;
}

img {
  &.rounded {
    object-fit: cover;
    border-radius: 50%;
    height: 200px;
    width: 200px;
  }
}
</style>