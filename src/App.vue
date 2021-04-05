<template>
  <Navbar/>
  <div uk-height-viewport="expand: true">
    <router-view />
  </div>
  <Footer/>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import Footer from "@/components/App/Footer.vue";
import Navbar from "@/components/App/Navbar.vue";

export default defineComponent({
  components: {
    Footer,
    Navbar,
  }
});
</script>

<style lang="scss">
@import "@/styles/variables.scss";
</style>
