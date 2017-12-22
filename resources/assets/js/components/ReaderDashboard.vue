<template>
    <div class="container">
        <pbl-book-buy @success="refreshBook" :book="selectedBook"></pbl-book-buy>

        <div class="page-heading border" v-if="purchased_books.length > 0">
            <h1 class="page-title">
                My Books
            </h1>
        </div>

        <div class="book-grid">
            <div class="book-block" v-for="(book, index) in books" :key="index">
                <h4 class="book-block-title">
                    <a :href="`/${book.url}`">{{ book.title }}</a>
                    <p>Status: <span :class="getStatus(book.status).class">{{ getStatus(book.status).title }}</span></p>
                </h4>

                <div class="book-block-info">
                    <div class="book-cover">
                        <a :href="`/${book.url}`">
                            <img class="img-responsive" :src="book.cover_art_url" :alt="book.title">
                        </a>
                    </div>

                    <div class="book-body">
                        <div class="book-body-section book-section-info">
                            <div class="labeled-field">
                                <div class="field-label">Book Title</div>
                                <div class="field-body">{{ book.title }}</div>
                            </div>

                            <div class="labeled-field">
                                <div class="field-label">Book Description</div>
                                <div class="field-body">{{ book.short_description }}</div>
                            </div>
                        </div>

                        <div class="book-body-section book-section-crowdsale">
                            <template v-if="isInCrowdSale(book)">
                                <div class="labeled-field">
                                    <div class="field-label">Crowdfunding ends date</div>
                                    <div class="field-body">{{ crowdFundingEndDate(book) | formatDate }}</div>
                                </div>

                                <div class="labeled-field">
                                    <div class="field-label">Contributor copies available</div>
                                    <div class="field-body">{{ book.soft_cap | formatNumber }}</div>
                                </div>
                            </template>

                            <div class="labeled-field" v-if="isNotPublished(book)">
                                <div class="field-label">Early bird price</div>
                                <div class="field-body">${{ book.price_for_crowdsale }}</div>
                            </div>

                            <div class="labeled-field" v-if="isNotPublished(book)">
                                <div class="field-label">Price after publishing</div>
                                <div class="field-body">${{ book.price_after_crowdsale }}</div>
                            </div>

                            <p v-if="isAbleToBuy(book)">
                                <button class="button button-active-action" @click.prevent="openBuyModal(book)">
                                    Contribute now with {{ getBookDiscount(book) | formatNumber }}% discount
                                </button>
                            </p>

                            <div class="labeled-field" v-if="isNotPublished(book)">
                                <div class="field-label">Status Description</div>
                                <div class="field-body">Pending to be published</div>
                            </div>

                            <div class="labeled-field" v-if="isPublic(book)">
                                <div class="field-label">Status Description</div>
                                <div class="field-body">Waiting for crowdfunding to start</div>
                            </div>

                            <p v-if="isPublished(book)">
                                <a :href="`/book/read/${book.id}`" class="button button-success button-large">Read</a>
                            </p>

                            <template v-if="!book.isLastViewed">
                                <div class="labeled-field" v-if="isNotPublished(book)">
                                    <div class="field-label">Copies Purchased</div>
                                    <div class="field-body">{{ book.copies_purchased }}</div>
                                </div>
                                <p>
                                    <a href="#!">Sell</a> | <a href="#!">Transfer</a>
                                </p>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
import statusConfig from 'root/config/statuses';
import { ReadManager } from 'root/utils/managers';

export default {
    props: [
        'last_viewed_book',
        'purchased_books',
    ],

    data() {
        return {
            selectedBook: null,
            isLastViewedPurchased: false,
            books: this.purchased_books,
            status: statusConfig,
        }
    },

    methods: {
        getBookDiscount(book) {
            return 100 - (book.price_for_crowdsale / book.price_after_crowdsale * 100);
        },

        isInCrowdSale(book) {
            return book.status === this.status.CROWDSALE_STARTED;
        },

        isNotPublished(book) {
            return book.status < this.status.PUBLISHED;
        },

        isPublished(book) {
            return book.status === this.status.PUBLISHED && !book.isLastViewed;
        },

        isPublic(book) {
            return book.status === this.status.ACTIVE;
        },

        isNotInCrowdSale(book) {
            return !(book.status < this.status.CROWDSALE_STARTED && !book.isLastViewed);
        },

        isAbleToBuy(book) {
            return book.status >= this.status.CROWDSALE_STARTED && book.status <= this.status.PUBLISHED && book.isLastViewed;
        },

        getStatus(status) {
            switch (status) {
                case this.status.ACTIVE:
                    return {
                        title: 'Active',
                        class: 'status-active'
                    }
                case this.status.CROWDSALE_STARTED:
                    return {
                        title: 'Crowdsale in Progress',
                        class: 'status-cs-started'
                    }
                case this.status.CROWDSALE_ENDED:
                    return {
                        title: 'Crowdsale Ended',
                        class: 'status-cs-ended'
                    }
                case this.status.PUBLISHED:
                    return {
                        title: 'Published',
                        class: 'status-published'
                    }
                case this.status.CANCELED:
                    return {
                        title: 'Canceled',
                        class: 'status-canceled'
                    }
                case this.status.PENDING:
                default:
                    return {
                        title: 'Pending',
                        class: 'status-pending'
                    }
            }
        },

        openBuyModal(selectedBook) {
            this.selectedBook = selectedBook;
            $('#buyModal').modal('show');
        },

        crowdFundingEndDate(book) {
            return moment(book.crowdsale_start_date).utc().add('days', book.duration);
        },

        setBooks() {
            this.books.forEach((book) => {
                this.isLastViewedPurchased = book.id === this.last_viewed_book.id || this.isLastViewedPurchased;
            });

            if (this.last_viewed_book.id && !this.isLastViewedPurchased) {
                this.last_viewed_book.isLastViewed = true;
                this.last_viewed_book.copies_purchased = 0;
                this.books.unshift(this.last_viewed_book);
            }
        },

        refreshBook(updatedBook) {
            this.books.forEach((book, index) => {
                if (book.id === updatedBook.id) {
                    this.$set(this.books, index, updatedBook)
                }
            });
        }
    },

    mounted() {
        this.setBooks();
    }
}

</script>
