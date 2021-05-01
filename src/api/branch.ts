import * as Request from "../utils/request"
import * as Commit from "./commit"

export interface BaseResult {
  name: string,
  updatedAt: string,
}

export interface FetchResult extends BaseResult {
  commitsCount: number,
  commits: Commit.BaseResult[],
  lastCommit: string | null,
}

export async function fetch(user: string, project: string, branch?: string | null): Promise<FetchResult | null> {
  if (branch == null) return null;
  return Request.json("api/branch.php", {
    q: "getBranch",
    user,
    project,
    branch,
  })
}

export async function add(user: string, project: string, branch: string): Promise<boolean> {
  const response = await Request.post("api/branch.php", {
    user,
    project,
    branch,
  });
  if (response.ok) return true;
  if (response.status === 400) return false;
  Request.exception(response);
  return false;
}

export async function remove(user: string, project: string, branch: string): Promise<void> {
  const response = await Request.delete_("api/branch.php", {
    user, project, branch,
  });
  if (response.ok) return;
  return Request.exception(response);
}

export async function rename(user: string, project: string, branch: string, new_name: string): Promise<boolean> {
  const response = await Request.patch("api/branch.php", {
    user,
    project,
    branch,
    new_name,
  });
  if (response.ok) return true;
  if (response.status === 401) return false;
  Request.exception(response);
  return false;
}

export async function all(user: string, project: string): Promise<BaseResult[]> {
  return Request.json("api/project.php", {
    q: "fetchAll",
    user,
    project,
  });
}

export async function find(user: string, project: string, branch: string): Promise<boolean> {
  return Request.json("api/branch.php", {
    q: "find",
    user,
    project,
    branch,
  })
}

export async function count(user: string, project: string): Promise<number> {
  return Request.json("api/branch.php", {
    q: "count",
    user,
    project,
  });
}

export async function merge(user: string, project: string, source: string, dest: string): Promise<boolean> {
  const response = await Request.get("api/branch.php", {
    q: "merge",
    user,
    project,
    source,
    dest,
  });
  if (response.ok) return true;
  Request.exception(response);
  return false;
}
