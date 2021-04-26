<template>
  <table class="uk-table uk-table-divider uk-table-hover">
    <tbody>
      <tr v-for="file in commit.files" :key="file.path">
        <td><input class="uk-checkbox" type="checkbox" /></td>
        <td class="uk-table-link">
          <router-link
            :to="{
              name: 'File',
              params: {
                userName,
                projectName,
                branchName,
                commitID,
                filePath: file.path,
              },
            }"
          >
            {{ file.path }}
          </router-link>
        </td>
        <td>
          <router-link
            :to="{
              name: 'Files',
              params: {
                userName,
                projectName,
                branchName,
                commitID: file.id,
              },
            }"
          >
            {{ file.message }}
          </router-link>
        </td>
        <td class="uk-text-nowrap">{{ file.createdAt.toLocaleString() }}</td>
      </tr>
    </tbody>
  </table>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import * as Commit from "@/api/commit";

export default defineComponent({
  props: {
    userName: String,
    projectName: String,
    branchName: String,
    commitID: String,
    commit: Object as () => Commit.FetchResult,
  },
  setup() {
    //
  },
});
</script>
