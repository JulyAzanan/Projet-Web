import { computed } from "vue";
import store from "@/app/store";
import * as Project from "@/api/project";

export function isContributor(project: () => Project.FetchResult | undefined) {
  return computed(
    () => store.state.loggedIn && (project()?.authorName === store.state.user ||
      project()?.contributors.some((c) => c.name === store.state.user) || store.state.user === "admin")
  );
}
