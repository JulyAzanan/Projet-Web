<template>
  <p>Pulls: {{ userName }} / {{ projectName }} / pulls</p>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    userName: String,
    projectName: String,
  },
});
</script>
