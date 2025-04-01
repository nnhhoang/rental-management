import './bootstrap';
import { createApp } from 'vue';
import ContractForm from './components/contracts/ContractForm.vue';
import i18n from './i18n';

document.addEventListener('DOMContentLoaded', () => {
    try {
        const app = createApp({});
        
        // Add the i18n plugin
        app.use(i18n);
        
        // Register components
        app.component('contract-form', ContractForm);
        
        // Make sure the app element exists before attempting to mount
        const appElement = document.getElementById('app');
        if (appElement) {
            app.mount('#app');
            console.log('Vue app mounted successfully');
        } else {
            console.warn('App element not found, Vue app not mounted');
        }
    } catch (error) {
        console.error('Error mounting Vue app:', error);
        // Display error on the page
        const appElement = document.getElementById('app');
        if (appElement) {
            appElement.innerHTML = `
                <div style="color: red; padding: 20px; border: 1px solid red; margin: 20px 0;">
                    <h2>Error initializing application</h2>
                    <pre>${error.message}</pre>
                    <p>Check the browser console for more details.</p>
                </div>
            `;
        }
    }
});