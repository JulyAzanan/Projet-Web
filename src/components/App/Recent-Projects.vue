<template>
  <li v-for="name in publicProjects" :key="name">
    <a href="#">
      <span class="uk-margin-small-right" uk-icon="icon: folder"></span>
      {{ name }}
    </a>
  </li>

  <li
    v-if="publicProjects.length > 0 && privateProjects.length > 0"
    class="uk-nav-divider"
  ></li>

  <li v-for="name in privateProjects" :key="name">
    <a href="#">
      <span class="uk-margin-small-right" uk-icon="icon: lock"></span>
      {{ name }}
    </a>
  </li>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  setup() {
    // TODO
    const publicProjects: string[] = [
      "Projet 3615",
      "Nier: Automata",
      "Daft Punk",
    ];
    const privateProjects: string[] = ["Foo", "Bar"];
    return {
      publicProjects,
      privateProjects,
    };
  },
});
</script>
