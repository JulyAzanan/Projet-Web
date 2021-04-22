import { createApp } from 'vue'
import uk from 'uikit';
import App from './App.vue'
import { notifyError } from "./utils/notification"
import router from './app/routes'
import store from './app/store'
// @ts-ignore
import Icons from 'uikit/dist/js/uikit-icons';

// loads the Icon plugin
// @ts-ignore
uk.use(Icons);

router.onError(err => notifyError("Erreur lors de la requête du chemin", err))

createApp(App).use(store).use(router).mount('#app')
