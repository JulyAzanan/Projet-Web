<template>
  The branch is {{ user }} / {{ project }} / {{ branch }}
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    user: String,
    project: String,
    branch: String,
  },
});
</script>
