import './bootstrap';
import { createApp } from 'vue';
import ContractForm from './components/contracts/ContractForm.vue';
import i18n from './i18n';

try {
    const app = createApp({});
    
    // Add the i18n plugin
    app.use(i18n);
    
    // Register components
    app.component('contract-form', ContractForm);
    
    // Mount the app
    app.mount('#app');
    console.log('Vue app mounted successfully');
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