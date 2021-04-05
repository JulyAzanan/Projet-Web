<template>
  <div>
    <h3>{{ filepath }}</h3>
    <p><mark>TODO: affichage partition</mark></p>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    username: String,
    project: String,
    branch: String,
    commit: String,
    filepath: String,
  },
  setup() {
    console.log("file");
  },
});
</script>
