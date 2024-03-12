export default class AssetService {
    static loadJsScript(url, callback) {
        const script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = url;
        document.body.appendChild(script);

        script.addEventListener('load', callback);
    }

    static loadCssStylesheet(url, callback) {
        const link = document.createElement('link');
        link.type = 'text/css';
        link.rel = 'stylesheet';
        link.href = url;
        document.body.appendChild(link);

        link.addEventListener('load', callback);
    }
}
