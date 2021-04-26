import sleep from "./sleep";
import * as Request from "../utils/request"

export async function add(user: string, project: string, contributor: string): Promise<boolean> {
  await sleep(500);
  return true;
  //
  const response = await Request.post("api/contributor.php", {
    user,
    project,
    contributor,
  });
  if (response.ok) return true;
  if (response.status === 400) return false;
  return Request.exception(response);
}

export async function remove(user: string, project: string, contributor: string): Promise<void> {
  await sleep(500);
  return;
  //
  const response = await Request.delete_("api/project.php", {
    user, project, contributor,
  });
  if (response.ok) return;
  return Request.exception(response);
}

export async function count(user: string, project: string): Promise<number> {
  await sleep(500);
  return 52;
  // 
  return Request.json("api/contributor.php", {
    q: "count",
    user,
    project,
  });
}

export async function isContributor(user: string, project: string, contributor: string): Promise<boolean> {
  await sleep(500);
  return true;
  // 
  return Request.json("api/contributor.php", {
    q: "find",
    user,
    project,
    contributor,
  });
}
