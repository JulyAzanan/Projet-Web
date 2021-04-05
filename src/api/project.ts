function sleep(ms: number) {
  // add ms millisecond timeout before promise resolution
  return new Promise((resolve) => setTimeout(resolve, ms));
}

export async function find(user?: string, project?: string) {
  if (user == undefined || project == undefined) throw null;
  await sleep(500);
  const foo = false;
  if (foo) return null;
  return {
    contributors: ["Steel", "July", "Michel"],
    mainBranch: "main",
    updatedAt: new Date(),
    createdAt: new Date(),
  }
}
