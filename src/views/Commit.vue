<template>
  <div class="uk-overflow-auto">
    <div class="uk-alert-primary" uk-alert>
      <div class="uk-child-width-auto" uk-grid>
        <div class="uk-width-expand">
          <router-link
            :to="{ name: 'User', params: { userName } }"
            class="uk-margin-small-right"
          >
            <img
              :src="`https://picsum.photos/seed/${userName}/200/300`"
              :alt="userName"
              :uk-tooltip="`title: ${userName}; pos: bottom`"
              class="rounded"
            />
          </router-link>
          <strong>Steel</strong> Message de commit
        </div>
        <div>
          <router-link
            :to="{
              name: 'Commit',
              params: { userName, projectName, branchName, commitID: '8f5e91' },
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
    userName: String,
    projectName: String,
    branchName: String,
    commitID: String,
  },
  setup(props) {
    if (props.commitID == null) {
      router.push({
        name: "Files",
        params: {
          userName: props.userName ?? "Steel",
          projectName: props.projectName ?? "daft_punk",
          branchName: props.branchName ?? "main",
          commitID: "8f5e91",
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
