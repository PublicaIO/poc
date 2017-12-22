<template>
    <div>
        <pbl-progress v-if="loading"></pbl-progress>
        <div v-if="error" class="alert alert-danger">{{ error }}</div>

        <iframe :src="download_url" frameborder="0" width="100%" height="800px"></iframe>
    </div>
</template>

<script>
import { ReadManager } from 'root/utils/managers';

export default {
    props: [
        'book',
    ],

    data() {
        return {
            download_url: null,
            loading: null,
            error: null,
        }
    },

    computed: {
        user() {
            return this.$store.state.user.authUser;
        }
    },

    methods: {
        checkReadTokenAvailability() {
            if (!this.book.contract_address) {
                this.logError('Book/Reader::checkReadTokenAvailability', new Error('Contract address missing'));
                return;
            }

            this.loading = true;
            const readManager = new ReadManager(this.book.contract_address);

            readManager.instance.methods.balanceOf(this.user.wallet_address).call((error, balance) => {
                if (error) {
                    this.logError('Book/Reader::getBalance', error);
                    this.loading = false;
                    return;
                }

                if (balance > 0) {
                    this.getDownloadUrl();
                } else {
                    this.error = 'You haven\'t bought this book';
                    this.loading = false;
                }
            });
        },

        getDownloadUrl() {
            axios
                .get(`/book/download_url/${this.book.id}`)
                .then((response) => {
                    this.download_url = response.data;
                    this.loading = false;
                })
                .catch(() => {
                    this.loading = false;
                });
        },
    },

    mounted() {
        this.checkReadTokenAvailability();
    },
}
</script>
