export default function debounce<F extends () => unknown>(fn: F, wait: number): () => void {
  let timer: number;
  return () => {
    clearTimeout(timer);
    // @ts-ignore
    timer = setTimeout(fn, wait);
  }
}
