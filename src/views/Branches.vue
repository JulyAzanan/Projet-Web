<template>
  <div class="uk-section uk-section-default uk-section-small">
    <div class="uk-container uk-container-xsmall">
      <form class="uk-display-block uk-form-stacked">
        <div class="uk-child-width-auto" uk-grid>
          <div class="uk-width-expand uk-margin-small-right">
            <input
              v-model="branchName"
              @input="checkBranch"
              :class="{ 'uk-form-danger': invalidBranch }"
              class="uk-input"
              type="text"
              placeholder="Nom de la branche"
              required
            />
          </div>
          <button
            class="uk-button uk-button-primary"
            type="submit"
            @click="createBranch"
          >
            Ajouter un branche
          </button>
        </div>
      </form>
      <div v-for="branch in project.branches" :key="branch.name" uk-alert>
        <div class="uk-child-width-auto" uk-grid>
          <strong class="uk-width-expand">{{ branch.name }}</strong>
          <span v-if="branch.name === project.mainBranch" class="uk-label">
            Branche principale
          </span>
        </div>
        <a
          v-if="branch.name !== project.mainBranch"
          @click="removeBranch(branch.name)"
          class="uk-alert-close"
          uk-close
        ></a>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from "vue";
import * as Branch from "@/api/branch";
import * as Project from "@/api/project";
import router from "@/app/routes";
import { notifySuccess, notifyWarning } from "@/utils/notification";
import debounce from "@/utils/debounce";

export default defineComponent({
  props: {
    userName: String,
    projectName: String,
    project: Object as () => Project.FetchResult,
  },
  setup(props) {
    const branchName = ref("");
    const invalidBranch = ref(false);
    const branches = ref(props.project?.branches ?? []);

    async function createBranch(event: Event) {
      if (branchName.value === "") return;
      event.preventDefault();
      const success = Branch.add(
        props.userName!,
        props.projectName!,
        branchName.value
      );
      if (success) {
        branches.value.push({ name: branchName.value, updatedAt: new Date() });
        branchName.value = "";
        notifySuccess("Branche ajoutée");
      } else notifyWarning("Erreur lors de l'ajout de la branche");
    }

    function removeBranch(branch: string) {
      const success = Branch.remove(
        props.userName!,
        props.projectName!,
        branch,
      );
      if (success) {
        for (let i = 0; i < branches.value.length; i++) {
          if (branches.value[i].name === branch) {
            branches.value.splice(i, 1);
          }
        }
        notifySuccess("Branche supprimée");
      } else notifyWarning("Erreur lors de la suppression de la branche");
    }

    async function branchExists() {
      invalidBranch.value = await Branch.find(
        props.userName!,
        props.projectName!,
        branchName.value
      );
    }

    const checkBranch = debounce(branchExists, 500);

    return {
      createBranch,
      checkBranch,
      branchName,
      invalidBranch,
      removeBranch,
    };
  },
});
</script>

<style lang="scss" scoped>
.uk-label {
  padding-left: 10px;
}
</style>
