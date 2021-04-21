import sleep from "./sleep";
import * as Project from "./project"

interface BaseResult {
  email?: string,
  age?: number,
  bio?: string,
  latestCommit?: Date,
}

export interface FetchResult extends BaseResult {
  projectCount: number,
  friends: number,
  projects: Project.AllOfResult[],
}

export const projectPerPage = 6;

export async function fetch(user: string | null, page: number): Promise<FetchResult | null> {
  if (user == null) return null;
  await sleep(500); // TODO
  return {
    email: "turfu@pm.me",
    age: 56,
    friends: 4,
    bio: "Je suis une fleur",
    projectCount: 42,
    projects: await Project.allOf(user, projectPerPage, page * projectPerPage ),
  }
}