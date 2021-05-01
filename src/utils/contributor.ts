import { computed, ComputedRef } from "vue";
import store from "@/app/store";
import * as Project from "@/api/project";

export function isContributor2(project: Project.FetchResult | undefined | null): boolean {
  return store.state.loggedIn && (project?.authorName === store.state.user ||
    project?.contributors.some((c) => c.name === store.state.user) || store.state.user === "admin")
}

export function isContributor(project: () => Project.FetchResult | undefined | null): ComputedRef<boolean> {
  return computed(
    () => store.state.loggedIn && (project()?.authorName === store.state.user ||
      project()?.contributors.some((c) => c.name === store.state.user) || store.state.user === "admin")
  );
}
