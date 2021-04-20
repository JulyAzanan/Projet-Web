import sleep from "./sleep"

export interface Result {
  contributors: string[],
  mainBranch: string,
  description: string,
  updatedAt: Date,
  createdAt: Date,
}

export async function find(user?: string, project?: string): Promise<Result | null> {
  if (user == undefined || project == undefined) throw null;
  await sleep(500);
  const foo = false;
  if (foo) return null;
  return {
    contributors: ["Steel", "July", "Michel"],
    mainBranch: "main",
    description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam pellentesque turpis eu libero elementum, mattis egestas nisi scelerisque. Nulla facilisi. Pellentesque lacinia felis non leo scelerisque tristique. Suspendisse bibendum quam quis ullamcorper lacinia. Ut nec semper enim, vitae efficitur felis. Curabitur scelerisque dignissim metus, at sagittis tortor scelerisque sit amet. Donec eu tincidunt justo.",
    updatedAt: new Date(),
    createdAt: new Date(),
  }
}
