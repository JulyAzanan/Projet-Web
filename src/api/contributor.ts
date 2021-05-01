import * as Request from "../utils/request"

export async function add(user: string, project: string, contributor: string): Promise<boolean> {
  const response = await Request.post("api/contributor.php", {
    user,
    project,
    contributor,
  });
  if (response.ok) return true;
  if (response.status === 400) return false;
  Request.exception(response);
  return false;
}

export async function remove(user: string, project: string, contributor: string): Promise<boolean> {
  const response = await Request.delete_("api/contributor.php", {
    user, project, contributor,
  });
  if (response.ok) return true;
  Request.exception(response);
  return false;
}

export async function count(user: string, project: string): Promise<number> {
  return Request.json("api/contributor.php", {
    q: "count",
    user,
    project,
  });
}

export async function isContributor(user: string, project: string, contributor: string): Promise<boolean> {
  return Request.json("api/contributor.php", {
    q: "find",
    user,
    project,
    contributor,
  });
}
