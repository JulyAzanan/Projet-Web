<template>
  <table class="uk-table uk-table-divider uk-table-hover">
    <tbody>
      <tr v-for="i in [1,2,3,4,5]" :key="i">
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
                filePath: 'fichier_3615',
              },
            }"
          >fichier_3615</router-link>
        </td>
        <td>Description random</td>
        <td class="uk-text-nowrap">Il y a 5 mois</td>
      </tr>
    </tbody>
  </table>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    userName: String,
    projectName: String,
    branchName: String,
    commitID: String,
  },
  setup(props) {
    console.log("files", props)
  }
});
</script>
