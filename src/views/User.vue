<template>
  The user is {{ user }}
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    user: String,
  },
});
</script>
