import sleep from "./sleep";

interface BaseResult {
  private: boolean,
  updatedAt: Date,
  createdAt: Date,
  description: string,
}

export interface FetchResult extends BaseResult {
  contributors: string[],
  branches: string[],
  mainBranch: string,
}

export async function fetch(user?: string, project?: string): Promise<FetchResult | null> {
  if (user == null || project == null) return null;
  await sleep(500); // TODO
  return {
    private: false,
    contributors: ["Steel", "July", "Michel"],
    branches: ["main", "dev", "prod"],
    mainBranch: "main",
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam pellentesque turpis eu libero elementum, mattis egestas nisi scelerisque. Nulla facilisi. Pellentesque lacinia felis non leo scelerisque tristique. Suspendisse bibendum quam quis ullamcorper lacinia. Ut nec semper enim, vitae efficitur felis. Curabitur scelerisque dignissim metus, at sagittis tortor scelerisque sit amet. Donec eu tincidunt justo.",
  };
}