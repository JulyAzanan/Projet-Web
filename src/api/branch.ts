import sleep from "./sleep"

interface Result {
  updatedAt: Date,
}

export async function find(user?: string, project?: string, branch?: string): Promise<Result | null> {
  if (user == undefined || project == undefined || branch == undefined) throw null;
  await sleep(500);
  return {
    updatedAt: new Date(),
  }
}