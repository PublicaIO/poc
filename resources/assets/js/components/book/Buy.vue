<template>
    <div class="modal fade" id="buyModal" tabindex="-1" role="dialog">
        <div v-if="book" class="modal-dialog" role="document">
            <pbl-progress v-if="loading"></pbl-progress>

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Buy this book</h4>
                </div>

                <div class="modal-body buy-body">
                    <pbl-ui-form-field id="title" :init-value="buyQty" title="Quantity" type="number" @changed="buyQty = arguments[0]">
                    </pbl-ui-form-field>

                    <h3>
                        Price: ${{ formatNumber(buyQty * book.price_for_crowdsale) }} (PBL {{ formatNumber(convertToPbl(buyQty * book.price_for_crowdsale)) }})
                    </h3>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click.prevent="buy">Contribute</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ReadManager, PebbleManager } from 'root/utils/managers';
import numeral from 'numeral';
import BN from 'bn.js';
import CONFIG from 'root/config';

export default {
    props: [
        'book'
    ],

    computed: {
        auth_user() {
            return this.$store.state.user.authUser;
        }
    },

    data() {
        return {
            loading: null,
            buyQty: 1,
        }
    },

    methods: {
        formatNumber(number) {
            return numeral(number).format('0,0');
        },

        convertToPbl(value) {
            if (value && !isNaN(value)) {
                return parseInt(value) / 0.2;
            }

            return 0;
        },

        buy() {
            this.loading = true;
            const readManager = new ReadManager(this.book.contract_address);

            this.approve((error) => {
                if (error) {
                    this.logError('Book/Buy::buy::approve', error);
                    return;
                }

                BlockchainManager.callAuthorized(
                    readManager,
                    'buy',
                    this.auth_user.wallet_address,
                    this.auth_user.wallet_password,
                    (error, transactionHash) => {
                        if (error) {
                            this.logError('Book/Buy::buy::callAuthorized::buy', error);
                            return;
                        }

                        axios.post('/book/buy', {
                            book_id: this.book.id,
                            transaction_hash: transactionHash,
                            amount: this.buyQty,
                            pbl_amount: this.convertToPbl(this.book.price_for_crowdsale) * this.buyQty
                        })
                            .then((response) => {
                                this.loading = false;
                                $('#buyModal').modal('hide');
                                this.$emit('success', response.data);
                            })
                            .catch((e) => {
                                this.errors = e.response.data.errors;
                                this.loading = false;
                            });
                    },
                    this.auth_user.wallet_address
                );
            });
        },

        approve(callback) {
            this.loading = true;
            const pebbleManager = new PebbleManager();

            const pblPrice = new BN(this.convertToPbl(this.book.price_for_crowdsale) * this.buyQty);
            const decimal = new BN(CONFIG.decimals.toString());
            const price = pblPrice.mul(decimal);

            BlockchainManager.callAuthorized(
                pebbleManager,
                'approve',
                this.auth_user.wallet_address,
                this.auth_user.wallet_password,
                callback,
                this.book.contract_address,
                this.auth_user.wallet_address,
                price
            );
        },
    }
}
</script>
