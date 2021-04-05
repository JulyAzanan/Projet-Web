<template>
  <div class="uk-section uk-section-default uk-section-small">
    <div class="uk-container">
      <h3 v-if="ready">
        <router-link :to="{ name: 'User', params: { username } }">
          {{ username }}
        </router-link>
        /
        <router-link
          :to="{ name: 'Branch-default', params: { username, project } }"
        >
          {{ project }}
        </router-link>
      </h3>
      <div v-else uk-spinner></div>
      <div>
        <ul class="uk-tab" uk-switcher="animation: uk-animation-fade">
          <li>
            <router-link
              :to="{ name: 'Branch-default', params: { username, project } }"
              >Partitions</router-link
            >
          </li>
          <li>
            <router-link :to="{ name: 'Pulls', params: { username, project } }"
              >Changements
            </router-link>
          </li>
          <li>
            <router-link
              :to="{ name: 'Discussions', params: { username, project } }"
              >Discussions
            </router-link>
          </li>
        </ul>

        <router-view v-if="ready"></router-view>
        <div v-else uk-spinner></div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from "vue";
import { notFound } from "@/routes";
import * as Project from "@/api/project";

export default defineComponent({
  props: {
    username: String,
    project: String,
  },
  setup(props) {
    const ready = ref(true);
    Project.exists(props.username, props.project).then((exists) => {
      if (exists) ready.value = true;
      else notFound();
    });
    return { ready };
  },
});
</script>
