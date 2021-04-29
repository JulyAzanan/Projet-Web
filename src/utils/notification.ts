import uk from 'uikit';

// eslint-disable-next-line
export function notifyError(message: string, err: any): void {
  uk.notification({
    message: `<span uk-icon='icon: warning'></span> ${message}<br><code>${err.message ?? err}</code>`,
    status: "danger",
    pos: "bottom-right",
    timeout: 10000,
  })
}
export function notifyWarning(message: string): void  {
  uk.notification({
    message: `<span uk-icon='icon: warning'></span> ${message}`,
    status: "warning",
    pos: "bottom-right",
    timeout: 10000,
  })
}
export function notifySuccess(message: string): void  {
  uk.notification({
    message: `<span uk-icon='icon: check'></span> ${message}`,
    status: "success",
    pos: "bottom-right",
    timeout: 3000,
  })
}
