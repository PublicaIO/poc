<template>
    <div class="book-view-wrapper">
        <pbl-progress v-if="loading"></pbl-progress>

        <header class="page-heading">
            <h1 class="page-title">
                Create new book crowdsale
            </h1>

            <div class="page-actions">
                <button class="button button-active-action" @click.prevent="saveBook">
                    Save
                </button>

                <button v-if="book.status == status.PENDING" class="button button-success" @click.prevent="makePublic">
                    Make Public
                </button>

                <button v-if="book.status == status.ACTIVE" class="button button-success" @click.prevent="startCrowdSale">
                    Start Crowdsale
                </button>
            </div>
        </header>

        <main>
            <form class="form-block">
                <section>
                    <h2>Book Details</h2>

                    <div class="form-wrap">
                        <div class="form-group">
                            <pbl-ui-form-field id="title" :init-value="book.title" title="Book title" type="input" @changed="book.title = arguments[0]">
                            </pbl-ui-form-field>
                        </div>

                        <div class="form-group">
                            <pbl-ui-form-field id="url" :init-value="bookUrl" title="Book Crowdfunding Page Address" type="input" :disabled="true">
                            </pbl-ui-form-field>
                        </div>

                        <div class="form-group">
                            <div class="form-field">
                                <div class="input">
                                    <label for="cover_art">Cover Art</label>
                                    <input id="cover_art" type="file" @change="processCoverFile($event, 'cover_art')">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <pbl-ui-form-field id="promotion_text" :init-value="book.promotion_text" title="Book Promotion Text" type="textarea" @changed="book.promotion_text = arguments[0]">
                            </pbl-ui-form-field>

                            <pbl-form-error v-if="errors.promotion_text" :errors="errors">
                                {{ errors.promotion_text[0] }}
                            </pbl-form-error>
                        </div>

                        <div class="form-group">
                            <pbl-ui-form-field id="short_description" :init-value="book.short_description" title="Book Description" type="textarea" @changed="book.short_description = arguments[0]">
                            </pbl-ui-form-field>

                            <pbl-form-error v-if="errors.short_description" :errors="errors">
                                {{ errors.short_description[0] }}
                            </pbl-form-error>
                        </div>

                        <div class="form-group">
                            <div class="form-field">
                                <div class="input">
                                    <label for="first_chapter">First Chapter</label>
                                    <input id="first_chapter" type="file">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section>
                    <h2>Crowdfunding Setup</h2>
                    <h3>Your Goal</h3>

                    <div class="form-wrap">
                        <p>
                            Check crowdfunding tips and tricks for successful authors
                        </p>

                        <div class="form-intro">
                            <div class="side">
                                <iframe width="560" height="315" src="https://www.youtube.com/embed/fGagdMe07vc" frameborder="0" gesture="media" allow="encrypted-media" allowfullscreen></iframe>
                            </div>

                            <div class="side">
                                <button class="button button-active-action button-large" @click.prevent="true">
                                    Check out FAQ section
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <pbl-ui-form-field id="goal" title="Your Goal" :init-value="book.goal" type="number" :description="true" symbol="$" :body="`Smart contract will be set up in PBL and price adjusted as per daily exchange.<br>Current PBL goal is <strong>${convertToPbl(book.goal)}</strong> based on current exchange rate. LEARN MORE`"
                                @changed="book.goal = arguments[0]">
                            </pbl-ui-form-field>

                            <pbl-form-error v-if="errors.goal" :errors="errors">{{ errors.goal[0] }}</pbl-form-error>
                        </div>

                        <div class="form-group">
                            <pbl-ui-form-field id="price_for_crowdsale" :init-value="book.price_for_crowdsale" title="Book price during crowfunding for early supporters" type="number" :description="true"
                                symbol="$" :body="`Price per your book token will be set up in PBL and price adjusted as per daily exchange.<br>Current PBL price per one book is PBL <strong>${convertToPbl(book.price_for_crowdsale)}</strong>. LEARN MORE`"
                                @changed="book.price_for_crowdsale = arguments[0]">
                            </pbl-ui-form-field>

                            <pbl-form-error v-if="errors.price_for_crowdsale" :errors="errors">
                                {{ errors.price_for_crowdsale[0] }}
                            </pbl-form-error>
                        </div>

                        <div class="form-group">
                            <pbl-ui-form-field id="crowdsale_keys_amount" title="Amount of tokens (book access keys) during crowdsale" :init-value="crowdSaleTokensAmount" type="number"
                                :disabled="true" :description="true">
                            </pbl-ui-form-field>
                        </div>

                        <div class="form-group">
                            <pbl-ui-form-field id="soft_cap" :init-value="book.soft_cap" title="Soft cap - what is a minimum viable amount to release the book?" type="number" symbol="$"
                                body="Price describe the difference in added value and end product between your goal soft cap.<br>LEARN MORE"
                                @changed="book.soft_cap = arguments[0]" :description="true">
                            </pbl-ui-form-field>

                            <pbl-form-error v-if="errors.soft_cap" :errors="errors">
                                {{ errors.soft_cap[0] }}
                            </pbl-form-error>
                        </div>

                        <div class="form-group">
                            <pbl-ui-form-field id="soft_cap_description" :init-value="book.soft_cap_description" title="Soft Cap Description Text" type="textarea" @changed="book.soft_cap_description = arguments[0]">
                            </pbl-ui-form-field>

                            <pbl-form-error v-if="errors.soft_cap_description" :errors="errors">
                                {{ errors.soft_cap_description[0] }}
                            </pbl-form-error>
                        </div>

                        <div class="form-group">
                            <pbl-ui-form-field id="crowdsale_start_date" :init-value="crowdSaleStartDate" title="Start date of the crowdsale" type="date"
                                @changed="book.crowdsale_start_date = arguments[0]" :description="true">
                            </pbl-ui-form-field>

                            <pbl-form-error v-if="errors.crowdsale_start_date" :errors="errors">
                                {{ errors.crowdsale_start_date[0] }}
                            </pbl-form-error>
                        </div>

                        <div class="form-group">
                            <pbl-ui-form-field id="duration" :init-value="book.duration" title="Duration of crowdfunding campaign (you can prolong it later, if necessary)" type="number"
                                symbol="days" @changed="book.duration = arguments[0]" :description="true">
                            </pbl-ui-form-field>

                            <pbl-form-error v-if="errors.duration" :errors="errors">
                                {{ errors.duration[0] }}
                            </pbl-form-error>
                        </div>

                        <div class="form-group">
                            <pbl-ui-form-field id="price_after_crowdsale" :init-value="book.price_after_crowdsale" title="Book price after crowdfunding" type="number" symbol="$" @changed="book.price_after_crowdsale = arguments[0]"
                                :description="true">
                            </pbl-ui-form-field>

                            <pbl-form-error v-if="errors.price_after_crowdsale" :errors="errors">
                                {{ errors.price_after_crowdsale[0] }}
                            </pbl-form-error>
                        </div>

                        <div class="form-group">
                            <pbl-ui-form-field id="aftersale_keys_amount" :init-value="book.aftersale_keys_amount" title="Amount of tokens (book access keys) offered after crowdfunding" type="number"
                                @changed="book.aftersale_keys_amount = arguments[0]" :description="true">
                            </pbl-ui-form-field>

                            <pbl-form-error v-if="errors.aftersale_keys_amount" :errors="errors">
                                {{ errors.aftersale_keys_amount[0] }}
                            </pbl-form-error>
                        </div>
                    </div>

                    <pbl-kyc @success="kycPassed"></pbl-kyc>

                </section>

                <section id="newsection">
                    <h2>Revenue Sharing</h2>
                    <h3>Add people with whom you want to share the revenue from sales here</h3>

                    <div class="tb-wrap">
                        <table>
                            <tr>
                                <td>Beneficiary</td>
                                <td>Etherium Address</td>
                                <td>Revenue share</td>
                                <td>Maximum revenue</td>
                            </tr>

                            <tr v-for="i in rev">
                                <td>
                                    <div class="input-field">
                                        <input type="text">
                                    </div>
                                </td>
                                <td>
                                    <div class="input-field">
                                        <input type="text">
                                    </div>
                                </td>
                                <td>
                                    <div class="input-field">
                                        <input type="text"> %
                                    </div>
                                </td>
                                <td>
                                    <div class="input-field">
                                        <input type="text"> PBL
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <button class="button button-active-action" @click.prevent="rev++">
                            Add
                        </button>
                    </div>

                    <div class="form-group">
                        <button class="button button-active-action" @click.prevent="saveBook">
                            Save
                        </button>

                        <button v-if="book.status == status.PENDING" class="button button-success" @click.prevent="makePublic">
                            Make Public
                        </button>

                        <button v-if="book.status == status.ACTIVE" class="button button-success" @click.prevent="startCrowdSale">
                            Start Crowdsale
                        </button>
                    </div>
                </section>
            </form>
        </main>

    </div>
