<template>
  <div>
    <HomeNotLogged v-if="!logged"/>
    <HomeLogged v-else/>
  </div>
</template>

<script lang="ts">
import { defineComponent, computed } from 'vue';
import store from "@/app/store";
import HomeNotLogged from '@/components/HomeNotLogged.vue'; // @ is an alias to /src
import HomeLogged from '@/components/HomeLogged.vue';

export default defineComponent({
  components: {
    HomeNotLogged,
    HomeLogged
  },
  setup() {
    return {
      logged: computed(() => store.state.loggedIn),
      userName: computed(() => store.state.user),
    };
  },
});
</script>
