<template>
  <div class="uk-overflow-auto">
    <div class="uk-alert-primary" uk-alert>
      <div class="uk-child-width-auto" uk-grid>
        <div class="uk-width-expand">
          <router-link
            :to="{ name: 'User', params: { username } }"
            class="uk-margin-small-right"
          >
            <img
              :src="`https://picsum.photos/seed/${username}/200/300`"
              :alt="username"
              :uk-tooltip="`title: ${username}; pos: bottom`"
              class="rounded"
            />
          </router-link>
          <strong>Steel</strong> Message de commit
        </div>
        <div>
          <router-link
            :to="{
              name: 'Commit',
              params: { username, project, branch, commit: '8f5e91' },
            }"
          >
            <code>8f5e91</code>
          </router-link>
          5 Février 2035
        </div>
      </div>
    </div>
    <router-view></router-view>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import router from "@/routes";

export default defineComponent({
  props: {
    username: String,
    project: String,
    branch: String,
    commit: String,
  },
  setup(props) {
    console.log("commit", props);
    if (props.commit == null) {
      router.push({
        name: "Commit",
        params: {
          username: props.username ?? "Steel",
          project: props.project ?? "daft_punk",
          branch: props.branch ?? "main",
          commit: "8f5e91",
        },
      });
    }
  },
});
</script>

<style lang="scss" scoped>
img {
  &.rounded {
    object-fit: cover;
    border-radius: 50%;
    height: 30px;
    width: 30px;
  }
}
</style>
