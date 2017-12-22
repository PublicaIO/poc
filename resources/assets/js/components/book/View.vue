<template>
    <div class="book-view-wrapper">
        <pbl-progress v-if="loading"></pbl-progress>
        <pbl-book-buy :book="book"></pbl-book-buy>

        <header class="book-heading">
            <h4>
                The next book by {{ author.name }} {{ author.surname }}
            </h4>

            <h1 class="book-title">
                {{ book.title }}
            </h1>

            <h2 class="book-promotion">
                {{ book.promotion_text }}
            </h2>

            <div class="book-sale-date">
                <div class="book-sale-dates-list">
                    <div class="date-block" v-for="(date, index) in newDate" :key="index">
                        {{ date.value }}
                        <p>{{ date.title }}</p>
                    </div>
                </div>

                <p class="book-sale-start-date" v-if="isCrowdSaleStarted">
                    Crowdfunding ends on {{ crowdSaleEndDate }}.
                </p>

                <p class="book-sale-start-date" v-else>
                    Crowdfunding starts on {{ crowdSaleStartDate }}.
                </p>

                <h3 class="book-contrib-discount">
                    Only {{ formatNumber(totalToken) }} first copies have {{ formatNumber(crowdSaleDiscount) }}% early contribitor discount!
                </h3>

                <div class="sign-up" v-if="!isCrowdSaleStarted">
                    <pbl-ui-form-field id="signup" :init-value="signupMail" title="E-Mail" type="mail" @changed="signupMail = arguments[0]"></pbl-ui-form-field>
                    <button class="button button-active-action">Sign Up</button>
                </div>

                <p v-if="isCrowdSaleStarted">
                    <button class="button button-success button-large" @click="openBuyModal">
                        Contribute now with {{ formatNumber(crowdSaleDiscount) }}% Discount
                    </button>
                </p>
            </div>
        </header>

        <main class="book-body">
            <section class="about-book">
                <header class="book-section-header">
                    <h2>About the book</h2>
                </header>

                <div class="book-section-body">
                    <div class="book-about">
                        <div class="book-cover">
                            <img :src="book.cover_art_url" :alt="book.title">
                            <button class="button button-active-action">Read First Chapter</button>
                        </div>

                        <div class="book-about-body">
                            {{ book.short_description }}
                        </div>
                    </div>
                </div>
            </section>

            <section class="about-author">
                <header class="book-section-header">
                    <h2>About the author</h2>
                </header>

                <div class="book-section-body">
                    <p>"We just decided to." Furtunately, Anton, Yuri and the rest of the Publica team have more than enough of the qualifications I lack.</p>
                    <p>
                        I found Satoshi Nakamoto's paper in 2013. It reminded me of that Newsroom episode so I watched it again.
                        I couldn't find a way to fit both of their ideas into my own career in broadcast technology, but I knew that
                        digital trust would offer a better future to human communication. I found that fit in publishing. In the sprint of 2017 in Lillehammer
                        Norway I wrote a poem to myself about why I should do something about it.
                    </p>
                </div>

                <div class="about-author-info">
                    <div class="about-author-info-pic">
                        <img :src="book.cover_art_url" :alt="book.title">
                        <p>
                            Josef Marc<br>
                            Publica CEO
                        </p>
                    </div>

                    <div class="about-author-info-body">
                        <p>Fan club on <a href="#">Facebook</a></p>
                        <p>Follow Self Publishing 3.0 on <a href="#">Twitter</a></p>
                        <p>Join our vivid chat on <a href="#">Telegram</a></p>
                        <p><a href="#">Amazon store</a> and <a href="#">Book Reviews</a></p>
                    </div>
                </div>
            </section>

            <section class="book-crowdfunding">
                <header class="book-section-header">
                    <h2>Crowdfunding</h2>
                </header>

                <div class="book-section-body">
                    <div id="graph"></div>
                    <p>Crowdfunding duration: {{ this.book.duration }} days</p>
                    <p>Soft cap: ${{ formatNumber(this.book.soft_cap) }}, maximum funding amount ${{ formatNumber(this.book.goal) }}.</p>
                </div>

                <div class="sign-up">
                    <pbl-ui-form-field id="signup" :init-value="signupMail" title="E-Mail" type="mail" @changed="signupMail = arguments[0]"></pbl-ui-form-field>
                    <button class="button button-active-action">Sign Up</button>
                </div>
            </section>
        </main>
    </div>
</template>

<script>
import moment from 'moment';
import momentDurationFormatSetup from 'moment-duration-format';
import Highcharts from 'highcharts';
import numeral from 'numeral';
import { ReadManager, PebbleManager } from 'root/utils/managers';
import statusConfig from 'root/config/statuses';
momentDurationFormatSetup(moment);

