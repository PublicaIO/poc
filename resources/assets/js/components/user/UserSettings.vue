<template>
    <div class="row">
        <div class="col-md-12">
            <template v-if="!edit">
                <button type="button" @click.prevent="edit = true" class="btn btn-primary">Edit</button>
            </template>

            <template v-else>
                <button type="button" @click.prevent="submit" class="btn btn-primary">Save</button>
                <button type="button" @click.prevent="edit = false" class="btn btn-danger">Cancel</button>
            </template>
        </div>

        <div class="col-md-12 form-horizontal">
            <div class="form-group">
                <label for="name" class="col-md-4 control-label">Full Name</label>
                <div v-if="!edit" id="name" class="col-md-6">{{ user.name }}</div>
                <template v-else>
                    <div class="col-md-6">
                        <input type="text" name="name" class="form-control" v-model="user.name" id="name">
                        <form-error v-if="errors.name" :errors="errors">
                            {{ errors.name[0] }}
                        </form-error>
                    </div>
                </template>
            </div>
        </div>
        <div class="col-md-12 form-horizontal">
            <div class="form-group">
                <label for="username" class="col-md-4 control-label">Username</label>
                <div v-if="!edit" id="username" class="col-md-6">{{ user.username }}</div>
                <template v-else>
                    <div class="col-md-6">
                        <input type="text" name="username" class="form-control" v-model="user.username"
                               id="username">
                        <form-error v-if="errors.username" :errors="errors">
                            {{ errors.username[0] }}
                        </form-error>
                    </div>
                </template>
            </div>
        </div>
        <div class="col-md-12 form-horizontal">
            <div class="form-group">
                <label for="email" class="col-md-4 control-label">Email</label>
                <div v-if="!edit" id="email" class="col-md-6">{{ user.email }}</div>
                <template v-else>
                    <div class="col-md-6">
                        <input type="text" name="email" class="form-control" v-model="user.email" id="email">
                        <form-error v-if="errors.email" :errors="errors">
                            {{ errors.email[0] }}
                        </form-error>
                    </div>
                </template>
            </div>
        </div>
        <div class="col-md-12 form-horizontal">
            <div class="form-group">
                <label for="pbl_balance" class="col-md-4 control-label">PBL Balance</label>
                <div id="pbl_balance" class="col-md-6">{{ `${pblBalance}` | convertFromWei | formatNumber }}</div>
            </div>
        </div>
    </div>
</template>

<script>
import { PebbleManager } from 'root/utils/managers';

export default {
    props: ['user'],

    data() {
        return {
            edit: false,
            errors: [],
            pblBalance: 0
        }
    },

    methods: {
        submit() {
            axios.patch(`user/${this.user.id}`, {
                name: this.user.name,
                username: this.user.username,
                email: this.user.email
            })
                .then((response) => {
                    this.edit = false;

                    if (response.data.redirect) {
                        window.location = response.data.redirect;
                    }
                })
                .catch((e) => {
                    this.errors = e.response.data.errors;
                });
        },

        getBalance() {
            const pblContract = new PebbleManager();

            pblContract.balanceOf(this.user.wallet_address, (error, balance) => {
                if (error) {
                    this.logError('UserSettings::getBalance', error);
                    return;
                }

                this.pblBalance = balance;
            });
        }
    },

    mounted() {
        this.getBalance();
    }
}
</script>
