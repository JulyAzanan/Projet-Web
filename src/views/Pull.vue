<template>
  The project is {{ userName }} / {{ projectName }} / {{ pull }}
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    userName: String,
    projectName: String,
    pull: String
  },
});
</script>
