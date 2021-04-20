<template>
  <div>
    <h3>{{ filePath }}</h3>
    <p><mark>TODO: affichage partition</mark></p>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    userName: String,
    projectName: String,
    branchName: String,
    commitID: String,
    filePath: String,
  },
  setup() {
    console.log("file");
  },
});
</script>
