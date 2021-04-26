import sleep from "./sleep";

export interface BaseResult {
  name: string,
  updatedAt: Date,
}

export interface FetchResult extends BaseResult {
  commitsCount: number,
  lastCommit: string | null,
}

export async function fetch(user?: string, project?: string, branch?: string): Promise<FetchResult | null> {
  if (user == null || project == null || branch == null) return null;
  await sleep(500);
  return {
    name: "foo",
    lastCommit: "8f5e91",
    commitsCount: 4,
    updatedAt: new Date(),
  }
}