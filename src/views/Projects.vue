<template>
  <div class="uk-section uk-section-default uk-section-small">
    <div class="uk-container uk-margin-medium-bottom">
      <h2>Liste des projets</h2>
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
                placeholder="Rechercher un projet..."
                id="cMail"
              />
            </div>
          </div>
          <div class="uk-container uk-margin-large-top uk-inline" uk-grid>
            <div
              class="uk-grid-column-small uk-child-width-1-4@s uk-text-center uk-margin-medium-bottom"
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
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import ProjectCard from "@/components/User/ProjectCard.vue";

export default defineComponent({
  props: {
    page: String,
  },
  components: {
    ProjectCard,
  },
});
</script>

<style lang="scss" scoped>
</style>