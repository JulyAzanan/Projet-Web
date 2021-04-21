import sleep from "./sleep";

interface BaseResult {
  updatedAt: Date,
  createdAt: Date,
}

export interface FetchResult extends BaseResult {
  commits: string[],
  lastCommit: string | null,
}

export async function fetch(user?: string, project?: string, branch?: string): Promise<FetchResult | null> {
  if (user == null || project == null || branch == null) return null;
  await sleep(500);
  return {
    lastCommit: "8f5e91",
    commits: ["8f5e91", "1ds849", "7q8d8s", "165qsd"],
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
  }
}