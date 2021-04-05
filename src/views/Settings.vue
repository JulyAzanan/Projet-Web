<template>
  The settings is settings
  <h2>Bienvenue {{ username }} !</h2>
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
      <input class="uk-input" type="email" :placeholder="email" id="cMail" />
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
      <button class="uk-form-icon" href="#" uk-icon="icon: pencil"></button>
      <input class="uk-input" type="text" placeholder="Entrez votre âge." />
      <!-- TODO : modifier l'âge quand on clique sur le petit icon crayon -->
    </div>
    <div v-show="modifyAge" class="uk-inline">
      <button class="uk-form-icon" href="#" uk-icon="icon: pencil"></button>
      <input class="uk-input" type="text" placeholder="Entrez votre âge." />
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
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {},
});
</script>
