import { createApp } from 'vue'
import App from './App.vue'
import router from './routes'
import uk from 'uikit';
// @ts-ignore
import Icons from 'uikit/dist/js/uikit-icons';

// loads the Icon plugin
// @ts-ignore
uk.use(Icons);

router.onError(err => uk.notification({
    message: `<span uk-icon='icon: warning'></span> Erreur lors de la requête du chemin<br><code>${err.message}</code>`,
    status: "danger",
    pos: "bottom-right",
    timeout: 10000,
}))

createApp(App).use(router).mount('#app')
