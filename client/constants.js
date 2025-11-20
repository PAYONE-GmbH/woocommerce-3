// Get plugin URL dynamically from PHP-provided settings
const getPluginUrl = () => {
    if (typeof window.wc !== 'undefined' && window.wc.wcSettings) {
        const payoneData = window.wc.wcSettings.getSetting('payone_data', {});
        if (payoneData.pluginUrl) {
            return payoneData.pluginUrl.replace(/\/$/, ''); // Remove trailing slash if present
        }
    }
    // Fallback to default path
    return '/wp-content/plugins/payone-woocommerce-3';
};

export const PAYONE_PLUGIN_URL = getPluginUrl();
export const PAYONE_ASSETS_URL = `${PAYONE_PLUGIN_URL}/assets`;
