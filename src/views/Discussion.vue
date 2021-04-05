<template>
  The project is {{ username }} / {{ project }} / {{ discussion }}
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    username: String,
    project: String,
    discussion: String,
  },
});
</script>
