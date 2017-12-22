<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2>{{ author.name }}</h2>
                    </div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <figure class="figure">
                                    <img :src="photo" :alt="author.name" class="img-thumbnail">
                                </figure>
                            </div>

                            <div class="col-sm-9">
                                <form class="form-horizontal">
                                    <template v-if="edit">
                                        <button @click.prevent="submit" type="button" class="btn btn-success">Save</button>
                                        <button @click.prevent="edit = false" type="button" class="btn btn-info">Cancel</button>
                                    </template>

                                    <template v-else>
                                        <button type="button" v-if="isOwner" @click="edit = true" class="btn btn-primary">Edit </button>
                                    </template>

                                    <div class="form-group">
                                        <label for="email" class="col-md-4 control-label">Email</label>
                                        <div v-if="!edit" :id="email" class="col-md-6">{{ email }}</div>

                                        <template v-else>
                                            <div class="col-md-6">
                                                <input type="email" name="email" class="form-control" v-model="email" id="email">
                                                <form-error v-if="errors.email" :errors="errors">
                                                    {{ errors.email[0] }}
                                                </form-error>
                                            </div>
                                        </template>
                                    </div>

                                    <div class="form-group">
                                        <label for="about" class="col-md-4 control-label">About</label>
                                        <div v-if="!edit" :id="about" class="col-md-6">{{ about }}</div>

                                        <template v-else>
                                            <div class="col-md-6">
                                                <textarea name="about" v-model="about" id="about" class="form-control"></textarea>
                                                <form-error v-if="errors.about" :errors="errors">
                                                    {{ errors.about[0] }}
                                                </form-error>
                                            </div>
                                        </template>
                                    </div>

                                    <div class="form-group">
                                        <label for="kindle_store_link" class="col-md-4 control-label">Kindle Store Link</label>
                                        <div v-if="!edit" :id="kindle_store_link" class="col-md-6">
                                            <span>
                                                {{ kindle_store_link }}
                                            </span>
                                        </div>
                                        <template v-else>
                                            <div class="col-md-6">
                                                <textarea name="kindle_store_link" v-model="kindle_store_link" id="kindle_store_link" class="form-control"></textarea>
                                                <form-error v-if="errors.kindle_store_link" :errors="errors">
                                                    {{ errors.kindle_store_link[0] }}
                                                </form-error>
                                            </div>
                                        </template>
                                    </div>

                                    <div class="form-group" v-if="isOwner">
                                        <label for="pbl_balance" class="col-md-4 control-label">PBL Balance</label>
                                        <div id="pbl_balance" class="col-md-6">{{ `${pblBalance}` | convertFromWei | formatNumber }}</div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="book-list">
            <h2>My Books</h2>

            <div class="panel panel-default" v-if="isOwner">
                <div class="panel-body">
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
                </div>
            </div>


            <div class="panel panel-default" v-for="(book, index) in books" :key="index">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-2">
                            <img :src="book.cover_art_url" :alt="book.title" class="img-thumbnail">
                        </div>
                        <div class="col-md-10">
                            <h3>
                                <a :href="`/${book.url}`">{{ book.title }}</a>
                            </h3>
                            <p>{{ book.status_label }}</p>

                            <p v-if="book.status == status.CROWDSALE_STARTED">PBL {{ book.sold_keys * book.price_for_crowdsale | convertToPbl | formatNumber }} or {{ (book.sold_keys / book.soft_cap) * 100 | formatNumber('0.00') }}% of the goal raised.</p>
                            <p v-if="book.status == status.CROWDSALE_ENDED">PBL {{ book.sold_keys * book.price_for_crowdsale | convertToPbl | formatNumber }} raised (${{ book.sold_keys * book.price_for_crowdsale | formatNumber('0,0.00') }})</p>
                            <p v-if="book.status == status.PUBLISHED">PBL {{ book.sold_keys * book.price_for_crowdsale | convertToPbl | formatNumber }} in earnings (${{ book.sold_keys * book.price_for_crowdsale | formatNumber('0,0.00') }})</p>

                            <a :href="`/book/edit/${book.id}`" v-if="(book.status == status.PENDING || book.status == status.ACTIVE || book.status == status.CROWDSALE_STARTED) && isOwner">Edit</a>
                            <a :href="`/book/upload/${book.id}`" v-if="book.status == status.CROWDSALE_ENDED && isOwner">Upload manuscript</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import { PebbleManager } from 'root/utils/managers';
import statusConfig from 'root/config/statuses';

export default {
    props: [
        'author',
        'info',
        'auth_user',
        'submit_url',
        'photo',
        'books'
    ],

    data() {
        return {
            edit: false,
            email: this.author.email,
            username: this.author.username,
            about: this.info.about,
            kindle_store_link: this.info.kindle_store_link,
            errors: [],
            pblBalance: 0,
            status: statusConfig
        }
    },

    computed: {
        isOwner() {
            return this.author.id === this.auth_user.id
        }
    },

    methods: {
        submit() {
            this.errors = '';

            axios.post(this.submit_url, {
                id: this.author.id,
                email: this.email,
                about: this.about,
                kindle_store_link: this.kindle_store_link,
                username: this.username
            })
                .then((response) => {
                    if (response.data.success) {
                        this.edit = false;
                    }

                    if (response.data.redirect) {
                        window.location = response.data.redirect;
                    }
                })
                .catch((e) => {
                    this.errors = e.response.data.errors;
                });
        },

        getPblBalance() {
            if (!this.isOwner) return;

            const pblContract = new PebbleManager();

            pblContract.balanceOf(this.auth_user.wallet_address, (error, balance) => {
                if (error) {
                    this.logError('AuthorView::getPblBalance', error);
                    return;
                }

                this.pblBalance = balance;
            });
        }
    },

    mounted() {
        this.getPblBalance();
    }
}
</script>
