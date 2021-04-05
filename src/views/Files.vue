<template>
  The branch is {{ username }} / {{ project }} / {{ branch }} / {{ commit }} / files
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    username: String,
    project: String,
    branch: String,
    commit: String,
  },
});
</script>
