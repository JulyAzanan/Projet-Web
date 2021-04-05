import { createApp } from 'vue'
import App from './App.vue'
import router from './routes'
import uk from 'uikit';
// @ts-ignore
import Icons from 'uikit/dist/js/uikit-icons';

// loads the Icon plugin
// @ts-ignore
uk.use(Icons);

createApp(App).use(router).mount('#app')
