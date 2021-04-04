<template>
  <div class="uk-container">
    <div uk-grid>
      <div class="uk-width-1-3@s" uk-first-column>
        <h2>Bienvenue {{ user }} !</h2>
        <button class="uk-button uk-button-text buttonNormalText">
          <span class="uk-margin-small-right" uk-icon="icon: user"></span>
          {{ friendAmount }} amis
        </button>
        <!-- TODO : afficher une liste des amis quand on clique sur le bouton -->
        <div>
          <div class="uk-inline">
            <button
              class="uk-form-icon"
              uk-icon="icon: pencil"
              v-on:click="changeMail()"
            ></button>
            <input
              class="uk-input"
              type="email"
              :placeholder="email"
              id="cMail"
            />
            <!-- TODO : modifier l'email quand on clique sur le petit icon crayon -->
          </div>
        </div>
        <hr class="uk-divider-icon" />
        <div>
          <button
            v-if="age > 0"
            class="uk-button uk-button-text buttonNormalText"
            v-on:click="modifyAge = !modifyAge"
          >
            <span class="uk-margin-small-right" uk-icon="icon: calendar"></span>
            {{ age }} ans
          </button>
          <div v-else class="uk-inline">
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
          <div v-show="modifyAge" class="uk-inline">
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
        <button
          v-if="age > 0"
          class="uk-button uk-button-text buttonNormalText"
          v-on:click="modifyPassword = !modifyPassword"
        >
          Modifier le mot de passe
        </button>
        <div v-show="modifyPassword">
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
      <div class="uk-width-2-3@s uk-container" uk-grid>
        <h2>Projets récents</h2>
        <div
          class="uk-grid-column-small uk-grid-row-small uk-child-width-1-3@s uk-text-center"
          uk-grid
        >
          <ProjectCard
            v-bind:priv="true"
            v-bind:project="'Nier: Automata'"
            v-bind:username="user"
            v-bind:author="user"
            v-bind:updatedAt="'04/04/2021'"
            v-bind:branch="'main'"
          />
          <ProjectCard
            v-bind:priv="true"
            v-bind:project="'Daft Punk'"
            v-bind:username="user"
            v-bind:author="user"
            v-bind:updatedAt="'04/04/2021'"
            v-bind:branch="'master'"
          />
          <ProjectCard
            v-bind:priv="false"
            v-bind:project="'Tu'"
            v-bind:username="user"
            v-bind:author="user"
            v-bind:updatedAt="'04/04/2021'"
            v-bind:branch="'branch1'"
          />
          <ProjectCard
            v-bind:priv="false"
            v-bind:project="'Tournes'"
            v-bind:username="user"
            v-bind:author="user"
            v-bind:updatedAt="'04/04/2021'"
            v-bind:branch="'main'"
          />
          <ProjectCard
            v-bind:priv="false"
            v-bind:project="'Joyeuses'"
            v-bind:username="user"
            v-bind:author="user"
            v-bind:updatedAt="'04/04/2021'"
            v-bind:branch="'ahahah'"
          />
          <ProjectCard
            v-bind:priv="false"
            v-bind:project="'Pâques'"
            v-bind:username="user"
            v-bind:author="user"
            v-bind:updatedAt="'02/04/2021'"
            v-bind:branch="'Liora'"
          />
          <ProjectCard
            v-bind:priv="false"
            v-bind:project="'foo'"
            v-bind:username="user"
            v-bind:author="user"
            v-bind:updatedAt="'05/04/2021'"
            v-bind:branch="'vitalité'"
          />
          <ProjectCard
            v-bind:priv="false"
            v-bind:project="'bar'"
            v-bind:username="user"
            v-bind:author="user"
            v-bind:updatedAt="'01/04/2021'"
            v-bind:branch="'main'"
          />
          <ProjectCard
            v-bind:priv="false"
            v-bind:project="'nya'"
            v-bind:username="user"
            v-bind:author="user"
            v-bind:updatedAt="'02/04/2021'"
            v-bind:branch="'main'"
          />
        </div>
        <h2>Activités de mes amis</h2>
        <div
          class="uk-grid-column-small uk-grid-row-small uk-child-width-1-3@s uk-text-center"
          uk-grid
        >
          <!-- Générer quelques cartes de manière random, selon les projets récents des amis. Adapter si pas d'amis -->
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
        <p></p>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import ProjectCard from "@/components/App/ProjectCard.vue";

export default defineComponent({
  props: {
    user: String,
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
    return {
      friendAmount,
      email,
      age,
    };
  },
  methods: {
    changeMail: function () {
      /* let mail = document.getElementById("cMail");
      this.email = mail; */
    },
  },
  data() {
    return {
      modifyAge: false,
      modifyPassword: false,
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
</style>