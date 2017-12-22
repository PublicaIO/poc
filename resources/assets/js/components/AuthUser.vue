<template>
</template>

<script>
export default {
    props: ['init_user'],

    data() {
        return {
            user: this.init_user
        }
    },

    methods: {
        createWallet() {
            if (!this.user.id || this.user.wallet_address) {
                return;
            }

            BlockchainManager.createWallet(this.user.wallet_password, (error, wallet_address) => {
                if (error) {
                    this.logError('AuthUser::createWallet', error);
                    return;
                }

                axios.post('/wallet/create', { wallet_address })
                    .then((response) => {
                        this.user.wallet_address = wallet_address;
                        this.$store.commit('setAuthUser', this.user);
                    })
                    .catch((error) => {
                        this.logError('AuthUser::createWallet::POST', error);
                    });
            });
        }
    },

    mounted() {
        this.$store.commit('setAuthUser', this.user);
        this.createWallet();
    }
}
</script>
