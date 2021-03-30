<template>
  The branch is {{ user }} / {{ project }} / {{ branch }} / {{ commit }}
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    user: String,
    project: String,
    branch: String,
    commit: String,
  },
});
</script>
