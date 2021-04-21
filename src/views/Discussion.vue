<template>
  <p>
    The project is {{ userName }} / {{ projectName }} / {{ discussion }}
  </p>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    userName: String,
    projectName: String,
    discussion: String,
  },
});
</script>
