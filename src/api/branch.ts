import sleep from "./sleep";
import * as Request from "../utils/request"

export interface BaseResult {
  name: string,
  updatedAt: Date,
}

export interface FetchResult extends BaseResult {
  commitsCount: number,
  lastCommit: string | null,
}

export async function fetch(user: string, project: string, branch?: string | null): Promise<FetchResult | null> {
  if (branch == null) return null;
  await sleep(500);
  return {
    name: "foo",
    lastCommit: "8f5e91",
    commitsCount: 4,
    updatedAt: new Date(),
  }
  //
  return Request.json("api/branch.php", {
    q: "getBranch",
    user,
    project,
    branch,
  })
}

export async function add(user: string, project: string, branch: string): Promise<boolean> {
  await sleep(500);
  return true;
  //
  const response = await Request.post("api/branch.php", {
    user,
    project,
    branch,
  });
  if (response.ok) return true;
  if (response.status === 400) return false;
  return Request.exception(response);
}

export async function remove(user: string, project: string, branch: string): Promise<void> {
  await sleep(500);
  return;
  //
  const response = await Request.delete_("api/branch.php", {
    user, project, branch,
  });
  if (response.ok) return;
  return Request.exception(response);
}

export async function rename(user: string, project: string, branch: string, new_name: string): Promise<boolean> {
  await sleep(500);
  return true;
  //
  const response = await Request.patch("api/branch.php", {
    user,
    project,
    branch,
    new_name,
  });
  if (response.ok) return true;
  if (response.status === 401) return false;
  return Request.exception(response);
}

export async function all(user: string, project: string): Promise<BaseResult[]> {
  await sleep(500);
  return [{
    name: "foo",
    updatedAt: new Date()
  }, {
    name: "bar",
    updatedAt: new Date()
  }, {
    name: "foooo",
    updatedAt: new Date()
  }, {
    name: "dev",
    updatedAt: new Date()
  }]
  //
  return Request.json("api/project.php", {
    q: "fetchAll",
    user,
    project,
  });
}

export async function find(user: string, project: string, branch: string): Promise<boolean> {
  await sleep(500);
  return false;
  //
  return Request.json("api/branch.php", {
    q: "find",
    user,
    project,
    branch,
  })
}

export async function count(user: string, project: string): Promise<number> {
  await sleep(500);
  return 52;
  // 
  return Request.json("api/branch.php", {
    q: "count",
    user,
    project,
  });
}
