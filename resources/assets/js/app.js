import Vue from 'vue';
import Vuex from 'vuex';
import BN from 'bn.js';
import { BlockchainManager, PebbleManager } from 'root/utils/managers';
import CONFIG from 'root/config';
import Logger from 'root/plugins/logger';
import Store from './store';
import components from './components';
import filters from './filters';

require('./bootstrap');

Vue.use(Vuex);
const store = new Vuex.Store(Store);

Vue.use(Logger);

components.forEach((component) => {
    Vue.component(`pbl-${component.tag}`, component.comp);
});

filters.forEach((filter) => {
    Vue.filter(filter.title, filter.filter);
});

const app = new Vue({
    el: '#app',
    store,

    methods: {
        // Development function to deploy PBL contract from console.
        createPblContract() {
            window.BlockchainManager.callAuthorized(
                PebbleManager,
                'createInstance',
                CONFIG.deployer,
                CONFIG.deployerPassword,
                this.log
            );
        },

        // Development function to receive PBLs on auth user address from console.
        receivePbls() {
            const user = this.$store.state.user.authUser;
            const pebbleManager = new PebbleManager(CONFIG.pblContract);

            window.BlockchainManager.callAuthorized(
                pebbleManager,
                'receivePbls',
                user.wallet_address,
                user.wallet_password,
                this.log,
                user
            );
        }
    },

    created() {
        window.BlockchainManager = new BlockchainManager(CONFIG.network);
        window.BN = BN;
    }
});