export default {
    props: [
        'initial_book',
        'author',
        'auth_user'
    ],

    data() {
        return {
            status: statusConfig,
            signupMail: null,
            book: this.initial_book,
            edit: this.new_entry,
            errors: '',
            loading: false,
            download_link: {
                url: '',
                expiry: 0
            },
            newDate: [
                {
                    title: 'days',
                    value: 0,
                },
                {
                    title: 'hours',
                    value: 0,
                },
                {
                    title: 'mins',
                    value: 0,
                },
                {
                    title: 'secs',
                    value: 0,
                }
            ]
        };
    },

    computed: {
        isCrowdSaleStarted() {
            return this.book.status === status.CROWDSALE_STARTED;
        },

        crowdSaleEndDate() {
            return moment(this.book.crowdsale_start_date).add(this.book.duration, 'd').format('MMM Do, YYYY');
        },

        crowdSaleStartDate() {
            return moment(this.book.crowdsale_start_date).format('MMM Do, YYYY');
        },

        crowdSaleDiscount() {
            return 100 - this.book.price_for_crowdsale / (this.book.price_after_crowdsale / 100);
        },

        totalToken() {
            const tokens = this.book.goal / this.book.price_for_crowdsale;
            return isNaN(tokens) ? 0 : tokens;
        },

        pieChartData() {
            const onePercent = ((this.totalToken + this.book.aftersale_keys_amount) / 100);

            const percents = [
                this.totalToken / onePercent,
                this.book.aftersale_keys_amount / onePercent
            ];

            const data = [
                {
                    name: `${this.formatNumber(this.totalToken)} copies at ${this.formatNumber(this.book.price_for_crowdsale)}$ with ${this.formatNumber(this.crowdSaleDiscount)}% discount`,
                    y: percents[0],
                    color: '#A6D53D'
                },
                {
                    name: `${this.formatNumber(this.book.aftersale_keys_amount)} copies at ${this.formatNumber(this.book.price_after_crowdsale)}$`,
                    y: percents[1],
                    sliced: true,
                    selected: false,
                    color: '#065AAE'
                }
            ];

            return data;
        }
    },

    methods: {
        getBalance() {
            const readManager = new ReadManager(this.book.contract_address);

            readManager.instance.methods.balanceOf(this.auth_user.wallet_address).call((error, balance) => {
                if (error) {
                    this.logError('ViewBook::getBalance', error);
                    return;
                }

                this.log(balance);
            });
        },

        convertToPbl(value) {
            if (value && !isNaN(value)) {
                return parseInt(value) / 0.2;
            }

            return 0;
        },

        openBuyModal() {
            if (typeof this.auth_user.id !== 'undefined') {
                $('#buyModal').modal('show');
            } else {
                window.location = `/login/reader?book=${this.book.id}`;
            }
        },

        formatNumber(number) {
            return numeral(number).format('0,0');
        },

        parseMillisecondsIntoReadableTime() {
            const milliseconds = this.getMsDifference();

            // Get days from milliseconds
            const days = milliseconds / (1000 * 60 * 60 * 24);
            const absoluteDays = Math.floor(days);
            const d = absoluteDays > 9 ? absoluteDays : `0${absoluteDays}`;

            // Get hours from milliseconds
            const hours = (days - absoluteDays) * 24;
            const absoluteHours = Math.floor(hours);
            const h = absoluteHours > 9 ? absoluteHours : `0${absoluteHours}`;

            // Get remainder from hours and convert to minutes
            const minutes = (hours - absoluteHours) * 60;
            const absoluteMinutes = Math.floor(minutes);
            const m = absoluteMinutes > 9 ? absoluteMinutes : `0${absoluteMinutes}`;

            // Get remainder from minutes and convert to seconds
            const seconds = (minutes - absoluteMinutes) * 60;
            const absoluteSeconds = Math.floor(seconds);
            const s = absoluteSeconds > 9 ? absoluteSeconds : `0${absoluteSeconds}`;

            this.newDate = [
                {
                    title: 'days',
                    value: d,
                },
                {
                    title: 'hours',
                    value: h,
                },
                {
                    title: 'mins',
                    value: m,
                },
                {
                    title: 'secs',
                    value: s,
                }
            ];
        },

        getMsDifference() {
            const today = moment(moment().utc().format('YYYY-MM-DD hh:mm:ss'));
            const startDate = moment(this.book.crowdsale_start_date);

            if (today.isBefore(startDate) && this.book.status < status.CROWDSALE_STARTED) {
                const diff = moment(this.book.crowdsale_start_date).diff(today, 'milliseconds');
                return moment.duration(diff, 'milliseconds');
            } else if (moment(this.book.crowdsale_start_date).add(this.book.duration, 'd').isBefore(moment())) {
                return 0;
            }

            const diff = moment(this.book.crowdsale_start_date).add(this.book.duration, 'd').diff(today, 'milliseconds');
            return moment.duration(diff, 'milliseconds');
        },

        checkBookContractData(callback = () => {}) {
            if (!this.book.id || !this.book.contract_transaction) return;

            if (this.book.contract_address) {
                callback(null, true);
                return;
            }

            this.loading = true;

            BlockchainManager.getTransactionReceipt(this.book.contract_transaction, (error, receipt) => {
                if (error) {
                    this.loading = false;
                    callback(error);
                    return;
                }

                if (receipt && receipt.contractAddress) {
                    axios.post('/book/saveContract', {
                        id: this.book.id,
                        contract_address: receipt.contractAddress
                    })
                        .then((response) => {
                            this.loading = false;
                            this.book = response.data;
                            callback(null, true);
                        })
                        .catch((error) => {
                            this.errors = error.response.data.errors;
                            this.loading = false;
                            callback(error, null);
                        });
                } else {
                    this.loading = false;
                    callback();
                }
            });
        }
    },

    mounted() {
        setInterval(() => {
            this.parseMillisecondsIntoReadableTime();
        }, 1000);

        Highcharts.chart('graph', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            credits: {
                enabled: false,
            },
            title: {
                text: null
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>',
                        style: {
                            color:
                (Highcharts.theme && Highcharts.theme.contrastTextColor) ||
                'black'
                        }
                    }
                }
            },
            series: [
                {
                    name: 'Copies',
                    colorByPoint: true,
                    size: '80%',
                    innerSize: '60%',
                    data: this.pieChartData
                }
            ]
        });

        const expiration = new Date((new Date()).getTime() + (15 * 3600 * 24 * 1000));
        document.cookie = `last_book_viewed=${this.book.id}; expires=${expiration}; path=/`;

        this.checkBookContractData((error, result) => {
            if (error) {
                this.logError('Book/View::checkBookContractData', error);
                return;
            }

            if (result) this.getBalance();
        });
    }
};
</script>
