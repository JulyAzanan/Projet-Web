import * as User from "./user"
import * as Branch from "./branch"
import * as Request from "../utils/request"

export const perPage = 7;

export interface BaseResult {
  name: string,
  authorName: string
  private: boolean,
  updatedAt: Date,
  createdAt: Date,
  description: string,
  mainBranch: string,
}

export interface FetchResult extends BaseResult {
  contributors: User.BaseResult[],
  branches: Branch.BaseResult[],
}

export async function fetch(user: string, project?: string | null): Promise<FetchResult | null> {
  if (project == null) return null;
  return Request.json("api/project.php", {
    q: "getProject",
    user,
    project,
  })
}

export async function add(user: string, project: string, isPrivate: boolean, description: string | null): Promise<boolean> {
  const response = await Request.post("api/project.php", {
    user,
    project,
    private: isPrivate,
    description: description || null,
  });
  if (response.ok) return true;
  if (response.status === 400) return false;
  Request.exception(response);
  return false;
}

export async function remove(user: string, project: string): Promise<void> {
  const response = await Request.delete_("api/project.php", {
    user, project
  });
  if (response.ok) return;
  return Request.exception(response);
}

export interface UpdateInput {
  private: boolean | null
  description?: string | null
  mainBranch: string | null
}

export async function edit(user: string, project: string, content: UpdateInput): Promise<boolean> {
  const response = await Request.patch("api/project.php", {
    user,
    project,
    description: null,
    mainBranch: null,
    private: false,
    ...content,
  });
  if (response.ok) return true;
  if (response.status === 401) return false;
  Request.exception(response);
  return false;
}

export async function allOf(user: string, page: number): Promise<BaseResult[]> {
  return Request.json("api/project.php", {
    q: "fetchAllFromUser",
    user,
    first: perPage,
    after: perPage * (page - 1),
  });
}

export async function all(page: number): Promise<BaseResult[]> {
  return Request.json("api/project.php", {
    q: "fetchAll",
    first: perPage,
    after: perPage * (page - 1),
  });
}

export async function find(user: string, project: string): Promise<boolean> {
  return Request.json("api/project.php", {
    q: "find",
    user,
    project,
  })
}

export interface SearchResult {
  results: BaseResult[],
  count: number
}

export async function search(project: string, page: number): Promise<SearchResult> {
  return Request.json("api/project.php", {
    q: "seek",
    project,
    first: perPage,
    after: perPage * (page - 1),
  })
}

export async function count(): Promise<number> {
  return Request.json("api/project.php", { q: "count" });
}

export async function countOf(user: string): Promise<number> {
  return Request.json("api/project.php", {
    q: "countFromUser",
    user,
  });
}
