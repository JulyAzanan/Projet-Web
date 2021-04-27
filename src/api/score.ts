import sleep from "./sleep";
import * as Request from "../utils/request"
import { musicXML } from "./xml";

export interface BaseResult {
  name: string,
}

export interface DownloadResult extends BaseResult {
  content: string,
}

export async function fetch(user: string, project: string, branch: string, commit: string, partition?: string | null): Promise<DownloadResult | null> {
  if (partition == null) return null;
  await sleep(500);
  return {
    name: "file5.xml",
    content: musicXML
  };
  //
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
  await sleep(500);
  return 52;
  // 
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
  createdAt: Date,
}

export async function all(user: string, project: string, branch: string, commit: string): Promise<AllResult[]> {
  await sleep(500);
  return [{
    name: "foo",
    id: "dqzdeq",
    createdAt: new Date(),
    message: "Pour tout n dans la vie",
  }, {
    name: "bar",
    id: "dqzdeq",
    createdAt: new Date(),
    message: "Pour tout n dans la vie",
  }, {
    name: "foo52",
    id: "dqzdeq",
    createdAt: new Date(),
    message: "Pour tout n dans la vie",
  }]
  //
  return Request.json("api/partition.php", {
    q: "fetchAll",
    user,
    project,
    branch,
    commit,
  });
}
