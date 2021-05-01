import * as Request from "../utils/request"
import * as Score from "./score"

export interface BaseResult {
  name: string,
}

export interface DownloadResult extends BaseResult {
  content: string,
}

export async function fetch(user: string, project: string, branch: string, commit: string, partition?: string | null): Promise<DownloadResult | null> {
  if (partition == null) return null;
  return Request.json("api/partition.php", {
    q: "getPartition",
    user,
    project,
    branch,
    commit,
    partition,
  })
}

export async function count(user: string, project: string, branch: string, partition: string): Promise<number> {
  return Request.json("api/partition.php", {
    q: "count",
    user,
    project,
    branch,
    partition,
  });
}

export interface AllResult extends BaseResult {
  id: string,
  message: string,
  createdAt: string,
}

export async function all(user: string, project: string, branch: string, commit: string): Promise<AllResult[]> {
  return Request.json("api/partition.php", {
    q: "fetchAll",
    user,
    project,
    branch,
    commit,
  });
}

export async function download(user: string, project: string, branch: string, commit: string): Promise<Score.DownloadResult[]> {
  return Request.json("api/partition.php", {
    q: "download",
    user,
    project,
    branch,
    commit,
  });
}

