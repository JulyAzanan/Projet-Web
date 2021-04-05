<template>
  <div class="uk-section uk-section-default uk-section-small">
    <div class="uk-container">
      <h3>
        <router-link :to="{ name: 'User', params: { username } }">
          {{ username }}
        </router-link>
        /
        <router-link :to="{ name: 'Project', params: { username, project } }">
          {{ project }}
        </router-link>
      </h3>
      <div>
        <ul class="uk-tab" uk-switcher="animation: uk-animation-fade">
          <li>
            <router-link
              :to="{
                name: 'Branch',
                params: { username, project, branch: null },
              }"
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
          <li class="uk-disabled"><a>Disabled</a></li>
        </ul>

        <router-view></router-view>
      </div>
    </div>
    <div class="uk-container">
      <div uk-grid>
        <div class="uk-width-2-3@m"></div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    username: String,
    project: String,
  },
});
</script>
