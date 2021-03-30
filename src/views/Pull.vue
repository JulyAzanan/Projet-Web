<template>
  The project is {{ user }} / {{ project }} / {{ pull }}
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    user: String,
    project: String,
    pull: String
  },
});
</script>
