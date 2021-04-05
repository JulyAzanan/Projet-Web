function sleep(ms: number) {
  // add ms millisecond timeout before promise resolution
  return new Promise((resolve) => setTimeout(resolve, ms));
}

export async function exists(username?: string, project?: string) {
  if (username == undefined || project == undefined) return false;
  await sleep(500);
  return true;
}

export async function metadata(username?: string, project?: string) {
  if (username == undefined || project == undefined) throw new Error("metadata: undefined props");
  await sleep(500);
  return {
    username,
    project,
    meta: {
      mainBranch: "main"
    }
  };
}
