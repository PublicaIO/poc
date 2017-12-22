<template>
    <div class="modal fade" id="kyc_modal" tabindex="-1" role="dialog">
        <pbl-progress v-if="loading"></pbl-progress>

        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Please prove your identity by uploading requested documents</h4>
                </div>

                <div class="modal-body">
                    <form action="#" method="post" enctype="multipart/form-data" class="form-horizontal">
                        <div class="form-group">
                            <label for="title" class="col-md-4 control-label">Passport Photo</label>
                            <div class="col-md-6">
                                <input type="file" name="passport" @change="processFileField($event, 'passport')">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="title" class="col-md-4 control-label">Utility Bill Photo</label>
                            <div class="col-md-6">
                                <input type="file" name="utility_bill" @change="processFileField($event, 'utility_bill')">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="title" class="col-md-4 control-label">Selfie With Passport</label>
                            <div class="col-md-6">
                                <input type="file" name="selfie_with_passport" @change="processFileField($event, 'selfie_with_passport')">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click.prevent="submit">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
export default {
    data() {
        return {
            passport: null,
            utility: null,
            selfie: null,
            formData: new FormData(),
            loading: null,
        }
    },

    methods: {
        submit() {
            this.loading = true;

            axios
                .post('/kyc', this.formData)
                .then((response) => {
                    if (response.data.success) {
                        this.loading = false;
                        this.$emit('success');
                        $('#kyc_modal').modal('hide');
                    }
                })
                .catch((error) => {
                    this.loading = false;
                    this.logError('Kyc::submit', error);
                });
        },

        processFileField(event, fieldName) {
            this.formData.set(fieldName, event.target.files[0], event.target.files[0].name);
        }
    }
}
</script>
