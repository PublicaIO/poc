<template>
    <div class="container">
        <pbl-manuscript-upload :book_id="currentBookId"></pbl-manuscript-upload>

        <div class="dashboard-page">
            <div class="page-heading border">
                <h2 class="page-title">
                    My Funds
                    <p>PBL tokens: <span>{{ `${pblBalance}` | convertFromWei | formatNumber }}</span> (estimated $<span>{{ `${pblBalance}` | convertFromWei | convertToFiat }}</span>)</p>
                </h2>

                <p class="page-actions">
                    <a href="https://publica.io" class="pull-right">How to convert PBL tokens to Bitcoin, Litecoin, Dash, Ether or fiat?</a>
                </p>
            </div>

            <div class="page-heading" v-if="books.length > 0">
                <h2 class="page-title">
                    My Books
                </h2>
            </div>

            <div class="row">
                <div class="col-md-2">
                    <a href="/book/new/">
                        <img src="/img/new_book.svg" alt="Start new book crowdfunding" class="img-thumbnail">
                    </a>
                </div>
                <div class="col-md-10">
                    <h3>
                        <a href="/book/new/">Start new book crowdfunding</a>
                    </h3>
                </div>
            </div>


            <div class="row " v-for="(book, index) in books" :key="index">
                <div class="col-md-2">
                    <img :src="book.cover_art_url" :alt="book.title" class="img-thumbnail">
                </div>

                <div class="col-md-10">
                    <h3>
                        <a :href="`/${book.url}`">{{ book.title }}</a>
                    </h3>

                    <h4 style="color:#cbb956;">
                        {{ book.status_label }}
                    </h4>

                    <p v-if="book.status == status.CROWDSALE_STARTED">PBL {{ book.received_pbl | formatNumber }} or {{ (book.received_pbl * 0.2 / book.soft_cap) * 100 | formatNumber('0.00') }}% of the goal raised.</p>
                    <p v-if="book.status == status.CROWDSALE_ENDED">PBL {{ book.received_pbl | formatNumber }} raised (${{ book.received_pbl * 0.2 | formatNumber('0,0.00') }})</p>
                    <p v-if="book.status == status.PUBLISHED">PBL {{ book.received_pbl | formatNumber }} in earnings (${{ book.received_pbl * 0.2 | formatNumber('0,0.00') }})</p>

                    <a class="btn btn-primary" :href="`/book/edit/${book.id}`" v-if="(book.status == status.PENDING || book.status == status.ACTIVE || book.status == status.CROWDSALE_STARTED) && isOwner">Edit</a>
                    <a class="btn btn-primary" @click.prevent="showManuscriptUploadModal(book.id)" v-if="book.status == status.CROWDSALE_ENDED && isOwner">Upload manuscript</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import CONFIG from 'root/config';
import { PebbleManager } from 'root/utils/managers';
import statusConfig from 'root/config/statuses';

export default {
    props: [
        'author',
        'books'
    ],

    data() {
        return {
            errors: [],
            pblBalance: 0,
            currentBookId: null,
            status: statusConfig
        }
    },

    computed: {
        isOwner() {
            return true;
        }
    },

    methods: {
        getPblBalance() {
            if (!this.isOwner) return;

            const pblContract = new PebbleManager();

            pblContract.balanceOf(this.author.wallet_address, (error, balance) => {
                if (error) {
                    this.logError('AuthorView::getPblBalance', error);
                    return;
                }

                this.pblBalance = balance;
            });
        },

        showManuscriptUploadModal(bookId) {
            this.currentBookId = bookId;
            $('#manuscript_upload').modal();
        }
    },

    mounted() {
        this.getPblBalance();
    }
}
</script>
