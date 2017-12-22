import Web3 from 'web3';
import CONFIG from 'root/config';
import axios from 'axios';

export default {
    state: {
        web3: null,
        wallet: null
    },

    mutations: {
        setWeb3(state, web3inst) {
            state.web3 = web3inst;
        },

        setWallet(state, wallet) {
            state.wallet = wallet;
        }
    },

    actions: {
        initBlockchain(context) {
            const web3 = new Web3();
            web3.setProvider(CONFIG.network);

            context.commit('setWeb3', web3);
        },

        createWallet(context, password) {
            const { web3 } = context.state.web3;

            console.log(web3);

            web3.eth.personal.newAccount(password, (error, address) => {
                if (error) {
                    console.error('createAccount', error);
                    return;
                }

                axios.post('/wallet/create', {
                    address,
                    private_key: password
                })
                    .then((response) => {
                        context.$store.commit('setWallet', address);
                    })
                    .catch((error) => {
                        console.error('walletSave', error);
                    });
            });
        }
    }
}