</template>

<style>

    #newsection button {
        margin: 5px 0 5px 5px;
    }

    #newsection .tb-wrap {
        border: 1px solid #b6bedb;
        border-radius: 10px;
        margin: 10px 0;
    }

    #newsection table {
        width: 100%;
        border-collapse: collapse;
    }
    #newsection table tr td {
        padding: 5px;
    }

    #newsection table tr td .input-field input {
        border: 1px dashed #b6bedb;
        border-radius: 5px;
        padding: 3px 10px;
        background: #fff;
        font-family: 'Lato', sans-serif;
        color: #0057B1;
    }
</style>


<script>
import { ReadManager } from 'root/utils/managers';
import CONFIG from 'root/config';
import statusConfig from 'root/config/statuses';
import moment from 'moment';

export default {
    props: [
        'initial_book',
        'author',
        'auth_user',
        'edit',
        'kyc_passed'
    ],

    data() {
        return {
            rev: 1,
            book: {
                title: null,
                url: null,
                short_description: null,
                cover_art_url: null,
                price_for_crowdsale: null,
                price_after_crowdsale: null,
                crowdsale_start_date: null,
                presale_keys_amount: null,
                goal: null,
                soft_cap: null,
                duration: null,
                aftersale_keys_amount: null,
                promotion_text: null,
                status: null,
                soft_cap_description: null,
            },
            errors: '',
            loading: false,
            isKycPassed: this.kyc_passed,
            formData: new FormData(),
            status: statusConfig
        };
    },

    methods: {
        processCoverFile(event, fieldName) {
            this.formData.set(fieldName, event.target.files[0], event.target.files[0].name);
        },

        convertToPbl(value) {
            if (value && !isNaN(value)) {
                return parseInt(value) / 0.2;
            }

            return 0;
        },

        saveAction(callback = () => {}) {
            _.each(this.book, (value, key) => {
                if (key === 'cover_art') {
                    return;
                }
                this.formData.set(key, value);
            });

            this.formData.set('url', this.bookUrl);

            axios
                .post('/book/save', this.formData)
                .then((response) => {
                    if (response.data.errors) {
                        callback(response.data, null);
                        return;
                    }

                    callback(null, response.data);
                })
                .catch((error) => {
                    callback(error.response.data, null);
                });
        },

        saveBook() {
            this.loading = true;

            this.saveAction((error, data) => {
                if (error) {
                    this.errors = error.errors;
                    this.loading = false;
                    return;
                }

                this.book = data;
                this.edit = false;
                this.errors = '';

                this.loading = false;
            });
        },

        saveContract(error, transactionHash) {
            this.loading = true;

            if (error) {
                this.logError('Book/Create::saveContract', error);
                return;
            }

            axios.post('/book/saveContract', {
                id: this.book.id,
                contract_transaction: transactionHash
            })
                .then((response) => {
                    this.book = response.data.book;
                    window.location = response.data.redirect;
                })
                .catch((error) => {
                    this.errors = error.response.data.errors;
                    this.loading = false;
                });
        },

        makePublic() {
            this.loading = true;
            axios
                .post('/book/update-status', {
                    id: this.book.id,
                    status: 2
                })
                .then((response) => {
                    this.book = response.data;
                })
                .catch((error) => {
                    this.errors = error.response.data.errors;
                });
            this.loading = false;
        },

        startCrowdSale() {
            if (this.isKycPassed) {
                this.loading = true;

                this.saveAction((error, data) => {
                    if (error) {
                        this.errors = error.errors;
                        this.loading = false;
                        return;
                    }

                    this.edit = false;
                    this.errors = '';
                    this.book = data;

                    BlockchainManager.callAuthorized(
                        ReadManager,
                        'createInstance',
                        CONFIG.deployer,
                        CONFIG.deployerPassword,
                        this.saveContract,
                        CONFIG.pblContract,
                        this.author.wallet_address,
                        this.author.name,
                        this.book.short_description,
                        this.book.title,
                        this.convertToPbl(this.book.price_for_crowdsale) * (10 ** 18),
                        this.book.price_after_crowdsale,
                        this.book.goal,
                        this.book.soft_cap,
                        this.crowdSaleTokensAmount,
                        moment(this.book.crowdsale_start_date).add(this.book.duration, 'days').utc().format('X'),
                        this.book.aftersale_keys_amount,
                        this.tokenSymbol,
                    );
                })
            } else {
                $('#kyc_modal').modal('show');
            }
        },

        kycPassed() {
            this.isKycPassed = true;
            this.startCrowdSale();
        },

        str_slug(str) {
            str = str.replace(/^\s+|\s+$/g, ''); // trim
            str = str.toLowerCase();

            // remove accents, swap ñ for n, etc
            const from = 'àáäâèéëêìíïîòóöôùúüûñç·/_,:;';
            const to = 'aaaaeeeeiiiioooouuuunc------';
            for (let i = 0, l = from.length; i < l; i++) {
                str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
            }

            str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
                .replace(/\s+/g, '-') // collapse whitespace and replace by -
                .replace(/-+/g, '-'); // collapse dashes

            return str;
        }
    },

    computed: {
        bookUrl() {
            if (this.book.title !== null && this.book.title.trim().length > 0) {
                return `https://publica.io/${this.str_slug(this.book.title)}`;
            }

            return 'https://publica.io/';
        },

        crowdSaleTokensAmount() {
            const tokens = this.book.goal / this.book.price_for_crowdsale;
            return isNaN(tokens) ? 0 : tokens;
        },

        tokenSymbol() {
            let symbol = '';

            if (this.book.title) {
                symbol += 'READ_';
                this.book.title.split(' ').forEach(item => symbol += item.length > 0 ? item.slice(0, 2) : '');
            }

            return symbol.toUpperCase();
        },

        crowdSaleStartDate() {
            if (this.edit) {
                return moment(this.book.crowdsale_start_date).format('YYYY-MM-DD');
            }

            return null;
        }
    },

    created() {
        this.book = Object.assign(this.book, this.initial_book);
    }
};
</script>
