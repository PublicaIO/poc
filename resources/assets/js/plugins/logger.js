const Logger = {
    install(Vue) {
        Vue.mixin({
            methods: {
                log(...args) {
                    console.log(...args); // eslint-disable-line
                },

                logError(...args) {
                    console.error(...args); // eslint-disable-line
                }
            }
        });
    }
};

export default Logger;
