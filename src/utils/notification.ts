import uk from 'uikit';

export function notifyError(message: string, err: any) {
  uk.notification({
    message: `<span uk-icon='icon: warning'></span> ${message}<br><code>${err.message ?? err}</code>`,
    status: "danger",
    pos: "bottom-right",
    timeout: 10000,
  })
}
export function notifyWarning(message: string) {
  uk.notification({
    message: `<span uk-icon='icon: warning'></span> ${message}`,
    status: "warning",
    pos: "top-center",
    timeout: 10000,
  })
}