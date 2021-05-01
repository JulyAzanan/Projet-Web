import * as User from "./user"
import * as Request from "../utils/request"
import * as Score from "./score"

export interface BaseResult {
  id: string,
  message: string,
  createdAt: Date,
  publisher: string,
}

export interface FetchResult extends Omit<BaseResult, "publisher"> {
  publisher: User.BaseResult,
  files: Score.AllResult[],
}

export async function fetch(user: string, project: string, branch: string, commit?: string | null): Promise<FetchResult | null> {
  if (commit == null) return null;
  return Request.json("api/commit.php", {
    q: "getCommit",
    user,
    project,
    branch,
    commit,
  })
}

export interface ScoreInput {
  name: string,
  content: string,
}

export interface ContentInput {
  message: string,
  scores: ScoreInput[]
}

export async function add(user: string, project: string, branch: string, content: ContentInput): Promise<{id: string} | null> {
  const response = await Request.post("api/commit.php", {
    user,
    project,
    branch,
    ...content
  });
  if (response.ok) return response.json();
  Request.exception(response);
  return null;
}

export async function count(user: string, project: string, branch: string): Promise<number> {
  return Request.json("api/commit.php", {
    q: "count",
    user,
    project,
    branch,
  });
}

export async function find(user: string, project: string, branch: string, commit: string): Promise<boolean> {
  return Request.json("api/commit.php", {
    q: "find",
    user,
    project,
    branch,
    commit,
  })
}

export async function all(user: string, project: string, branch: string): Promise<BaseResult[]> {
  return Request.json("api/commit.php", {
    q: "fetchAll",
    user,
    project,
    branch,
  });
}
