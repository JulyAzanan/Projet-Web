export type Nil<T> = T | null | undefined;

export function isNil<T>(element: Nil<T>): element is null | undefined {
  return element == null;
}
